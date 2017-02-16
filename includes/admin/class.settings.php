<?php
/**
 * Settings
 *
 * @package     SFWP\Settings
 * @since       1.0.0
 */


// Exit if accessed directly
if (!defined('ABSPATH')) exit;


if (!class_exists('SFWP_Settings')) {

    class SFWP_Settings
    {
        public $options;

        private $checks = true;

        private $curl;
        private $php;

        public function __construct()
        {
            // Variables
            $this->options = sfwp_get_options();

            // Checks
            $this->curl = $this->check_curl();

            // Initialize
            add_action('sfwp_admin_menu', array( &$this, 'add_admin_menu'), 10 );
            add_action('admin_init', array( &$this, 'init_settings') );
        }

        function add_admin_menu( $parent_menu_slug )
        {

            add_submenu_page(
                $parent_menu_slug,
                __( 'Settings', 'collpress' ),
                __( 'Settings', 'collpress' ),
                'edit_pages',
                $parent_menu_slug,
                array( &$this, 'options_page' )
            );

        }

        function init_settings()
        {
            register_setting(
                'sfwp_settings',
                'sfwp_settings',
                array( &$this, 'validate_input_callback' )
            );

            // SECTION: Quickstart
            add_settings_section(
                'sfwp_quickstart',
                __('Quickstart Guide', 'wp-sendy'),
                array( &$this, 'section_quickstart_render' ),
                'sfwp_settings'
            );

            /*
             * Action to add more settings right after the quickstart
             */
            do_action( 'sfwp_settings_register' );

            // SECTION: API
            add_settings_section(
                'sfwp_settings_api',
                __('Sendy API Settings', 'wp-sendy'),
                false,
                'sfwp_settings'
            );

            add_settings_field(
                'sfwp_api_status',
                __('Status', 'wp-sendy'),
                array(&$this, 'api_status_render'),
                'sfwp_settings',
                'sfwp_settings_api'
            );

            add_settings_field(
                'sfwp_api_url',
                __('Sendy URL', 'wp-sendy'),
                array(&$this, 'api_url_render'),
                'sfwp_settings',
                'sfwp_settings_api',
                array('label_for' => 'sfwp_api_url')
            );

            add_settings_field(
                'sfwp_api_key',
                __('API Key', 'wp-sendy'),
                array(&$this, 'api_key_render'),
                'sfwp_settings',
                'sfwp_settings_api',
                array('label_for' => 'sfwp_api_key')
            );

            /*
             * Action to add more settings within this section
             */
            do_action( 'sfwp_settings_api_register' );

            /*
            // SECTION: Output
            add_settings_section(
                'sfwp_settings_general',
                __('Output Settings', 'wp-sendy'),
                false,
                'sfwp_settings'
            );

            add_settings_field(
                'sfwp_default_templates',
                __('Standard Templates', 'wp-sendy'),
                array(&$this, 'default_templates_render'),
                'sfwp_settings',
                'sfwp_settings_output'
            );

            add_settings_field(
                'sfwp_course_details',
                __('Course Details', 'wp-sendy'),
                array(&$this, 'course_details_render'),
                'sfwp_settings',
                'sfwp_settings_output',
                array('label_for' => 'sfwp_course_details')
            );

            add_settings_field(
                'sfwp_custom_css',
                __('Custom CSS', 'wp-sendy'),
                array(&$this, 'custom_css_render'),
                'sfwp_settings',
                'sfwp_settings_output',
                array('label_for' => 'sfwp_custom_css')
            );
            */

            /*
             * Action to add more settings within this section
             */
            do_action( 'sfwp_settings_output_register' );

            /*
            // SECTION: Debug
            add_settings_section(
                'sfwp_settings_other',
                __('Other Settings', 'wp-sendy'),
                false,
                'sfwp_settings'
            );

            add_settings_field(
                'sfwp_widget_text_shortcodes',
                __('Widgets & Shortcodes', 'wp-sendy'),
                array(&$this, 'widget_text_shortcodes_render'),
                'sfwp_settings',
                'sfwp_settings_other',
                array('label_for' => 'sfwp_widget_text_shortcodes')
            );

            add_settings_field(
                'sfwp_credits',
                __('You love this plugin?', 'wp-sendy'),
                array(&$this, 'credits_render'),
                'sfwp_settings',
                'sfwp_settings_other',
                array('label_for' => 'sfwp_credits')
            );

            add_settings_field(
                'sfwp_developer_mode',
                __('Developer Mode', 'wp-sendy'),
                array(&$this, 'developer_mode_render'),
                'sfwp_settings',
                'sfwp_settings_other',
                array('label_for' => 'sfwp_developer_mode')
            );

            if ( SFWP_DEBUG ) {

                add_settings_field(
                    'sfwp_debug_information',
                    __('Debug Information', 'wp-sendy'),
                    array(&$this, 'debug_information_render'),
                    'sfwp_settings',
                    'sfwp_settings_other'
                );
            }
            */

            /*
             * Action to add more settings within this section
             */
            do_action( 'sfwp_settings_debug_register' );
        }

        function validate_input_callback( $input ) {

            //sfwp_debug($input);

            $status = ( isset ( $this->options['api_status'] ) ) ? $this->options['api_status'] : false;
            $error = ( isset ( $this->options['api_error'] ) ) ? $this->options['api_error'] : '';

            $input['api_url'] = untrailingslashit( $input['api_url'] );

            if ( ! empty ( $input['api_url'] ) && ! empty ( $input['api_key'] ) ) {

                $api_url = ( isset ( $this->options['api_url'] ) ) ? $this->options['api_url'] : '';
                $api_url_new = $input['api_url'];

                $api_key = ( isset ( $this->options['api_key'] ) ) ? $this->options['api_key'] : '';
                $api_key_new = $input['api_key'];

                if ( $api_url != $api_url_new || $api_key != $api_key_new ) {

                    $validation = sfwp_validate_api_credentials( $api_url_new, $api_key_new );

                    $status = ( ! empty ( $validation['status'] ) ) ? true : false;
                    $error = ( ! empty ( $validation['error'] ) ) ? $validation['error'] : '';
                }
            }

            $input['api_status'] = $status;
            $input['api_error'] = $error;

            $input = apply_filters( 'sfwp_settings_validate_input', $input );

            return $input;
        }

        function section_quickstart_render() {
            ?>

            <div class="postbox">
                <h3 class='hndle'><?php _e('Quickstart Guide', 'wp-sendy'); ?></h3>
                <div class="inside">
                    <p><?php _e( 'There are two ways of displaying courses:', 'wp-sendy' ); ?></p>
                    <p>
                        <strong><?php _e( 'Single course by ID', 'wp-sendy' ); ?></strong><br />
                        <?php _e( 'In order to get the course ID, simply add the course to the cart and take the ID out of the url of your browser.', 'wp-sendy' ); ?>
                    </p>
                    <p>
                        <code>[sfwp id="480986"]</code>
                    </p>

                    <p>
                        <strong><?php _e( 'Search for courses', 'wp-sendy' ); ?></strong><br />
                        <?php _e('Alternatively you can search for courses and display grids or lists of multiple courses.', 'wp-sendy'); ?> <span style="color: darkorange; font-weight: bold;"><?php _e( 'This feature requires API keys!', 'wp-sendy' ); ?></span>
                    <p>
                        <code>[sfwp search="css" items="6" template="grid" grid="3"]</code> <?php _e( 'or', 'wp-sendy' ); ?> <code>[sfwp search="html" items="6" template="list"]</code>
                    </p>

                    <p><?php printf( wp_kses( __( 'Please take a look into the <a href="%s">documentation</a> for more options.', 'wp-sendy' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( 'https://coder.flowdee.de/docs/article/wp-sendy/' ) ); ?></p>

                    <?php do_action( 'sfwp_settings_quickstart_render' ); ?>
                </div>
            </div>

            <?php
        }

        function api_status_render() {
            $this->api_status_html();
        }

        function api_url_render() {

            $api_url = ( ! empty( $this->options['api_url'] ) ) ? esc_attr( trim( $this->options['api_url'] ) ) : '';

            ?>
            <input type='text' name='sfwp_settings[api_url]' id="sfwp_api_url" placeholder="https://my-domain.com/sendy"
                   value='<?php echo esc_attr( trim( $api_url ) ); ?>' style="width: 350px;"><br />
            <small><?php _e( 'Please enter the url of your Sendy installation (without trailing slash).', 'wp-sendy' ); ?></small>
            <?php
        }

        function api_key_render() {

            $api_key = ( ! empty( $this->options['api_key'] ) ) ? esc_attr( trim( $this->options['api_key'] ) ) : '';

            ?>
            <input type='text' name='sfwp_settings[api_key]' id="sfwp_api_key"
                   value='<?php echo esc_attr( trim( $api_key ) ); ?>' style="width: 350px;">
            <?php
        }


        /*
        function api_client_render() {

            $api_client_id = ( !empty($this->options['api_client_id'] ) ) ? esc_attr( trim( $this->options['api_client_id'] ) ) : '';
            $api_client_password = ( !empty($this->options['api_client_password'] ) ) ? esc_attr( trim($this->options['api_client_password'] ) ) : '';

            ?>
            <h4 style="margin: 5px 0"><?php _e('Status', 'wp-sendy'); ?></h4>
            <?php if ( ! empty( $api_client_id ) && ! empty( $api_client_password ) ) { ?>
                <?php $this->api_status_render(); ?>
            <?php } else { ?>
                <span style="color: dodgerblue;"><?php _e("API credentials are currently only required when searching courses or displaying categories.", 'wp-sendy'); ?></span>
            <?php } ?>

            <h4 style="margin-bottom: 5px"><?php _e('Client ID', 'wp-sendy'); ?></h4>
            <input type='text' name='sfwp_settings[api_client_id]' id="sfwp_api_client_id"
                   value='<?php echo esc_attr( trim( $api_client_id ) ); ?>' style="width: 350px;">

            <h4 style="margin: 15px 0 5px 0;"><?php _e('Client Password', 'wp-sendy'); ?></h4>
            <input type='text' name='sfwp_settings[api_client_password]' id="sfwp_api_client_password"
                   value='<?php echo esc_attr( trim( $api_client_password ) ); ?>' style="width: 350px;">

            <p>
                <small>
                    <?php printf( wp_kses( __( 'Before entering your API credentials you have to create a new API Client <a href="%s">here</a>.', 'wp-sendy' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( 'https://www.udemy.com/user/edit-api-clients/' ) ); ?>
                </small>
            </p>
            <?php
        }

        function cache_duration_render() {

            $cache_durations = array(
                '360' => __('6 Hours', 'wp-sendy'),
                '720' => __('12 Hours', 'wp-sendy'),
                '1440' => __('1 Day', 'wp-sendy'),
                '4320' => __('3 Days', 'wp-sendy'),
                '10080' => __('1 Week', 'wp-sendy'),
            );

            $cache_duration = ( isset ( $this->options['cache_duration'] ) ) ? $this->options['cache_duration'] : '1440';

            ?>
            <select id="sfwp_cache_duration" name="sfwp_settings[cache_duration]">
                <?php foreach ( $cache_durations as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $cache_duration, $key ); ?>><?php echo $label; ?></option>
                <?php } ?>
            </select>

            <input type="hidden" id="sfwp_delete_cache" name="sfwp_settings[delete_cache]" value="0" />
            <?php
        }

        function default_templates_render() {

            $templates = array(
                'standard' => __('Standard', 'wp-sendy'),
                'grid' => __('Grid', 'wp-sendy'),
                'list' => __('List', 'wp-sendy')
            );

            $template_course = ( isset ( $this->options['template_course'] ) ) ? $this->options['template_course'] : 'standard';
            $template_courses = ( isset ( $this->options['template_courses'] ) ) ? $this->options['template_courses'] : 'list';

            ?>
            <h4 style="margin: 5px 0;"><?php _e('Single Course', 'wp-sendy'); ?></h4>
            <p>
                <select id="sfwp_template_course" name="sfwp_settings[template_course]">
                    <?php foreach ( $templates as $key => $label ) { ?>
                        <option value="<?php echo $key; ?>" <?php selected( $template_course, $key ); ?>><?php echo $label; ?></option>
                    <?php } ?>
                </select>
            </p>

            <br />

            <h4 style="margin: 5px 0;"><?php _e('Multiple Courses', 'wp-sendy'); ?></h4>
            <p>
                <select id="sfwp_template_courses" name="sfwp_settings[template_courses]">
                    <?php foreach ( $templates as $key => $label ) { ?>
                        <option value="<?php echo $key; ?>" <?php selected( $template_courses, $key ); ?>><?php echo $label; ?></option>
                    <?php } ?>
                </select>
            </p>

            <br />

            <p><?php printf( esc_html__( 'Available templates (%1$s) can be used to overwrite each shortcode individually: e.g.', 'wp-sendy' ), 'standard, grid, list' ); ?> <code>[sfwp id="1234,6789" template="list"]</code></p>
            <p></p>
            <?php
        }

        function course_details_render() {

            $course_details_options = array(
                'course' => __('Course Subtitle', 'wp-sendy'),
                'instructor' => __('Instructor information', 'wp-sendy'),
            );

            $course_details = ( isset ( $this->options['course_details'] ) ) ? $this->options['course_details'] : 'course';

            ?>
            <select id="sfwp_course_details" name="sfwp_settings[course_details]">
                <?php foreach ( $course_details_options as $key => $label ) { ?>
                    <option value="<?php echo $key; ?>" <?php selected( $course_details, $key ); ?>><?php echo $label; ?></option>
                <?php } ?>
            </select>
            <p><small><?php _e('This will be applied to grid and list templates. The standard template already shows both information.', 'wp-sendy'); ?></small></p>

            <?php $course_meta = ( isset ( $this->options['course_meta'] ) && $this->options['course_meta'] == '1' ) ? 1 : 0; ?>
            <p>
                <input type="checkbox" id="sfwp_course_meta" name="sfwp_settings[course_meta]" value="1" <?php echo($course_meta == 1 ? 'checked' : ''); ?>>
                <label for="sfwp_course_meta"><?php _e('Show lectures and playing time', 'wp-sendy'); ?></label>
            </p>
            <?php
        }

        function custom_css_render() {

            $custom_css_activated = ( isset ( $this->options['custom_css_activated'] ) && $this->options['custom_css_activated'] == '1' ) ? 1 : 0;
            $custom_css = ( !empty ( $this->options['custom_css'] ) ) ? $this->options['custom_css'] : '';
            ?>

            <p>
                <input type="checkbox" id="sfwp_custom_css_activated" name="sfwp_settings[custom_css_activated]" value="1" <?php echo($custom_css_activated == 1 ? 'checked' : ''); ?>>
                <label for="sfwp_custom_css_activated"><?php _e('Output custom CSS styles', 'wp-sendy'); ?></label>
            </p>
            <br />
            <textarea id="sfwp_custom_css" name="sfwp_settings[custom_css]" rows="10" cols="80" style="width: 100%;"><?php echo stripslashes($custom_css); ?></textarea>
            <p>
                <small><?php _e("Please don't use the <code>style</code> tag. Simply paste you CSS classes/definitions e.g. <code>.sfwp .sfwp-course { background-color: #333; color: #fff; }</code>", 'wp-sendy' ) ?></small>
            </p>

            <?php
        }

        function widget_text_shortcodes_render() {

            $shortcodes = ( isset ( $this->options['widget_text_shortcodes'] ) && $this->options['widget_text_shortcodes'] == '1' ) ? 1 : 0;

            ?>
            <input type="checkbox" id="sfwp_widget_text_shortcodes" name="sfwp_settings[widget_text_shortcodes]" value="1" <?php echo($shortcodes == 1 ? 'checked' : ''); ?>>
            <label for="sfwp_widget_text_shortcodes"><?php _e("Activate if your theme doesn't support shortcodes within text widgets.", 'wp-sendy'); ?></label>
            <?php
        }

        function credits_render() {

            $credits = ( isset ( $this->options['credits'] ) && $this->options['credits'] == '1' ) ? 1 : 0;

            ?>
            <input type="checkbox" id="sfwp_credits" name="sfwp_settings[credits]" value="1" <?php echo($credits == 1 ? 'checked' : ''); ?>>
            <label for="sfwp_credits"><?php _e('Activate if you love this plugin and spread it to the world!', 'wp-sendy'); ?> :-)</label>
            <?php
        }

        function developer_mode_render() {

            $developer_mode = ( isset ( $this->options['developer_mode'] ) && $this->options['developer_mode'] == '1' ) ? 1 : 0;

            ?>
            <input type="checkbox" id="sfwp_developer_mode" name="sfwp_settings[developer_mode]" value="1" <?php echo($developer_mode == 1 ? 'checked' : ''); ?>>
            <label for="sfwp_developer_mode"><?php _e('Please activate for debugging reasons only', 'wp-sendy'); ?></label>
            <?php
        }

        function debug_information_render() {

            global $wp_version;

            $enabled = '<span style="color: green;"><strong><span class="dashicons dashicons-yes"></span> ' . __('Enabled', 'wp-sendy') . '</strong></span>';
            $disabled = '<span style="color: red;"><strong><span class="dashicons dashicons-no"></span> ' . __('Disabled', 'wp-sendy') . '</strong></span>';

            ?>

            <table class="widefat sfwp-settings-table">
                <thead>
                    <tr>
                        <th width="300"><?php _e('Setting', 'wp-sendy'); ?></th>
                        <th><?php _e('Values', 'wp-sendy'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>WordPress</th>
                        <td>Version <?php echo $wp_version; ?></td>
                    </tr>
                    <tr class="alternate">
                        <th>PHP</th>
                        <td>Version <strong><?php echo phpversion(); ?></strong></td>
                    </tr>
                    <tr>
                        <th><?php printf( esc_html__( 'PHP "%1$s" extension', 'wp-sendy' ), 'cURL' ); ?></th>
                        <td>
                            <?php echo (isset ($this->curl['enabled']) && $this->curl['enabled']) ? $enabled : $disabled; ?>
                            <?php if (isset ($this->curl['version'])) echo ' (Version ' . $this->curl['version'] . ')'; ?>
                        </td>
                    </tr>
                    <tr class="alternate">
                        <th><?php _e('Cache', 'wp-sendy'); ?></th>
                        <td>
                            <?php $cache = get_option( 'sfwp_cache', sfwp_get_cache_structure() ); ?>

                            <strong><?php _e('Size', 'wp-sendy'); ?></strong><br />
                            <?php printf( esc_html__( '%1$s courses and %2$s lists.', 'wp-sendy' ), '<strong>' . sizeof( $cache['items'] ) . '</strong>', '<strong>' . sizeof( $cache['lists'] ) . '</strong>' ); ?>
                            <br /><br />
                            <strong><?php _e('Last update', 'wp-sendy'); ?></strong><br />
                            <?php echo ( ! empty ( $cache['last_update'] ) && is_numeric( $cache['last_update'] ) ) ? sfwp_get_datetime( $cache['last_update'] ) : 'N/A'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Next Cron Execution', 'wp-sendy'); ?></th>
                        <td><?php echo sfwp_get_datetime( wp_next_scheduled( 'sfwp_wp_scheduled_events' ) ); ?></td>
                    </tr>
                </tbody>
            </table>

            <p>
                <?php _e('In case one of the values above is <span style="color: red;"><strong>red</strong></span>, please get in contact with your webhoster in order to enable the missing PHP extensions.', 'wp-sendy'); ?>
            </p>

            <br />

            <p>
                <strong><?php _e('Log file', 'wp-sendy'); ?></strong><br />
                <textarea rows="5" style="width: 100%;"><?php echo get_option( 'sfwp_log', __( 'No entries yet. ', 'wp-sendy' ) ); ?></textarea>
            </p>
            <p>
                <input type="hidden" id="sfwp_reset_log" name="sfwp_settings[reset_log]" value="0" />
                <?php submit_button( 'Reset log', 'delete button-secondary', 'sfwp-reset-log-submit', false ); ?>
            </p>
            <?php
        }
        */

        function options_page()
        {
            ?>

            <div class="sfwp sfwp-settings">
                <div class="wrap">
                    <?php screen_icon(); ?>
                    <h2><?php _e('Settings', 'wp-sendy'); ?></h2>

                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <div class="meta-box-sortables ui-sortable">
                                    <form action="options.php" method="post">

                                        <?php
                                        settings_fields('sfwp_settings');
                                        sfwp_do_settings_sections('sfwp_settings');
                                        ?>

                                        <p>
                                            <?php submit_button( 'Save Changes', 'button-primary', 'submit', false ); ?>
                                        </p>

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

        /*
         * API Status field
         */
        function api_status_html() {

            $status = ( ! empty ( $this->options['api_status'] ) ) ? true : false;
            $error = ( ! empty ( $this->options['api_error'] ) ) ? $this->options['api_error'] : '';

            $message = ( $status ) ? __( 'Connected', 'wp-sendy' ) : __( 'Disconnected', 'wp-sendy' );

            if ( $status ) {
                $bgcolor = 'darkgreen';
            } elseif ( ! $status && ! empty( $error ) ) {
                $bgcolor = 'darkred';
            } else {
                $bgcolor = 'gray';
            }

            ?>
            <span style="display: inline-block; padding: 3px 6px; font-size: 12px; text-transform: uppercase; background-color: <?php echo $bgcolor; ?>; color: #fff; font-weight: 600;"><?php echo ( ! empty ( $error ) ) ? $error : $message; ?></span>
            <?php
        }

        private function check_curl() {

            if ( ( function_exists('curl_version') ) ) {

                $curl_data = curl_version();
                $version = ( isset ( $curl_data['version'] ) ) ? $curl_data['version'] : null;

                return array(
                    'enabled' => true,
                    'version' => $version
                );
            } else {
                $this->checks = false;
                return false;
            }
        }
    }
}

new SFWP_Settings();