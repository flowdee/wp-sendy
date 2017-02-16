<?php
/**
 * Helper
 *
 * @package     SFWP\Helper
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get options
 */
function sfwp_get_options() {
    return get_option( 'sfwp_settings', array() );
}

/**
 * Get integrations options
 */
function sfwp_get_integrations_options() {
    return get_option( 'sfwp_integrations', array() );
}

/**
 * Display assets url
 */
function sfwp_the_assets() {
    echo SFWP_URL . 'public/assets';
}

/**
 * Debug
 *
 * @param $args
 * @param bool $title
 */
function sfwp_debug( $args, $title = false ) {

    if ( $title ) {
        echo '<h3>' . $title . '</h3>';
    }

    if ( $args ) {
        echo '<pre>';
        print_r($args);
        echo '</pre>';
    }
}