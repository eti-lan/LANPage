<?php
$catering['title'] = 'Catering';
$catering['new_order'] = 'Place New Order';
$catering['current_orders'] = 'Current Orders Today';
$catering['name'] = 'Name';
$catering['flyer_number'] = 'Number on Flyer';
$catering['quantity'] = 'Quantity';
$catering['size'] = 'Size';
$catering['size_normal'] = 'Normal';
$catering['size_small'] = 'Small';
$catering['size_big'] = 'Big';
$catering['comments'] = 'Comments';
$catering['price'] = 'Unit Price Including Delivery';
$catering['order_button'] = 'Place Order';
$catering['orders_summary'] = 'Summary';
$catering['status_legend'] = 'Status Legend';
$catering['status_new'] = 'New';
$catering['status_processing'] = 'In Progress';
$catering['status_completed'] = 'Completed';
$catering['total_today'] = 'Total Today:';
$catering['orders_count'] = 'Orders:';
$catering['view_flyer'] = 'View Current Flyer "%s"';
$catering['admin_panel'] = 'Admin Panel';
$catering['time'] = 'Time';
$catering['status'] = 'Status';
$catering['total'] = 'Total';
$catering['total_today'] = 'Total today';
$catering['currency'] = '&#8364;';
$catering['error_occurred'] = 'An error occurred. Please try again.';
$catering['total_sum'] = 'Total sum';
$catering['error'] = 'Error';
$catering['quantity_times'] = 'x';
$catering['total_summary'] = 'Total today';
$catering['orders'] = 'Orders';
// Validation messages
$catering['validation'] = array(
    'name_required' => 'Please enter a name',
    'flyer_number_invalid' => 'Please enter a valid flyer number',
    'quantity_invalid' => 'Please enter a valid quantity',
    'size_invalid' => 'Please select a valid size',
    'price_invalid' => 'Please enter a valid price'
);

// Status messages
$catering['messages'] = array(
    'order_success' => 'Order successfully placed',
    'save_error' => 'Error saving the order',
    'database_error' => 'Database error',
    'general_error' => 'An error occurred',
    'validation_error' => 'Validation error',
    'invalid_request' => 'Invalid request method',
    'post_only' => 'Only POST requests are allowed'
);

// Admin-Panel translations
$catering['admin'] = array(
    'title' => 'Manage Orders', 
    'stats' => array(
        'total_orders' => 'Total Orders',
        'total_revenue' => 'Total Revenue',
        'paid_revenue' => 'Paid Revenue',
        'unpaid_revenue' => 'Unpaid Revenue'
    ),
    'add_order' => 'Add New Order Manually',
    'current_orders' => 'Current Orders',
    'confirm_delete' => 'Really delete ALL entries?',
    'confirm_delete_checkbox' => 'I confirm that I want to delete all entries',
    'delete_all' => 'Delete All Entries',
    'actions' => 'Actions',
    'delete' => 'Delete',
    'paid_status' => 'Paid',
    'add_button' => 'Add Order',
    'edit_order' => 'Edit Order',
    'save_changes' => 'Save changes',
    'cancel' => 'Cancel',
    'unpaid_status' => 'not paid',
    'edit' => 'Edit',
    'confirm_delete_single' => 'Really delete this Order?'
);

$catering['flyer'] = array(
    'management' => 'Flyer Management',
    'toggle' => 'Show Flyer Management',
    'upload' => array(
        'title' => 'Upload Flyer',
        'name' => 'Flyer Name',
        'file' => 'PDF File',
        'button' => 'Upload',
        'error' => 'Only PDF files are allowed'
    ),
    'list' => array(
        'name' => 'Name',
        'filename' => 'Filename',
        'upload_date' => 'Upload Date',
        'status' => 'Status',
        'actions' => 'Actions'
    ),
    'status' => array(
        'active' => 'Active',
        'inactive' => 'Inactive'
    ),
    'actions' => array(
        'view' => 'View',
        'delete' => 'Delete',
        'close' => 'Close'
    ),
    'messages' => array(
		'update_success' => 'Status successfully updated',
		'delete_confirm' => 'Really delete this flyer?',
		'delete_success' => 'Flyer successfully deleted',
		'upload_success' => 'Flyer successfully uploaded',
		'default_error' => 'An error occurred',
		'table_error' => 'Error updating table'
    )
);

?>
