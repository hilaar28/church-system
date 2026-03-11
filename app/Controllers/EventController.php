<?php
/**
 * Event Controller
 */

class EventController extends Controller {
    /**
     * Events list
     */
    public function index() {
        $page = (int) ($this->input('page') ?? 1);
        $type = $this->input('type', '');
        
        $where = "is_active = 1";
        $params = [];
        
        if ($type) {
            $where .= " AND event_type = ?";
            $params[] = $type;
        }
        
        // Get total
        $this->db->query("SELECT COUNT(*) as total FROM events WHERE {$where}", $params);
        $total = $this->db->first()->total;
        
        // Get events
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $this->db->query(
            "SELECT e.*, 
            (SELECT COUNT(*) FROM event_registrations WHERE event_id = e.id) as registrations
            FROM events e
            WHERE {$where}
            ORDER BY e.start_datetime DESC
            LIMIT " . ITEMS_PER_PAGE . " OFFSET {$offset}",
            $params
        );
        
        $this->data['events'] = $this->db->results();
        $this->data['pagination'] = paginate($total, ITEMS_PER_PAGE, $page);
        $this->data['type'] = $type;
        
        $this->view('events.index');
    }

    /**
     * Add event page
     */
    public function add() {
        $this->data['page_title'] = 'Add Event';
        $this->view('events.form');
    }

    /**
     * Save event
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/events');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/events/add');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        $data = [
            'title' => $this->input('title'),
            'description' => $this->input('description'),
            'event_type' => $this->input('event_type') ?: 'other',
            'start_datetime' => $this->input('start_datetime'),
            'end_datetime' => $this->input('end_datetime'),
            'location' => $this->input('location'),
            'is_all_day' => $this->input('is_all_day') ? 1 : 0,
            'is_recurring' => $this->input('is_recurring') ? 1 : 0,
            'recurrence_pattern' => $this->input('recurrence_pattern'),
            'max_attendees' => $this->input('max_attendees') ?: null,
            'registration_required' => $this->input('registration_required') ? 1 : 0,
            'registration_deadline' => $this->input('registration_deadline') ?: null,
            'organizer_id' => $this->user()->id,
            'is_active' => 1
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'title' => 'required|min:3|max:200',
            'start_datetime' => 'required'
        ]);
        
        if (!$validator->validate()) {
            saveOld($data);
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect($id ? '/events/edit?id=' . $id : '/events/add');
        }
        
        if ($id) {
            $this->db->query(
                "UPDATE events SET title = ?, description = ?, event_type = ?, start_datetime = ?, 
                end_datetime = ?, location = ?, is_all_day = ?, is_recurring = ?, 
                recurrence_pattern = ?, max_attendees = ?, registration_required = ?, 
                registration_deadline = ? WHERE id = ?",
                array_merge(array_values($data), [$id])
            );
            $this->flash('success', 'Event updated successfully');
        } else {
            $this->db->query(
                "INSERT INTO events (title, description, event_type, start_datetime, end_datetime, 
                location, is_all_day, is_recurring, recurrence_pattern, max_attendees, 
                registration_required, registration_deadline, organizer_id, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                array_values($data)
            );
            $this->flash('success', 'Event added successfully');
        }
        
        $this->redirect('/events');
    }

    /**
     * Edit event page
     */
    public function edit() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/events');
        }
        
        $this->db->query("SELECT * FROM events WHERE id = ?", [$id]);
        $event = $this->db->first();
        
        if (!$event) {
            $this->flash('error', 'Event not found');
            $this->redirect('/events');
        }
        
        $this->data['event'] = $event;
        $this->data['page_title'] = 'Edit Event';
        
        $this->view('events.form');
    }

    /**
     * View event
     */
    public function viewEvent() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/events');
        }
        
        $this->db->query("SELECT * FROM events WHERE id = ?", [$id]);
        $event = $this->db->first();
        
        if (!$event) {
            $this->flash('error', 'Event not found');
            $this->redirect('/events');
        }
        
        $this->data['event'] = $event;
        
        // Get registrations
        $this->db->query(
            "SELECT er.*, m.first_name, m.last_name 
            FROM event_registrations er
            LEFT JOIN members m ON er.member_id = m.id
            WHERE er.event_id = ?
            ORDER BY er.registered_at DESC",
            [$id]
        );
        $this->data['registrations'] = $this->db->results();
        
        $this->view('events.view');
    }

    /**
     * Register for event
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/events');
        }
        
        $eventId = (int) ($this->input('event_id') ?? 0);
        $memberId = (int) ($this->input('member_id') ?? 0);
        $guestName = $this->input('guest_name');
        $guestEmail = $this->input('guest_email');
        $guestsCount = (int) ($this->input('guests_count') ?? 1);
        
        if (!$eventId) {
            $this->flash('error', 'Invalid event');
            $this->redirect('/events');
        }
        
        // Check if already registered
        $this->db->query(
            "SELECT * FROM event_registrations WHERE event_id = ? AND member_id = ?",
            [$eventId, $memberId]
        );
        if ($this->db->first()) {
            $this->flash('error', 'Already registered for this event');
            $this->redirect('/events/view?id=' . $eventId);
        }
        
        $this->db->query(
            "INSERT INTO event_registrations (event_id, member_id, guest_name, guest_email, guests_count) 
            VALUES (?, ?, ?, ?, ?)",
            [$eventId, $memberId, $guestName, $guestEmail, $guestsCount]
        );
        
        $this->flash('success', 'Successfully registered for event');
        $this->redirect('/events/view?id=' . $eventId);
    }

    /**
     * Delete event
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/events');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid event ID');
            $this->redirect('/events');
        }
        
        $this->db->query("UPDATE events SET is_active = 0 WHERE id = ?", [$id]);
        
        $this->flash('success', 'Event deleted successfully');
        $this->redirect('/events');
    }
}
