<?php
/**
 * Communication Controller
 */

class CommunicationController extends Controller {
    /**
     * Communications list
     */
    public function index() {
        $page = (int) ($this->input('page') ?? 1);
        
        // Get total
        $this->db->query("SELECT COUNT(*) as total FROM messages");
        $total = $this->db->first()->total;
        
        // Get messages
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $this->db->query(
            "SELECT m.*, mt.name as template_name
            FROM messages m
            LEFT JOIN email_templates mt ON m.template_id = mt.id
            ORDER BY m.created_at DESC
            LIMIT " . ITEMS_PER_PAGE . " OFFSET {$offset}"
        );
        
        $this->data['messages'] = $this->db->results();
        $this->data['pagination'] = paginate($total, ITEMS_PER_PAGE, $page);
        
        // Get statistics
        $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM messages");
        $this->data['stats'] = $this->db->first();
        
        $this->view('communications.index');
    }

    /**
     * Announcements list
     */
    public function announcements() {
        $page = (int) ($this->input('page') ?? 1);
        
        // Get total
        $this->db->query("SELECT COUNT(*) as total FROM announcements");
        $total = $this->db->first()->total;
        
        // Get announcements
        $offset = ($page - 1) * ITEMS_PER_PAGE;
        $this->db->query(
            "SELECT a.*, u.first_name as created_by_name
            FROM announcements a
            LEFT JOIN users u ON a.created_by = u.id
            ORDER BY a.created_at DESC
            LIMIT " . ITEMS_PER_PAGE . " OFFSET {$offset}"
        );
        
        $this->data['announcements'] = $this->db->results();
        $this->data['pagination'] = paginate($total, ITEMS_PER_PAGE, $page);
        
        $this->view('communications.announcements');
    }

    /**
     * Create announcement
     */
    public function createAnnouncement() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/communications/announcements');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/communications/announcements');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        $data = [
            'title' => $this->input('title'),
            'content' => $this->input('content'),
            'priority' => $this->input('priority') ?: 'normal',
            'target_audience' => $this->input('target_audience') ?: 'all',
            'valid_from' => $this->input('valid_from') ?: date('Y-m-d H:i:s'),
            'valid_until' => $this->input('valid_until') ?: null,
            'is_published' => $this->input('is_published') ? 1 : 0,
            'created_by' => $this->user()->id
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'title' => 'required|min:3|max:200',
            'content' => 'required|min:10'
        ]);
        
        if (!$validator->validate()) {
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect('/communications/announcements');
        }
        
        if ($id) {
            $this->db->query(
                "UPDATE announcements SET title = ?, content = ?, priority = ?, 
                target_audience = ?, valid_from = ?, valid_until = ?, is_published = ? 
                WHERE id = ?",
                array_merge(array_values($data), [$id])
            );
            $this->flash('success', 'Announcement updated successfully');
        } else {
            $this->db->query(
                "INSERT INTO announcements (title, content, priority, target_audience, 
                valid_from, valid_until, is_published, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                array_values($data)
            );
            $this->flash('success', 'Announcement created successfully');
        }
        
        $this->redirect('/communications/announcements');
    }

    /**
     * Messages page
     */
    public function messages() {
        $this->index();
    }

    /**
     * Send message
     */
    public function send() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/communications');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/communications');
        }
        
        $type = $this->input('type', 'email');
        $recipientId = $this->input('recipient_id') ?: null;
        $recipientEmail = $this->input('recipient_email');
        $recipientPhone = $this->input('recipient_phone');
        $subject = $this->input('subject');
        $body = $this->input('body');
        $templateId = $this->input('template_id') ?: null;
        
        // Validate
        if (!$body || (!$recipientId && !$recipientEmail && !$recipientPhone)) {
            $this->flash('error', 'Please provide recipient and message');
            $this->redirect('/communications');
        }
        
        $this->db->query(
            "INSERT INTO messages (type, recipient_id, recipient_email, recipient_phone, 
            subject, body, template_id, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')",
            [$type, $recipientId, $recipientEmail, $recipientPhone, $subject, $body, $templateId]
        );
        
        // In production, this would trigger actual email/SMS sending
        $this->db->query(
            "UPDATE messages SET status = 'sent', sent_at = NOW() WHERE id = ?",
            [$this->db->lastInsertId()]
        );
        
        $this->flash('success', 'Message sent successfully');
        $this->redirect('/communications');
    }

    /**
     * Delete announcement
     */
    public function deleteAnnouncement() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/communications/announcements');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid announcement ID');
            $this->redirect('/communications/announcements');
        }
        
        $this->db->query("DELETE FROM announcements WHERE id = ?", [$id]);
        
        $this->flash('success', 'Announcement deleted successfully');
        $this->redirect('/communications/announcements');
    }

    /**
     * Email templates
     */
    public function templates() {
        $this->db->query("SELECT * FROM email_templates ORDER BY name");
        $this->data['templates'] = $this->db->results();
        
        $this->view('communications.templates');
    }

    /**
     * Save template
     */
    public function saveTemplate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/communications/templates');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/communications/templates');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        $data = [
            'name' => $this->input('name'),
            'subject' => $this->input('subject'),
            'body' => $this->input('body')
        ];
        
        if ($id) {
            $this->db->query(
                "UPDATE email_templates SET name = ?, subject = ?, body = ? WHERE id = ?",
                array_merge(array_values($data), [$id])
            );
            $this->flash('success', 'Template updated successfully');
        } else {
            $this->db->query(
                "INSERT INTO email_templates (name, subject, body) VALUES (?, ?, ?)",
                array_values($data)
            );
            $this->flash('success', 'Template created successfully');
        }
        
        $this->redirect('/communications/templates');
    }
}
