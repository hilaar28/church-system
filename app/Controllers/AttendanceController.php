<?php
/**
 * Attendance Controller
 */

class AttendanceController extends Controller {
    /**
     * Attendance list
     */
    public function index() {
        $page = (int) ($this->input('page') ?? 1);
        $date = $this->input('date', date('Y-m-d'));
        $serviceType = $this->input('service_type', '');
        
        // Get services
        $this->db->query(
            "SELECT s.*, 
            (SELECT COUNT(*) FROM attendance WHERE service_id = s.id) as attendance_count 
            FROM services s 
            WHERE 1=1
            ORDER BY s.service_date DESC, s.start_time DESC"
        );
        $services = $this->db->results();
        
        // Filter services
        $filteredServices = [];
        foreach ($services as $service) {
            if ($date && $service['service_date'] !== $date) continue;
            if ($serviceType && $service['service_type'] !== $serviceType) continue;
            $filteredServices[] = $service;
        }
        
        $this->data['services'] = $filteredServices;
        $this->data['date'] = $date;
        $this->data['service_type'] = $serviceType;
        
        // Get statistics
        $this->db->query("SELECT 
            COUNT(DISTINCT service_id) as total_services,
            COUNT(*) as total_attendance,
            SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN status = 'visitor' THEN 1 ELSE 0 END) as visitors
            FROM attendance");
        $this->data['stats'] = $this->db->first();
        
        $this->view('attendance.index');
    }

    /**
     * Record attendance page
     */
    public function record() {
        $serviceId = (int) ($this->input('service_id') ?? 0);
        
        // Get all members for attendance marking
        $member = $this->model('Member');
        $this->data['members'] = $member->all(['last_name', 'ASC']);
        
        // Get service info
        if ($serviceId) {
            $this->db->query("SELECT * FROM services WHERE id = ?", [$serviceId]);
            $this->data['service'] = $this->db->first();
            
            // Get existing attendance
            $this->db->query(
                "SELECT a.*, m.first_name, m.last_name 
                FROM attendance a 
                LEFT JOIN members m ON a.member_id = m.id 
                WHERE a.service_id = ?",
                [$serviceId]
            );
            $this->data['attendance'] = $this->db->results();
        }
        
        // Get upcoming/past services
        $this->db->query(
            "SELECT * FROM services WHERE service_date >= CURDATE() ORDER BY service_date ASC"
        );
        $this->data['upcomingServices'] = $this->db->results();
        
        $this->data['page_title'] = 'Record Attendance';
        $this->view('attendance.record');
    }

    /**
     * Save attendance
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/attendance');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/attendance/record');
        }
        
        $serviceId = (int) ($this->input('service_id') ?? 0);
        $memberIds = $this->input('member_id', []);
        $statuses = $this->input('status', []);
        $visitorNames = $this->input('visitor_name', []);
        $visitorContacts = $this->input('visitor_contact', []);
        $currentUserId = $this->user()->id;
        
        if (!$serviceId) {
            $this->flash('error', 'Invalid service');
            $this->redirect('/attendance/record');
        }
        
        // Delete existing attendance for this service
        $this->db->query("DELETE FROM attendance WHERE service_id = ?", [$serviceId]);
        
        // Insert new attendance records
        foreach ($memberIds as $memberId) {
            $status = $statuses[$memberId] ?? 'present';
            $visitorName = $visitorNames[$memberId] ?? null;
            $visitorContact = $visitorContacts[$memberId] ?? null;
            
            if ($status === 'visitor' && empty($visitorName)) {
                continue;
            }
            
            $this->db->query(
                "INSERT INTO attendance (service_id, member_id, status, visitor_name, visitor_contact, recorded_by) 
                VALUES (?, ?, ?, ?, ?, ?)",
                [$serviceId, $memberId, $status, $visitorName, $visitorContact, $currentUserId]
            );
        }
        
        $this->flash('success', 'Attendance saved successfully');
        $this->redirect('/attendance/record?service_id=' . $serviceId);
    }

    /**
     * Attendance report
     */
    public function report() {
        $startDate = $this->input('start_date', date('Y-m-01'));
        $endDate = $this->input('end_date', date('Y-m-t'));
        $serviceType = $this->input('service_type', '');
        
        $where = "s.service_date BETWEEN ? AND ?";
        $params = [$startDate, $endDate];
        
        if ($serviceType) {
            $where .= " AND s.service_type = ?";
            $params[] = $serviceType;
        }
        
        $this->db->query(
            "SELECT s.*, 
            COUNT(a.id) as total_attendance,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN a.status = 'visitor' THEN 1 ELSE 0 END) as visitors,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent
            FROM services s
            LEFT JOIN attendance a ON s.id = a.service_id
            WHERE {$where}
            GROUP BY s.id
            ORDER BY s.service_date DESC",
            $params
        );
        
        $this->data['services'] = $this->db->results();
        $this->data['start_date'] = $startDate;
        $this->data['end_date'] = $endDate;
        $this->data['service_type'] = $serviceType;
        
        $this->view('attendance.report');
    }

    /**
     * Add new service
     */
    public function addService() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/attendance');
        }
        
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/attendance');
        }
        
        $data = [
            'service_name' => $this->input('service_name'),
            'service_type' => $this->input('service_type') ?: 'sunday_service',
            'service_date' => $this->input('service_date'),
            'start_time' => $this->input('start_time'),
            'end_time' => $this->input('end_time'),
            'notes' => $this->input('notes')
        ];
        
        $this->db->query(
            "INSERT INTO services (service_name, service_type, service_date, start_time, end_time, notes) 
            VALUES (?, ?, ?, ?, ?, ?)",
            array_values($data)
        );
        
        $this->flash('success', 'Service added successfully');
        $this->redirect('/attendance/record?service_id=' . $this->db->lastInsertId());
    }
}
