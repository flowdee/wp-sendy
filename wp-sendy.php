<?php
/**
 * Plugin Name:     Sendy for WordPress
 * Plugin URI:      https://wordpress.org/plugins/wp-sendy/
 * Description:     Subscribe your WordPress site visitors to your Sendy lists, with ease.
 * Version:         1.0.0
 * Author:          flowdee
 * Author URI:      https://flowdee.de
 * Text Domain:     wp-sendy
 *
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'SFWP' ) ) {

    /**
     * Main Udemy class
     *
     * @since       1.0.0
     */
    class SFWP {

        /**
         * @var         SFWP $instance The one true SFWP
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true SFWP
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new SFWP();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {

            // Plugin name
            define( 'SFWP_NAME', 'Sendy for WordPress' );

            // Plugin version
            define( 'SFWP_VER', '1.0.0' );

            // Plugin path
            define( 'SFWP_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'SFWP_URL', plugin_dir_url( __FILE__ ) );
        }
        
        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {

            // Include scripts
            require_once SFWP_DIR . 'includes/helper.php';

            if ( is_admin() ) {
                require_once SFWP_DIR . 'includes/admin/functions.php';
                require_once SFWP_DIR . 'includes/admin/pages.php';
                require_once SFWP_DIR . 'includes/admin/plugins.php';
                require_once SFWP_DIR . 'includes/admin/class.integrations.php';
                require_once SFWP_DIR . 'includes/admin/class.settings.php';
            }

            require_once SFWP_DIR . 'includes/class.integration.php';
            require_once SFWP_DIR . 'includes/class.sendy.php';
            require_once SFWP_DIR . 'includes/scripts.php';
            require_once SFWP_DIR . 'includes/functions.php';
            require_once SFWP_DIR . 'includes/shortcodes.php';
            //require_once SFWP_DIR . 'includes/widgets.php';
            //require_once SFWP_DIR . 'includes/hooks.php';

            require_once SFWP_DIR . 'includes/integrations/bootstrap.php';
        }

        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = SFWP_DIR . '/languages/';
            $lang_dir = apply_filters( 'sfwp_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'wp-sendy' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'wp-sendy', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/wp-sendy/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/wp-sendy/ folder
                load_textdomain( 'wp-sendy', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/wp-sendy/languages/ folder
                load_textdomain( 'wp-sendy', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'wp-sendy', false, $lang_dir );
            }
        }
    }
} // End if class_exists check

/**
 * The main function responsible for returning the one true SFWP
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \SFWP The one true SFWP
 *
 */
function sfwp_load() {

    $instance = SFWP::instance();

    do_action( 'sfwp_init' );

    return $instance;
}
add_action( 'plugins_loaded', 'sfwp_load' );