<?php
/**
 * Application Routes
 */

return [
    // Default route
    '/' => ['controller' => 'Dashboard', 'method' => 'index'],
    '/dashboard' => ['controller' => 'Dashboard', 'method' => 'index'],
    
    // Authentication
    '/login' => ['controller' => 'Auth', 'method' => 'login'],
    '/login/authenticate' => ['controller' => 'Auth', 'method' => 'authenticate'],
    '/logout' => ['controller' => 'Auth', 'method' => 'logout'],
    '/register' => ['controller' => 'Auth', 'method' => 'register'],
    '/register/store' => ['controller' => 'Auth', 'method' => 'store'],
    '/forgot-password' => ['controller' => 'Auth', 'method' => 'forgotPassword'],
    '/reset-password' => ['controller' => 'Auth', 'method' => 'resetPassword'],
    
    // Members
    '/members' => ['controller' => 'Member', 'method' => 'index'],
    '/members/add' => ['controller' => 'Member', 'method' => 'add'],
    '/members/edit' => ['controller' => 'Member', 'method' => 'edit'],
    '/members/view' => ['controller' => 'Member', 'method' => 'viewMember'],
    '/members/save' => ['controller' => 'Member', 'method' => 'save'],
    '/members/delete' => ['controller' => 'Member', 'method' => 'delete'],
    '/members/search' => ['controller' => 'Member', 'method' => 'search'],
    
    // Families
    '/families' => ['controller' => 'Family', 'method' => 'index'],
    '/families/add' => ['controller' => 'Family', 'method' => 'add'],
    '/families/edit' => ['controller' => 'Family', 'method' => 'edit'],
    '/families/view' => ['controller' => 'Family', 'method' => 'viewFamily'],
    '/families/save' => ['controller' => 'Family', 'method' => 'save'],
    '/families/delete' => ['controller' => 'Family', 'method' => 'delete'],
    
    // Groups
    '/groups' => ['controller' => 'Group', 'method' => 'index'],
    '/groups/add' => ['controller' => 'Group', 'method' => 'add'],
    '/groups/edit' => ['controller' => 'Group', 'method' => 'edit'],
    '/groups/view' => ['controller' => 'Group', 'method' => 'viewGroup'],
    '/groups/save' => ['controller' => 'Group', 'method' => 'save'],
    '/groups/delete' => ['controller' => 'Group', 'method' => 'delete'],
    '/groups/members' => ['controller' => 'Group', 'method' => 'members'],
    '/groups/addMember' => ['controller' => 'Group', 'method' => 'addMember'],
    '/groups/removeMember' => ['controller' => 'Group', 'method' => 'removeMember'],
    
    // Attendance
    '/attendance' => ['controller' => 'Attendance', 'method' => 'index'],
    '/attendance/record' => ['controller' => 'Attendance', 'method' => 'record'],
    '/attendance/save' => ['controller' => 'Attendance', 'method' => 'save'],
    '/attendance/report' => ['controller' => 'Attendance', 'method' => 'report'],
    '/attendance/add-service' => ['controller' => 'Attendance', 'method' => 'addService'],
    
    // Donations
    '/donations' => ['controller' => 'Donation', 'method' => 'index'],
    '/donations/add' => ['controller' => 'Donation', 'method' => 'add'],
    '/donations/edit' => ['controller' => 'Donation', 'method' => 'edit'],
    '/donations/save' => ['controller' => 'Donation', 'method' => 'save'],
    '/donations/delete' => ['controller' => 'Donation', 'method' => 'delete'],
    '/donations/report' => ['controller' => 'Donation', 'method' => 'report'],
    
    // Expenses
    '/expenses' => ['controller' => 'Expense', 'method' => 'index'],
    '/expenses/add' => ['controller' => 'Expense', 'method' => 'add'],
    '/expenses/edit' => ['controller' => 'Expense', 'method' => 'edit'],
    '/expenses/save' => ['controller' => 'Expense', 'method' => 'save'],
    '/expenses/delete' => ['controller' => 'Expense', 'method' => 'delete'],
    '/expenses/approve' => ['controller' => 'Expense', 'method' => 'approve'],
    '/expenses/categories' => ['controller' => 'Expense', 'method' => 'categories'],
    '/expenses/save-category' => ['controller' => 'Expense', 'method' => 'saveCategory'],
    
    // Finance
    '/finance' => ['controller' => 'Finance', 'method' => 'index'],
    '/finance/summary' => ['controller' => 'Finance', 'method' => 'summary'],
    '/finance/report' => ['controller' => 'Finance', 'method' => 'report'],
    
    // Events
    '/events' => ['controller' => 'Event', 'method' => 'index'],
    '/events/add' => ['controller' => 'Event', 'method' => 'add'],
    '/events/edit' => ['controller' => 'Event', 'method' => 'edit'],
    '/events/view' => ['controller' => 'Event', 'method' => 'viewEvent'],
    '/events/save' => ['controller' => 'Event', 'method' => 'save'],
    '/events/delete' => ['controller' => 'Event', 'method' => 'delete'],
    '/events/register' => ['controller' => 'Event', 'method' => 'register'],
    
    // Volunteers
    '/volunteers' => ['controller' => 'Volunteer', 'method' => 'index'],
    '/volunteers/opportunities' => ['controller' => 'Volunteer', 'method' => 'opportunities'],
    '/volunteers/assign' => ['controller' => 'Volunteer', 'method' => 'assign'],
    
    // Communications
    '/communications' => ['controller' => 'Communication', 'method' => 'index'],
    '/communications/announcements' => ['controller' => 'Communication', 'method' => 'announcements'],
    '/communications/messages' => ['controller' => 'Communication', 'method' => 'messages'],
    
    // Reports
    '/reports' => ['controller' => 'Report', 'method' => 'index'],
    '/reports/attendance' => ['controller' => 'Report', 'method' => 'attendance'],
    '/reports/donations' => ['controller' => 'Report', 'method' => 'donations'],
    '/reports/expenses' => ['controller' => 'Report', 'method' => 'expenses'],
    '/reports/members' => ['controller' => 'Report', 'method' => 'members'],
    '/reports/export' => ['controller' => 'Report', 'method' => 'export'],
    
    // Settings
    '/settings' => ['controller' => 'Settings', 'method' => 'index'],
    '/settings/save' => ['controller' => 'Settings', 'method' => 'save'],
    '/settings/users' => ['controller' => 'Settings', 'method' => 'users'],
    '/settings/add-user' => ['controller' => 'Settings', 'method' => 'addUser'],
    '/settings/edit-user' => ['controller' => 'Settings', 'method' => 'editUser'],
    '/settings/update-user' => ['controller' => 'Settings', 'method' => 'updateUser'],
    '/settings/delete-user' => ['controller' => 'Settings', 'method' => 'deleteUser'],
    '/settings/activity' => ['controller' => 'Settings', 'method' => 'activity'],
];
