<?php
/**
 * Functions
 *
 * @package     SFWP\Functions
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Validate API credentials
 *
 * @param $api_url
 * @param $api_key
 * @return array
 */
function sfwp_validate_api_credentials( $api_url, $api_key ) {

    $validation = array(
        'status' => false,
        'error' => null
    );

    $SendyAPI = new SFWP_Sendy_API( $api_url, $api_key );
    $SendyAPI->setListId( '123456789' );
    $response = $SendyAPI->subcount();

    var_dump( $response );

    $validation['status'] = true;
    $validation['error'] = 'blub';

    return $validation;
}

/**
 * Check content if scripts must be loaded
 */
function sfwp_has_plugin_content() {

    global $post;

    if( ( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'wp-sendy') || has_shortcode( $post->post_content, 'udemy') ) ) ) {
        return true;
    }

    return false;
}