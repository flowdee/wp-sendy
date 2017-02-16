<?php
/**
 * Integrations
 *
 * @package     SFWP\Admin
 * @since       1.0.0
 */


// Exit if accessed directly
if (!defined('ABSPATH')) exit;


if (!class_exists('SFWP_Integrations')) {

    class SFWP_Integrations
    {
        public $options;

        public $integration;

        public $settings_page_slug;

        public function __construct()
        {
            // Variables
            $this->options = sfwp_get_integrations_options();
            $this->settings_page_slug = 'wp-sendy-integrations';

            $this->integration = ( isset( $_GET['integration'] ) && $_GET['integration'] != '' ) ? $_GET['integration'] : false;

            // Initialize
            add_action('sfwp_admin_menu', array(&$this, 'add_admin_menu'), 30 );
            add_action('admin_init', array(&$this, 'init_settings'));
        }

        function add_admin_menu($parent_menu_slug)
        {

            add_submenu_page(
                $parent_menu_slug,
                __('Integrations', 'collpress'),
                __('Integrations', 'collpress'),
                'edit_pages',
                $this->settings_page_slug,
                array(&$this, 'options_page')
            );
        }

        function init_settings()
        {
            register_setting(
                'sfwp_integrations',
                'sfwp_integrations',
                array(&$this, 'validate_input_callback')
            );

            // Section: Enabled
            add_settings_section(
                'sfwp_integrations_enabled',
                __('Enabled Integrations', 'wp-sendy'),
                array(&$this, 'integrations_enabled_render'),
                'sfwp_integrations'
            );

            /*
            add_settings_field(
                'sfwp_api_status',
                __('Status', 'wp-sendy'),
                array(&$this, 'api_status_render'),
                'sfwp_settings',
                'sfwp_settings_api'
            );
            */

            // Section: Available
            add_settings_section(
                'sfwp_integrations_available',
                __('Available Integrations', 'wp-sendy'),
                array(&$this, 'integrations_available_render'),
                'sfwp_integrations'
            );

            do_action('sfwp_integrations_settings_available');
        }

        function integrations_enabled_render() {

            $integrations = apply_filters( 'sfwp_integrations_enabled_list', array() );

            ?>
            <h3><?php _e( 'Enabled Integrations', 'wp-sendy' ); ?></h3>
            <?php if ( is_array( $integrations ) && sizeof( $integrations ) > 0 ) { ?>
                <?php $this->integrations_table_render( $integrations ); ?>
            <?php } else { ?>
                <p><?php _e( 'No integrations enabled yet.', 'wp-sendy' ); ?></p>
            <?php }
        }

        function integrations_available_render() {

            $integrations = apply_filters( 'sfwp_integrations_available_list', array() );

            ?>
            <h3 style="margin-top: 40px;"><?php _e( 'Enabled Integrations', 'wp-sendy' ); ?></h3>
            <?php if ( is_array( $integrations ) && sizeof( $integrations ) > 0 ) { ?>
                <?php $this->integrations_table_render( $integrations ); ?>
            <?php } else { ?>
                <p><?php _e( 'No integrations available.', 'wp-sendy' ); ?></p>
            <?php }
        }

        function integrations_table_render( $integrations ) {

            if ( ! is_array( $integrations ) || sizeof( $integrations ) == 0 )
                return;
            ?>
            <table class="sfwp-table widefat striped">
                <thead>
                <tr>
                    <th><?php _e( 'Name', 'wp-sendy' ); ?></th>
                    <th><?php _e( 'Description', 'wp-sendy' ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ( $integrations as $integration ) { ?>
                    <tr<?php if ( ! $integration['installed'] ) echo ' style="opacity: 0.4;"'; ?>>
                        <td>
                            <?php if ( $integration['installed'] ) { ?>
                            <strong><a href="<?php $this->settings_page_url( $integration['slug'] ); ?>" title="<?php _e( 'Configure Integration', 'wp-sendy' ); ?>">
                                    <?php } ?>
                                    <?php echo $integration['name']; ?>
                                    <?php if ( $integration['installed'] ) { ?>
                                </a></strong>
                        <?php } ?>
                        </td>
                        <td><?php echo $integration['description']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
        }

        function options_page()
        {
            ?>

            <div class="sfwp sfwp-integrations">
                <div class="wrap">
                    <?php screen_icon(); ?>
                    <h2>
                        <?php _e('Integrations', 'wp-sendy'); ?>
                    </h2>

                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <div class="meta-box-sortables ui-sortable">
                                    <form action="options.php" method="post">

                                        <?php settings_fields('sfwp_integrations'); ?>

                                        <?php if ( $this->integration ) { ?>

                                            <p>
                                                <a class="button secondary" href="<?php $this->settings_page_url(); ?>" title="<?php _e('Back to overview', 'wp-sendy' ); ?>"><?php _e('Back to overview', 'wp-sendy' ); ?></a>
                                            </p>
                                            <?php do_action( 'sfwp_integrations_' . $this->integration . '_settings' ); ?>
                                            <p>
                                                <?php submit_button( 'Save Changes', 'button-primary', 'submit', false ); ?>
                                            </p>

                                        <?php } else { ?>
                                            <?php $this->integrations_enabled_render(); ?>
                                            <?php $this->integrations_available_render(); ?>
                                        <?php } ?>

                                        <?php //sfwp_do_settings_sections('sfwp_integrations'); ?>
                                    </form>
                                </div>

                            </div>
                            <!-- /#post-body-content -->
                            <div id="postbox-container-1" class="postbox-container">
                                <div class="meta-box-sortables">
                                    <?php
                                    $settings_infobox_plugin_slug = apply_filters( 'sfwp_settings_infobox_plugin_slug', 'udemy' );

                                    require_once SFWP_DIR . 'includes/libs/flowdee_infobox.php';
                                    $flowdee_infobox = new Flowdee_Infobox();
                                    $flowdee_infobox->set_plugin_slug( $settings_infobox_plugin_slug );
                                    $flowdee_infobox->display();
                                    ?>
                                </div>

                                <?php if ( ! defined( 'SFWP_PRO_NAME' ) || defined( 'SFWP_PRO_DEBUG' ) ) { ?>
                                    <div class="postbox">
                                        <h3><span><?php _e('Upgrade to PRO Version', 'wp-sendy'); ?></span></h3>
                                        <div class="inside">

                                            <p><?php _e('Do you want to <strong>earn money</strong> with course sales? The PRO version extends the plugin exclusively with our affiliate links feature.', 'wp-sendy'); ?></p>

                                            <ul>
                                                <li><span class="dashicons dashicons-star-filled sfwp-settings-star"></span> <strong><?php _e('Affiliate Links', 'wp-sendy'); ?></strong></li>
                                                <li><span class="dashicons dashicons-star-filled sfwp-settings-star"></span> <strong><?php _e('Masked Links', 'wp-sendy'); ?></strong></li>
                                                <li><span class="dashicons dashicons-star-filled sfwp-settings-star"></span> <strong><?php _e('Click Tracking', 'wp-sendy'); ?></strong></li>
                                                <li><span class="dashicons dashicons-star-filled sfwp-settings-star"></span> <strong><?php _e('Custom Templates', 'wp-sendy'); ?></strong></li>
                                            </ul>

                                            <p>
                                                <?php _e('I would be happy if you give it a chance!', 'wp-sendy'); ?>
                                            </p>

                                            <p>
                                                <?php
                                                $upgrade_link = esc_url( add_query_arg( array(
                                                        'utm_source'   => 'settings-page',
                                                        'utm_medium'   => 'infobox',
                                                        'utm_campaign' => 'Udemy for WordPress - PRO',
                                                    ), 'https://coder.flowdee.de/downloads/wp-sendy-pro/' )
                                                );
                                                ?>
                                                <a class="sfwp-settings-button sfwp-settings-button--block" target="_blank" href="<?php echo $upgrade_link; ?>" rel="nofollow"><?php _e('More details', 'wp-sendy'); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                <?php } ?>

                                <!-- /.meta-box-sortables -->
                            </div>
                            <!-- /.postbox-container -->
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        function settings_page_url( $integration = null ) {

            if ( ! empty( $integration ) ) {
                $url = admin_url( 'admin.php?page=' . $this->settings_page_slug . '&integration=' . $integration );
            } else {
                $url = admin_url( 'admin.php?page=' . $this->settings_page_slug );
            }

            echo $url;
        }
    }
}

new SFWP_Integrations();