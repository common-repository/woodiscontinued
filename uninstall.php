<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

global $wpdb;
$wpdb->update( 
    $wpdb->prefix . 'postmeta', 
    array( 'meta_value' => 'outofstock' ), 
    array( 'meta_key' => '_stock_status', 'meta_value' => 'discontinued' ) );
    