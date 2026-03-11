<?php
/**
 * Group Controller
 */

class GroupController extends Controller {
    /**
     * Groups list
     */
    public function index() {
        $type = $this->input('type', '');
        
        $group = $this->model('Group');
        
        if ($type) {
            $groups = $group->getByType($type);
        } else {
            $groups = $group->getActive(['name', 'ASC']);
        }
        
        // Add member count to each group
        $groupsWithCounts = [];
        foreach ($groups as $g) {
            $g['member_count'] = $g->getMemberCount();
            $groupsWithCounts[] = $g;
        }
        
        $this->data['groups'] = $groupsWithCounts;
        $this->data['type'] = $type;
        $this->data['types'] = ['sunday_school', 'small_group', 'ministry_team', 'cell_group', 'Bible_study'];
        
        $this->view('groups.index');
    }

    /**
     * Add group page
     */
    public function add() {
        $this->data['page_title'] = 'Add Group';
        
        // Get potential leaders
        $member = $this->model('Member');
        $this->data['potential_leaders'] = $member->all(['last_name', 'ASC']);
        
        $this->view('groups.form');
    }

    /**
     * Edit group page
     */
    public function edit() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/groups');
        }
        
        $group = $this->model('Group');
        $groupData = $group->find($id);
        
        if (!$groupData) {
            $this->flash('error', 'Group not found');
            $this->redirect('/groups');
        }
        
        $this->data['group'] = $groupData;
        $this->data['page_title'] = 'Edit Group';
        
        // Get potential leaders
        $member = $this->model('Member');
        $this->data['potential_leaders'] = $member->all(['last_name', 'ASC']);
        
        $this->view('groups.form');
    }

    /**
     * View group
     */
    public function viewGroup() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/groups');
        }
        
        $group = $this->model('Group');
        $group = $group->find($id);  // Use the loaded group
        
        if (!$group) {
            $this->flash('error', 'Group not found');
            $this->redirect('/groups');
        }
        
        $this->data['group'] = $group;
        $this->data['members'] = $group->members();
        $this->data['member_count'] = $group->getMemberCount();
        
        $this->view('groups.view');
    }

    /**
     * Save group
     */
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/groups');
        }
        
        // Verify CSRF
        $csrfToken = $this->input('csrf_token');
        if (!verifyCsrfToken($csrfToken)) {
            $this->flash('error', 'Invalid security token');
            $this->redirect('/groups/add');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        $data = [
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'group_type' => $this->input('group_type') ?: 'small_group',
            'meeting_day' => $this->input('meeting_day'),
            'meeting_time' => $this->input('meeting_time') ?: null,
            'meeting_location' => $this->input('meeting_location'),
            'leader_id' => $this->input('leader_id') ?: null,
            'capacity' => $this->input('capacity') ?: null,
            'is_active' => $this->input('is_active') ? 1 : 0
        ];
        
        // Validate
        $validator = new Validator($data);
        $validator->rules([
            'name' => 'required|min:2|max:100',
            'group_type' => 'required'
        ]);
        
        if (!$validator->validate()) {
            saveOld($data);
            $this->flash('error', implode(', ', $validator->getFlatErrors()));
            $this->redirect($id ? '/groups/edit?id=' . $id : '/groups/add');
        }
        
        $group = $this->model('Group');
        
        if ($id) {
            // Update
            $group->update($id, $data);
            $this->flash('success', 'Group updated successfully');
        } else {
            // Create
            $data['is_active'] = 1;
            $group->create($data);
            $this->flash('success', 'Group created successfully');
        }
        
        $this->redirect('/groups');
    }

    /**
     * Delete group
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/groups');
        }
        
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->flash('error', 'Invalid group ID');
            $this->redirect('/groups');
        }
        
        $group = $this->model('Group');
        $group->delete($id);
        
        $this->flash('success', 'Group deleted successfully');
        $this->redirect('/groups');
    }

    /**
     * Manage group members
     */
    public function members() {
        $id = (int) ($this->input('id') ?? 0);
        
        if (!$id) {
            $this->redirect('/groups');
        }
        
        $group = $this->model('Group');
        $group = $group->find($id);  // Use the loaded group
        
        if (!$group) {
            $this->flash('error', 'Group not found');
            $this->redirect('/groups');
        }
        
        $this->data['group'] = $group;
        $this->data['members'] = $group->members();
        $this->data['member_count'] = $group->getMemberCount();
        
        // Get available members to add
        $member = $this->model('Member');
        $allMembers = $member->getByStatus('member');
        
        // Filter out already members
        $currentMemberIds = array_column($group->members(), 'id');
        $availableMembers = array_filter($allMembers, function($m) use ($currentMemberIds) {
            return !in_array($m['id'], $currentMemberIds);
        });
        
        $this->data['available_members'] = array_values($availableMembers);
        
        $this->view('groups.members');
    }

    /**
     * Add member to group
     */
    public function addMember() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/groups');
        }
        
        $groupId = (int) ($this->input('group_id') ?? 0);
        $memberId = (int) ($this->input('member_id') ?? 0);
        $role = $this->input('role', 'member');
        
        if (!$groupId || !$memberId) {
            $this->flash('error', 'Invalid request');
            $this->redirect('/groups');
        }
        
        $group = $this->model('Group');
        $group = $group->find($groupId);
        
        if (!$group) {
            $this->flash('error', 'Group not found');
            $this->redirect('/groups/members?id=' . $groupId);
        }
        
        if ($group->hasMember($memberId)) {
            $this->flash('error', 'Member is already in this group');
        } else {
            $group->addMember($memberId, $role);
            $this->flash('success', 'Member added to group');
        }
        
        $this->redirect('/groups/members?id=' . $groupId);
    }

    /**
     * Remove member from group
     */
    public function removeMember() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/groups');
        }
        
        $groupId = (int) ($this->input('group_id') ?? 0);
        $memberId = (int) ($this->input('member_id') ?? 0);
        
        if (!$groupId || !$memberId) {
            $this->flash('error', 'Invalid request');
            $this->redirect('/groups');
        }
        
        $group = $this->model('Group');
        $group = $group->find($groupId);
        
        if (!$group) {
            $this->flash('error', 'Group not found');
            $this->redirect('/groups/members?id=' . $groupId);
        }
        
        $group->removeMember($memberId);
        
        $this->flash('success', 'Member removed from group');
        $this->redirect('/groups/members?id=' . $groupId);
    }
}
