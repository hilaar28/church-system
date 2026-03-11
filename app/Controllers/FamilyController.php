<?php
/**
 * Family Controller
 */

class FamilyController extends Controller {
    /**
     * Families list
     */
    public function index() {
        $page = (int) ($this->input('page') ?? 1);
        $search = $this->input('search', '');
        
        $family = $this->model('Family');
        
        if ($search) {
            $this->data['families'] = $family->search($search);
            $this->data['search'] = $search;
        } else {
            $result = $family->paginate($page, ITEMS_PER_PAGE, [], ['family_name', 'ASC']);
            $this->data['families'] = $result['data'];
            $this->data['pagination'] = paginate($result['total'], ITEMS_PER_PAGE, $page);
        }
        
        $this->view('families.index');
    }

    /**
     * Add family page
     */
    public function add() {
        $this->data['page_title'] = 'Add Family';
        
        // Get members for head of family dropdown
        $member = $this->model('Member');
        $this->data['members'] = $member->all(['last_name', 'ASC']);
        
        $this->view('families.form');
    }

    /**
     * Edit family page
     */
    public function edit() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/families');
        }
        
        $family = $this->model('Family');
        $familyData = $family->find($id);
        
        if (!$familyData) {
            $this->flash('error', 'Family not found');
            $this->redirect('/families');
        }
        
        $this->data['family'] = $familyData;
        $this->data['page_title'] = 'Edit Family';
        
        // Get members for dropdown
        $member = $this->model('Member');
        $this->data['members'] = $member->all(['last_name', 'ASC']);
        
        $this->view('families.form');
    }

    /**
     * View family
     */
    public function viewFamily() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/families');
        }
        
        $family = $this->model('Family');
        $familyData = $family->find($id);
        
        if (!$familyData) {
            $this->flash('error', 'Family not found');
            $this->redirect('/families');
        }
        
        $this->data['family'] = $familyData;
        $this->data['members'] = $family->members($id);
        
        $this->view('families.view');
    }

    /**
     * Save family
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/families');
        }
        
        // Verify CSRF
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/families/add');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        $data = [
            'family_name' => $this->input('family_name'),
            'address' => $this->input('address'),
            'city' => $this->input('city'),
            'state' => $this->input('state'),
            'postal_code' => $this->input('postal_code'),
            'phone' => $this->input('phone'),
            'head_of_family_id' => $this->input('head_of_family_id') ?: null
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'family_name' => 'required|min:2|max:100'
        ]);
        
        if (!$validator->validate()) {
            saveOld($data);
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect($id ? '/families/edit?id=' . $id : '/families/add');
        }
        
        $family = $this->model('Family');
        
        if ($id) {
            $family->update($id, $data);
            $this->flash('success', 'Family updated successfully');
        } else {
            $family->create($data);
            $this->flash('success', 'Family added successfully');
        }
        
        $this->redirect('/families');
    }

    /**
     * Delete family
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/families');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid family ID');
            $this->redirect('/families');
        }
        
        $family = $this->model('Family');
        $family->delete($id);
        
        $this->flash('success', 'Family deleted successfully');
        $this->redirect('/families');
    }

    /**
     * Add member to family
     */
    public function addMember() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/families');
        }
        
        $familyId = (int) ($this->input('family_id') ?? 0);
        $memberId = (int) ($this->input('member_id') ?? 0);
        $relationship = $this->input('relationship', 'other');
        
        if (!$familyId || !$memberId) {
            $this->flash('error', 'Invalid family or member');
            $this->redirect('/families/view?id=' . $familyId);
        }
        
        $family = $this->model('Family');
        $family->addMember($familyId, $memberId, $relationship);
        
        $this->flash('success', 'Member added to family');
        $this->redirect('/families/view?id=' . $familyId);
    }

    /**
     * Remove member from family
     */
    public function removeMember() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/families');
        }
        
        $familyId = (int) ($this->input('family_id') ?? 0);
        $memberId = (int) ($this->input('member_id') ?? 0);
        
        if (!$familyId || !$memberId) {
            $this->flash('error', 'Invalid family or member');
            $this->redirect('/families');
        }
        
        $family = $this->model('Family');
        $family->removeMember($familyId, $memberId);
        
        $this->flash('success', 'Member removed from family');
        $this->redirect('/families/view?id=' . $familyId);
    }
}
