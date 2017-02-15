<?php
/**
 * Plugins
 *
 * @package     SFWP\Admin\Plugins
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugins row action links
 *
 * @author Michael Cannon <mc@aihr.us>
 * @since 1.8
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 * @return array $links
 */
function sfwp_action_links( $links, $file ) {

    $settings_link = '<a href="' . admin_url( 'options-general.php?page=wp-sendy' ) . '">' . esc_html__( 'Settings', 'wp-sendy' ) . '</a>';

    if ( $file == 'wp-sendy/wp-sendy.php' )
        array_unshift( $links, $settings_link );

    return $links;
}
add_filter( 'plugin_action_links', 'sfwp_action_links', 10, 2 );

/**
 * Plugin row meta links
 *
 * @author Michael Cannon <mc@aihr.us>
 * @since 1.8
 * @param array $input already defined meta links
 * @param string $file plugin file path and name being processed
 * @return array $input
 */
function sfwp_row_meta( $input, $file ) {

    if ( $file != 'wp-sendy/wp-sendy.php' )
        return $input;

    $docs_link = esc_url( add_query_arg( array(
            'utm_source'   => 'plugins-page',
            'utm_medium'   => 'plugin-row',
            'utm_campaign' => 'WP Udemy',
        ), 'https://coder.flowdee.de/docs/article/wp-sendy/' )
    );

    $links = array(
        '<a href="' . $docs_link . '">' . esc_html__( 'Documentation', 'wp-sendy' ) . '</a>',
    );

    $input = array_merge( $input, $links );

    return $input;
}
add_filter( 'plugin_row_meta', 'sfwp_row_meta', 10, 2 );