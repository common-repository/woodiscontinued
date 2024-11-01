<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function woodiscontinued_wp_enqueue_scripts() {
    if( is_product() ) {
        wp_enqueue_script( 'woodiscontinued-public', plugin_dir_url( __FILE__ ) . '/js/woodiscontinued-script.js', array(), '1.0.1' );
    }
}
add_action( 'wp_enqueue_scripts', 'woodiscontinued_wp_enqueue_scripts' );

function woodiscontinued_woocommerce_after_add_to_cart_form() {
    if( is_product() ) {
        ?>
            <script>
                if(discontinuePub) {
                    discontinuePub.simpleProductDiscontinued(); 
                    discontinuePub.observer(discontinuePub.observed()).observe(
                        discontinuePub.observed(), 
                        { childList: true });
                }
            </script>
        <?php
    }
}
add_action( 'woocommerce_after_add_to_cart_form', 'woodiscontinued_woocommerce_after_add_to_cart_form' );

function woodiscontinued_woocommerce_get_availability_text( $availability_text, $product ) {
    $stock_status = get_post_meta( $product->get_id(), '_stock_status', true );
    if( strcasecmp( $stock_status, 'discontinued' ) === 0 ){
        return __( 'Discontinued', 'woodiscontinued' );
    }

    return $availability_text;
}
add_filter( 'woocommerce_get_availability_text', 'woodiscontinued_woocommerce_get_availability_text', 99, 2 );

function woodiscontinued_woocommerce_product_add_to_cart_text( $text, $product ) {
    $stock_status = get_post_meta( $product->get_id(), '_stock_status', true );
    if( strcasecmp( $stock_status, 'discontinued' )  === 0 ) {
        return __( 'Read more', 'woocommerce' );
    }

    return $text;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'woodiscontinued_woocommerce_product_add_to_cart_text', 99, 2 );

function woodiscontinued_woocommerce_product_add_to_cart_url( $url, $product ) {
    $product_id = $product->get_id();
    $stock_status = get_post_meta( $product_id, '_stock_status', true );
    if( strcasecmp( $stock_status, 'discontinued' )  === 0 ) {
        return get_permalink( $product->get_id() );
    } 
    
    return $url;
}
add_filter( 'woocommerce_product_add_to_cart_url', 'woodiscontinued_woocommerce_product_add_to_cart_url', 99, 2 );

function woodiscontinued_woocommerce_loop_add_to_cart_link( $html, $product, $args ) {
    $stock_status = get_post_meta( $product->get_id(), '_stock_status', true );
    if (isset( $args['class'] ) && strcasecmp( $stock_status, 'discontinued' )  === 0 && strpos( $args['class'], 'ajax_add_to_cart') !== false ) {
        return str_replace( 'ajax_add_to_cart','', $html );
    }

    return $html;
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'woodiscontinued_woocommerce_loop_add_to_cart_link', 99, 3 );
