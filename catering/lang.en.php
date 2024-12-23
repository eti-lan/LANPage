<?php
// General translations
$catering = [
    'title' => 'Catering',
    'time' => 'Time',
    'name' => 'Name',
    'status' => 'Status',
    'error' => 'Error',
    'currency' => '&#8364;',
    
    // Order-related translations
    'new_order' => 'Place New Order',
    'current_orders' => 'Current Orders Today',
    'flyer_number' => 'Number on Flyer',
    'quantity' => 'Quantity',
    'quantity_times' => 'x',
    'comments' => 'Comments',
    'price' => 'Unit Price Including Delivery',
    'order_button' => 'Place Order',
    'orders' => 'Orders',
    
    // Sizes
    'size' => 'Size',
    'size_normal' => 'Normal',
    'size_small' => 'Small',
    'size_big' => 'Big',
    
    // Status
    'status_legend' => 'Status Legend',
    'status_new' => 'New',
    'status_processing' => 'In Progress',
    'status_completed' => 'Completed',
    
    // Totals and Summaries
    'total' => 'Total',
    'total_today' => 'Total Today',
    'total_sum' => 'Total sum',
    'total_summary' => 'Total today',
    'orders_summary' => 'Summary',
    'orders_count' => 'Orders:',
    
    // Links and Buttons
    'view_flyer' => 'View Current Flyer "%s"',
    'admin_panel' => 'Admin Panel',
    
    // Error messages
    'error_occurred' => 'An error occurred. Please try again.',
];

// Validation messages
$catering['validation'] = [
    'name_required' => 'Please enter a name',
    'flyer_number_invalid' => 'Please enter a valid flyer number',
    'quantity_invalid' => 'Please enter a valid quantity',
    'size_invalid' => 'Please select a valid size',
    'price_invalid' => 'Please enter a valid price'
];

// Status messages
$catering['messages'] = [
    'order_success' => 'Order successfully placed',
    'save_error' => 'Error saving the order',
    'database_error' => 'Database error',
    'general_error' => 'An error occurred',
    'validation_error' => 'Validation error',
    'invalid_request' => 'Invalid request method',
    'post_only' => 'Only POST requests are allowed'
];

// Admin-Panel translations
$catering['admin'] = [
    'title' => 'Manage Orders',
    'stats' => [
        'total_orders' => 'Total Orders',
        'total_revenue' => 'Total Revenue',
        'paid_revenue' => 'Paid Revenue',
        'unpaid_revenue' => 'Unpaid Revenue'
    ],
    'add_order' => 'Add New Order Manually',
    'current_orders' => 'Current Orders',
    'confirm_delete' => 'Really delete ALL entries?',
    'confirm_delete_checkbox' => 'I confirm that I want to delete all entries',
    'confirm_delete_single' => 'Really delete this Order?',
    'delete_all' => 'Delete All Entries',
    'actions' => 'Actions',
    'delete' => 'Delete',
    'edit' => 'Edit',
    'edit_order' => 'Edit Order',
    'save_changes' => 'Save changes',
    'cancel' => 'Cancel',
    'paid_status' => 'Paid',
    'unpaid_status' => 'not paid',
    'add_button' => 'Add Order'
];

// Flyer-Management translations
$catering['flyer'] = [
    'management' => 'Flyer Management',
    'toggle' => 'Show Flyer Management',
    'upload' => [
        'title' => 'Upload Flyer',
        'name' => 'Flyer Name',
        'file' => 'PDF File',
        'button' => 'Upload',
        'error' => 'Only PDF files are allowed'
    ],
    'list' => [
        'name' => 'Name',
        'filename' => 'Filename',
        'upload_date' => 'Upload Date',
        'status' => 'Status',
        'actions' => 'Actions'
    ],
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive'
    ],
    'actions' => [
        'view' => 'View',
        'delete' => 'Delete',
        'close' => 'Close'
    ],
    'messages' => [
        'update_success' => 'Status successfully updated',
        'delete_confirm' => 'Really delete this flyer?',
        'delete_success' => 'Flyer successfully deleted',
        'upload_success' => 'Flyer successfully uploaded',
        'default_error' => 'An error occurred',
        'table_error' => 'Error updating table'
    ]
];
?>
