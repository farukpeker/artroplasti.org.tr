<?php
/**
 * Payment Functions
 */

if (!defined('ABSPATH')) {
    exit;
}

// Create custom table for payments
function artroplasti_create_payments_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'artroplasti_payments';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        amount decimal(10,2) NOT NULL,
        payment_date datetime NOT NULL,
        payment_method varchar(50) NOT NULL,
        payment_status varchar(20) NOT NULL,
        transaction_id varchar(100),
        description text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY user_id (user_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'artroplasti_create_payments_table');

// Get user payments
function artroplasti_get_user_payments($user_id = null) {
    global $wpdb;
    
    if (!$user_id) {
        $user_id = get_current_user_id();
    }

    $table_name = $wpdb->prefix . 'artroplasti_payments';
    
    $payments = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d ORDER BY payment_date DESC",
        $user_id
    ));

    return $payments;
}

// Add new payment
function artroplasti_add_payment($user_id, $amount, $payment_method, $payment_status, $transaction_id = '', $description = '') {
    global $wpdb;
    $table_name = $wpdb->prefix . 'artroplasti_payments';

    $data = array(
        'user_id'        => $user_id,
        'amount'         => $amount,
        'payment_date'   => current_time('mysql'),
        'payment_method' => $payment_method,
        'payment_status' => $payment_status,
        'transaction_id' => $transaction_id,
        'description'    => $description,
    );

    $wpdb->insert($table_name, $data);
    
    return $wpdb->insert_id;
}

// Get payment by ID
function artroplasti_get_payment($payment_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'artroplasti_payments';

    $payment = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id = %d",
        $payment_id
    ));

    return $payment;
}

// Update payment status
function artroplasti_update_payment_status($payment_id, $status) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'artroplasti_payments';

    return $wpdb->update(
        $table_name,
        array('payment_status' => $status),
        array('id' => $payment_id),
        array('%s'),
        array('%d')
    );
}
