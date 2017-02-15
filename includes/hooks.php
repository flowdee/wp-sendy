<?php
/**
 * Hooks
 *
 * @package     SFWP\Hooks
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Extend body classes
 */
function sfwp_add_body_classes( $classes ) {

    $classes[] = 'ufwp';

    return $classes;
}
//add_filter( 'body_class', 'sfwp_add_body_classes' );

/**
 * Maybe add credits
 */
function sfwp_maybe_add_credits_to_the_content( $content ) {

    if ( ! is_single() && ! is_page() )
        return $content;

    $options = sfwp_get_options();

    $credits = ( isset( $options['credits'] ) && $options['credits'] == '1' ) ? true : false;

    if ( sfwp_has_plugin_content() && $credits ) {

        $credits_url = apply_filters( 'sfwp_credits_url', 'https://wordpress.org/plugins/wp-sendy/' );

        $credits_link = '<a href="' . $credits_url . '" target="_blank" rel="nofollow" title="' . __('Udemy for WordPress', 'wp-sendy') . '">' . __('Udemy for WordPress', 'wp-sendy') . '</a>';

        $content .= '<p><small>' . __('Presentation of the video courses powered by ', 'wp-sendy') . $credits_link . '.</small></p>';
    }

    return $content;
}
add_filter( 'the_content', 'sfwp_maybe_add_credits_to_the_content' );

/**
 * Custom CSS
 */
function sfwp_insert_custom_css() {

    $options = sfwp_get_options();

    $custom_css_activated = ( isset( $options['custom_css_activated'] ) && $options['custom_css_activated'] == '1' ) ? true : false;

    if ( sfwp_has_plugin_content() && $custom_css_activated && ! empty ( $options['custom_css'] ) ) {
        echo '<style type="text/css">' . $options['custom_css'] . '</style>';
    }
}
add_action('wp_head','sfwp_insert_custom_css');