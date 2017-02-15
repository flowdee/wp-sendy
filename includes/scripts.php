<?php
/**
 * Scripts
 *
 * @package     SFWP\Scripts
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @global      string $post_type The type of post that we are editing
 * @return      void
 */
function sfwp_admin_scripts( $hook ) {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'SFWP_DEBUG' ) && SFWP_DEBUG ) ) ? '' : '.min';

    /**
     *	Settings page only
     */
    $screen = get_current_screen();

    if ( ! empty( $screen->base ) && ( $screen->base == 'settings_page_wp-sendy' || $screen->base == 'widgets' ) ) {

        wp_enqueue_script( 'sfwp_admin_js', SFWP_URL . 'public/assets/js/admin' . $suffix . '.js', array( 'jquery' ), SFWP_VER );
        wp_enqueue_style( 'sfwp_admin_css', SFWP_URL . 'public/assets/css/admin' . $suffix . '.css', false, SFWP_VER );

        do_action( 'sfwp_admin_enqueue_scripts' );
    }
}
add_action( 'admin_enqueue_scripts', 'sfwp_admin_scripts', 100 );

/**
 * Frontend scripts
 *
 * @since       1.0.0
 * @return      void
 */
function sfwp_scripts( $hook ) {

    if ( sfwp_has_plugin_content() ) {
        sfwp_load_scripts();
    }
}
add_action( 'wp_enqueue_scripts', 'sfwp_scripts' );

/**
 * Load frontend scripts
 */
function sfwp_load_scripts() {

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix = ( ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ( defined( 'SFWP_DEBUG' ) && SFWP_DEBUG ) ) ? '' : '.min';

    //wp_enqueue_script( 'sfwp_scripts', SFWP_URL . 'public/assets/js/scripts' . $suffix . '.js', array( 'jquery' ), SFWP_VER, true );
    wp_enqueue_style( 'sfwp_styles', SFWP_URL . 'public/assets/css/styles' . $suffix . '.css', false, SFWP_VER );

    do_action( 'sfwp_enqueue_scripts' );
}