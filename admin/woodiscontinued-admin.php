<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function woodiscontinued_admin_enqueue_scripts() {
    wp_register_script( 'woodiscontinued-admin-script', plugin_dir_url( __FILE__ ) . '/js/woodiscontinued-admin.js', array() );
    $translation_array = array(
        'variableDrodownDiscontinued' => __( 'Set Status - Discontinued', 'woodiscontinued' ),
    );

    wp_localize_script( 'woodiscontinued-admin-script' , 'woodiscontinuedVars', $translation_array );
    wp_enqueue_script( 'woodiscontinued-admin-script' );
}
add_action( 'admin_enqueue_scripts', 'woodiscontinued_admin_enqueue_scripts' );

function woodiscontinued_woocommerce_product_options_stock_status() {
    woocommerce_wp_select( 
        array( 
            'id' => '_stock_status', 
            'wrapper_class' => 'hide_if_variable custom-stock-status', 
            'label' => __( 'Stock status', 'woocommerce' ), 
            'options' => array(
                'instock' => __( 'In Stock', 'woocommerce' ),
                'outofstock' => __( 'Out of stock', 'woocommerce' ),
                'onbackorder' => __( 'On Backorder', 'woocommerce' ), 
                'discontinued' => __( 'Discontinued', 'woodiscontinued' )), 
            'desc_tip' => true, 
            'description' => __( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'woocommerce' ) ) );
}
add_action('woocommerce_product_options_stock_status', 'woodiscontinued_woocommerce_product_options_stock_status');

function woodiscontinued_woocommerce_process_product_meta( $product_id ) {
    update_post_meta( $product_id, '_stock_status', wc_clean( $_POST['_stock_status'] ) );
}
add_action( 'woocommerce_process_product_meta', 'woodiscontinued_woocommerce_process_product_meta', 99, 1 );

function woodiscontinued_woocommerce_variation_options_pricing( $loop, $variation_data, $variation ) {
    $product = new WC_Product_Variation( $variation->ID );
    woocommerce_wp_select(
        array(
            'id'            => "variable_stock_status{$loop}",
            'name'          => "variable_stock_status[{$loop}]",
            'value'         => '', //updated via ajax call
            'label'         => __( 'Stock status', 'woocommerce' ),
            'options'       => array(
                'instock' => __( 'In Stock', 'woocommerce' ),
                'outofstock' => __( 'Out of stock', 'woocommerce' ),
                'onbackorder' => __( 'On Backorder', 'woocommerce' ), 
                'discontinued' => __( 'Discontinued', 'woodiscontinued' )), 
            'desc_tip'      => true,
            'description'   => __( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'woocommerce' ),
            'wrapper_class' => 'form-row form-row-full hide_if_variation_manage_stock custom-stock-status-variable'
            )
        );

}
add_action( 'woocommerce_variation_options_pricing', 'woodiscontinued_woocommerce_variation_options_pricing', 99, 3 );

function woodiscontinued_save_product_variation( $product_id, $index ) {
    if( isset( $_POST['variable_stock_status'] ) ) {
        if( array_key_exists($index, $_POST['variable_stock_status'] ) ) {
            update_post_meta( $product_id, '_stock_status', wc_clean( $_POST['variable_stock_status'][$index] ) );
        }
    }
}
add_action( 'woocommerce_save_product_variation', 'woodiscontinued_save_product_variation', 1, 2 );

function woodiscontinued_rest_api_init() {
    register_rest_route( 
        'woodiscontinued/v2', 
        '/my_meta_query/', 
        array(
            'methods' => 'GET', 
            'callback' => 'woodiscontinued_get_variations_stock_status' 
    ) );
}

function woodiscontinued_get_variations_stock_status(){
    if(isset($_GET['parent_product_id'])) {
        $product_id = $_GET['parent_product_id'];
        $variation_stock_status_list = array();
        $product = get_product( $product_id );
        $children_ids = $product->get_children();
        foreach( $children_ids as $child_product_id ) {
            $stock_status = get_post_meta( $child_product_id, '_stock_status', true );
            $variation_stock_status_list[] = array( $child_product_id => $stock_status );
        }

        return $variation_stock_status_list;
    }
}
add_action( 'rest_api_init', 'woodiscontinued_rest_api_init' );

function woodiscontinued_woocommerce_variable_product_sync_data ( $product ) {
    $all_are_discontinued_flag = true;
    $child_id_list = $product->get_children();
    foreach( $child_id_list as $id ){
        $child_stock_status = get_post_meta( $id, "_stock_status", true );        
        if( strcasecmp( $child_stock_status, 'discontinued' ) !== 0 ){
            $all_are_discontinued_flag = false;
            break;
        }
    }

    if( $all_are_discontinued_flag ) {
        update_post_meta( $product->get_id(), '_stock_status', 'discontinued' );
        wp_die();
    } 
}
add_action( 'woocommerce_variable_product_sync_data', 'woodiscontinued_woocommerce_variable_product_sync_data' );

function woodiscontinued_woocommerce_bulk_edit_variations_default( $bulk_action, $data, $product_id, $variations ) {
    if( $bulk_action === 'variable_stock_status_discontinued' ) {
        foreach ( $variations as $variation_id ) {
            update_post_meta( $variation_id, '_stock_status', 'discontinued' );
        }
    }
}
add_action( 'woocommerce_bulk_edit_variations_default', 'woodiscontinued_woocommerce_bulk_edit_variations_default', 99, 4 );

function woodiscontinued_woocommerce_admin_stock_html( $stock_html, $product ) {
    $stock_status = get_post_meta( $product->get_id(), '_stock_status', true );
    if( strcasecmp( $stock_status, 'discontinued' ) === 0 ){
        $stock_html = str_ireplace( 'instock', 'outofstock', $stock_html );
        $stock_html = str_ireplace( 'in stock', 'Discontinued', $stock_html );
    }

    return $stock_html;
}
add_filter('woocommerce_admin_stock_html', 'woodiscontinued_woocommerce_admin_stock_html', 99, 2 );
