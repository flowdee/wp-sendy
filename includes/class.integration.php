<?php
/**
 * Integration Class
 *
 * @package     SFWP\includes
 * @since       1.0.0
 */


// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if ( ! class_exists( 'SFWP_Integration' ) ) {

    class SFWP_Integration
    {
        /**
         * @var string Name of this integration.
         */
        public $name = '';

        /**
         * @var string Description
         */
        public $description = '';

        /**
         * @var string Slug, used as an unique identifier for this integration.
         */
        public $slug = '';

        /**
         * @var bool Integration dependency installed or not
         */
        public $installed = false;

        /**
         * @var bool Integration enable or not
         */
        public $enabled = false;

        /**
         * @var array Array of settings
         */
        public $options = array();

        public function __construct() {

            // Variables
            $this->options = sfwp_get_integrations_options();

            $this->installed = $this->is_installed();
            $this->enabled = $this->is_enabled();

            if ( $this->installed && $this->enabled ) {
                add_action( 'sfwp_integrations_enabled_list', array( &$this, 'add_integrations_list_setting') );
            } else {
                add_action( 'sfwp_integrations_available_list', array( &$this, 'add_integrations_list_setting') );
            }

            add_action('sfwp_integrations_' . $this->slug . '_settings', array( $this, 'settings_render') );
        }

        public function settings_render() {
            // Silence
        }

        public function settings_header_render() {
            ?>
            <div class="postbox">
                <h3 class="hndle"><?php echo $this->name; ?></h3>
                <div class="inside">
                    <p><?php echo $this->description; ?></p>
                    <table class="form-table">
                        <tbody>
            <?php
        }

        public function settings_status_render() {

            $status = ( ! empty( $this->options[$this->slug . '_status'] ) ) ? true : false;
            ?>
            <tr valign="top">
                <th scope="row"><?php _e( 'Status', 'wp-sendy' ); ?></th>
                <td>
                    <label><input type="radio" name="sfwp_integrations[<?php echo $this->slug; ?>_status]" value="1" <?php if ( $status ) echo 'checked'; ?>><?php _e( 'Enabled', 'wp-sendy' ); ?></label>&nbsp;
                    <label><input type="radio" name="sfwp_integrations[<?php echo $this->slug; ?>_status]" value="0" <?php if ( ! $status ) echo 'checked'; ?>><?php _e( 'Disabled', 'wp-sendy' ); ?></label>
                </td>
            </tr>
            <?php
        }

        public function settings_footer_render() {
            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }

        /**
         * Are the required dependencies for this integration installed?
         *
         * @return bool
         */
        public function is_installed() {
            return false;
        }

        /**
         * Check if the integration is enabled
         *
         * @return bool
         */
        public function is_enabled() {
            return ( ! empty( $this->options[$this->slug . '_status'] ) ) ? true : false;
        }

        /**
         * Add integrations list setting
         *
         * @param $integrations
         * @return array
         */
        public function add_integrations_list_setting( $integrations ) {

            $integrations[] = array(
                'slug' => $this->slug,
                'name' => $this->name,
                'description' => $this->description,
                'installed' => $this->installed
            );

            return $integrations;
        }
    }
}