<?php
/**
 * Member Controller
 */

class MemberController extends Controller {
    /**
     * Members list
     */
    public function index() {
        $page = (int) ($this->input('page') ?? 1);
        $search = $this->input('search', '');
        $status = $this->input('status', '');
        
        $member = $this->model('Member');
        
        // Build where conditions
        $where = [];
        if ($status) {
            $where['membership_status'] = $status;
        }
        
        // Search
        if ($search) {
            $this->data['members'] = $member->search($search);
            $this->data['search'] = $search;
        } else {
            $result = $member->paginate($page, ITEMS_PER_PAGE, $where, ['last_name', 'ASC']);
            $this->data['members'] = $result['data'];
            $this->data['pagination'] = paginate($result['total'], ITEMS_PER_PAGE, $page);
        }
        
        $this->data['status'] = $status;
        $this->data['statuses'] = ['visitor', 'member', 'inactive', 'transferred'];
        
        $this->view('members.index');
    }

    /**
     * Add member page
     */
    public function add() {
        $this->data['page_title'] = 'Add Member';
        
        // Get families for dropdown
        $family = $this->model('Family');
        $this->data['families'] = $family->all(['family_name', 'ASC']);
        
        $this->view('members.form');
    }

    /**
     * Edit member page
     */
    public function edit() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/members');
        }
        
        $member = $this->model('Member');
        $memberData = $member->find($id);
        
        if (!$memberData) {
            $this->flash('error', 'Member not found');
            $this->redirect('/members');
        }
        
        $this->data['member'] = $memberData;
        $this->data['page_title'] = 'Edit Member';
        
        // Get families for dropdown
        $family = $this->model('Family');
        $this->data['families'] = $family->all(['family_name', 'ASC']);
        
        $this->view('members.form');
    }

    /**
     * View member
     */
    public function viewMember() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/members');
        }
        
        $member = $this->model('Member');
        $memberData = $member->find($id);
        
        if (!$memberData) {
            $this->flash('error', 'Member not found');
            $this->redirect('/members');
        }
        
        $this->data['member'] = $memberData;
        
        // Get related data
        $this->data['groups'] = $member->groups();
        $this->data['recentAttendance'] = $member->attendance(5);
        $this->data['recentDonations'] = $member->donations(5);
        $this->data['totalDonations'] = $member->getTotalDonations();
        
        $this->view('members.view');
    }

    /**
     * Save member
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/members');
        }
        
        // Verify CSRF
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/members/add');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        $data = [
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'date_of_birth' => $this->input('date_of_birth') ?: null,
            'gender' => $this->input('gender'),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'state' => $this->input('state'),
            'postal_code' => $this->input('postal_code'),
            'country' => $this->input('country') ?: 'Zimbabwe',
            'marital_status' => $this->input('marital_status'),
            'wedding_date' => $this->input('wedding_date') ?: null,
            'membership_date' => $this->input('membership_date') ?: date('Y-m-d'),
            'membership_status' => $this->input('membership_status') ?: 'member',
            'membership_type' => $this->input('membership_type') ?: 'regular',
            'baptized' => $this->input('baptized') ? 1 : 0,
            'baptized_date' => $this->input('baptized_date') ?: null,
            'notes' => $this->input('notes'),
            'emergency_contact_name' => $this->input('emergency_contact_name'),
            'emergency_contact_phone' => $this->input('emergency_contact_phone')
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'first_name' => 'required|min:2|max:50',
            'last_name' => 'required|min:2|max:50',
            'email' => 'email',
            'membership_status' => 'required'
        ]);
        
        if (!$validator->validate()) {
            saveOld($data);
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect($id ? '/members/edit?id=' . $id : '/members/add');
        }
        
        $member = $this->model('Member');
        
        if ($id) {
            // Update
            $member->update($id, $data);
            $this->flash('success', 'Member updated successfully');
        } else {
            // Create
            $member->create($data);
            $this->flash('success', 'Member added successfully');
        }
        
        $this->redirect('/members');
    }

    /**
     * Delete member
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/members');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid member ID');
            $this->redirect('/members');
        }
        
        $member = $this->model('Member');
        $member->delete($id);
        
        $this->flash('success', 'Member deleted successfully');
        $this->redirect('/members');
    }

    /**
     * Search members
     */
    public function search() {
        $term = $this->input('term', '');
        
        if (strlen($term) < 2) {
            $this->json([]);
        }
        
        $member = $this->model('Member');
        $results = $member->search($term);
        
        $this->json($results);
    }

    /**
     * Get member statistics
     */
    public function statistics() {
        if (!isAjax()) {
            $this->redirect('/members');
        }
        
        $member = $this->model('Member');
        $stats = $member->getStatistics();
        
        $this->json($stats);
    }
}
