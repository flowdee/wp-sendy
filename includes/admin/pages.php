<?php
/**
 * Setup admin menu pages
 *
 * Source: http://stackoverflow.com/a/23002306
 *
 */
add_action( 'admin_menu', function() {

    $parent_menu_slug = 'wp-sendy';

    add_menu_page(
        __( 'Sendy', 'collpress' ),
        __( 'Sendy', 'collpress' ),
        'edit_pages',
        $parent_menu_slug,
        'sfwp_admin_dashboard_page',
        plugins_url( 'wp-sendy/public/assets/img/menu-icon.png' ),
        100
    );

    /*
    add_submenu_page(
        $parent_menu_slug,
        __( 'CollPress - Dashboard', 'collpress' ),
        __( 'Dashboard', 'collpress' ),
        'edit_pages' ,
        $parent_menu_slug
    );
    */

    /**
     * Dynamically add more menu items
     */
    do_action( 'sfwp_admin_menu', $parent_menu_slug );

}, 11 );