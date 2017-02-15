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

function sfwp_cleanup_category_name( $category ) {

    $category = str_replace('And', 'and', ucwords( $category, '-' ) );

    return $category;
}

function sfwp_get_datetime( $timestamp ) {

    if ( ! is_numeric( $timestamp ) )
        return null;

    $date_format = get_option( 'date_format', 'm/d/Y' );
    $time_format = get_option( 'time_format', 'g:i:s A' );

    return get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), $date_format . ' - ' . $time_format );
}

function sfwp_the_assets() {
    echo SFWP_URL . 'public/assets';
}

/**
 * Output data to a log for debugging reasons
 **/
function sfwp_addlog( $string ) {

    if ( SFWP_DEBUG ) {

        $log = get_option( 'sfwp_log', '' );

        $string = date( 'd.m.Y H:i:s' ) . " >>> " . $string . "\n";
        $log .= $string;

        update_option( 'sfwp_log', $log );
    }
}

/*
 * Get options
 */
function sfwp_get_options() {
    return get_option( 'sfwp_settings', array() );
}