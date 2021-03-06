<?php
/**
 * Widget: Search
 *
 * @package     SFWP\WidgetSearch
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'SFWP_Search_Widget' ) ) {

    /**
     * Adds SFWP_Search widget.
     */
    class SFWP_Search_Widget extends WP_Widget {

        protected static $did_script = false;

        /**
         * Register widget with WordPress.
         */
        function __construct() {
            parent::__construct(
                'sfwp_search_widget', // Base ID
                __( 'SFWP - Search', 'wp-sendy' ), // Name
                array( 'description' => __( 'Searching for courses by keyword or category.', 'wp-sendy' ), ) // Args
            );

            add_action('wp_enqueue_scripts', array( $this, 'scripts' ) );
        }

        /**
         * Front-end display of widget.
         *
         * @see WP_Widget::widget()
         *
         * @param array $args     Widget arguments.
         * @param array $instance Saved values from database.
         */
        public function widget( $args, $instance ) {

            echo $args['before_widget'];

            if ( ! empty( $instance['title'] ) ) {
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            }

            if ( ! empty ( $instance['keywords'] ) ) {

                $shortcode_atts = array(
                    'type' => 'widget'
                );

                // Keywords
                if ( ! empty ( $instance['keywords'] ) )
                    $shortcode_atts['search'] = $instance['keywords'];

                // Category/subcategory TODO
                //if ( ! empty ( $instance['category'] ) )
                    //$shortcode_atts['category'] = $instance['category'];

                // Items
                if ( ! empty ( $instance['items'] ) && is_numeric( $instance['items'] ) )
                    $shortcode_atts['items'] = $instance['items'];

                // Lang
                if ( ! empty ( $instance['lang'] ) )
                    $shortcode_atts['lang'] = $instance['lang'];

                // Orderby
                if ( ! empty ( $instance['orderby'] ) )
                    $shortcode_atts['orderby'] = $instance['orderby'];

                // Template
                if ( ! empty ( $instance['template_custom'] ) ) {
                    $shortcode_atts['template'] = $instance['template_custom'];
                } elseif ( ! empty ( $instance['template'] ) ) {
                    $shortcode_atts['template'] = $instance['template'];
                }

                // Style
                if ( ! empty ( $instance['style'] ) )
                    $shortcode_atts['style'] = $instance['style'];

                // Execute Shortcode
                sfwp_widget_do_shortcode( $shortcode_atts );

            } else {
                _e( 'Keyword missing.', 'wp-sendy' );
            }

            echo $args['after_widget'];
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        public function form( $instance ) {

            $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
            $keywords = ! empty( $instance['keywords'] ) ? $instance['keywords'] : '';
            $items = ! empty( $instance['items'] ) ? $instance['items'] : '3';
            $lang = ! empty( $instance['lang'] ) ? $instance['lang'] : '';
            $orderby = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'date';
            $template = ! empty( $instance['template'] ) ? $instance['template'] : 'widget';
            $template_custom = ! empty( $instance['template_custom'] ) ? $instance['template_custom'] : '';
            $style = ! empty( $instance['style'] ) ? $instance['style'] : '';

            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ), 'wp-sendy' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'keywords' ) ); ?>"><?php _e( 'Keywords:', 'wp-sendy' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'keywords' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'keywords' ) ); ?>" type="text" value="<?php echo esc_attr( $keywords ); ?>">
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>"><?php _e( 'Items:', 'wp-sendy' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'items' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'items' ) ); ?>" type="number" value="<?php echo esc_attr( $items ); ?>">
            </p>

            <?php
            $orderby_options = array(
                'sales' => __('Sales', 'wp-sendy'),
                'date' => __('Date', 'wp-sendy'),
                'trends' => __('Trends', 'wp-sendy')
            );
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php _e( 'Order by:', 'wp-sendy' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
                    <?php foreach ( $orderby_options as $key => $label ) { ?>
                        <option value="<?php echo $key; ?>" <?php selected( $orderby, $key ); ?>><?php echo $label; ?></option>
                    <?php } ?>
                </select>
            </p>

            <?php
            $lang_options = array(
                '' => __('All', 'wp-sendy'),
                'en' => __('English', 'wp-sendy'),
                'fr' => __('French', 'wp-sendy'),
                'de' => __('German', 'wp-sendy'),
                'it' => __('Italian', 'wp-sendy'),
                'es' => __('Spanish', 'wp-sendy'),
                'ru' => __('Russian', 'wp-sendy')
            );
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'lang' ) ); ?>"><?php _e( 'Language:', 'wp-sendy' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'lang' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'lang' ) ); ?>">
                    <?php foreach ( $lang_options as $key => $label ) { ?>
                        <option value="<?php echo $key; ?>" <?php selected( $lang, $key ); ?>><?php echo $label; ?></option>
                    <?php } ?>
                </select>
            </p>

            <?php
            $templates = array(
                'widget' => __('Standard', 'wp-sendy'),
                'widget_small' => __('Small', 'wp-sendy')
            );
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>"><?php _e( 'Template:', 'wp-sendy' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'template' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'template' ) ); ?>">
                    <?php foreach ( $templates as $key => $label ) { ?>
                        <option value="<?php echo $key; ?>" <?php selected( $template, $key ); ?>><?php echo $label; ?></option>
                    <?php } ?>
                </select>
                <br />
                <small>
                    <?php _e( 'The templates listed above are optimized for widgets.', 'wp-sendy' ); ?>
                </small>
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'template_custom' ) ); ?>"><?php _e( 'Custom Template:', 'wp-sendy' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'template_custom' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'template_custom' ) ); ?>" type="text" value="<?php echo esc_attr( $template_custom ); ?>">
                <br />
                <small>
                    <?php _e( 'You can use another template by entering the the name: e.g. <strong>my_widget</strong>.', 'wp-sendy' ); ?>
                </small>
            </p>

            <?php
            $styles = array(
                '' => __('Standard', 'wp-sendy'),
                'clean' => __('Clean', 'wp-sendy'),
                'light' => __('Light', 'wp-sendy'),
                'dark' => __('Dark', 'wp-sendy')
            );
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php _e( 'Style:', 'wp-sendy' ); ?></label>
                <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>">
                    <?php foreach ( $styles as $key => $label ) { ?>
                        <option value="<?php echo $key; ?>" <?php selected( $style, $key ); ?>><?php echo $label; ?></option>
                    <?php } ?>
                </select>
            </p>

            <?php
        }

        /**
         * Sanitize widget form values as they are saved.
         *
         * @see WP_Widget::update()
         *
         * @param array $new_instance Values just sent to be saved.
         * @param array $old_instance Previously saved values from database.
         *
         * @return array Updated safe values to be saved.
         */
        public function update( $new_instance, $old_instance ) {
            $instance = array();

            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['keywords'] = ( ! empty( $new_instance['keywords'] ) ) ? strip_tags( $new_instance['keywords'] ) : '';
            $instance['items'] = ( ! empty( $new_instance['items'] ) ) ? strip_tags( $new_instance['items'] ) : '';
            $instance['lang'] = ( ! empty( $new_instance['lang'] ) ) ? strip_tags( $new_instance['lang'] ) : '';
            $instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) ? strip_tags( $new_instance['orderby'] ) : '';
            $instance['template'] = ( ! empty( $new_instance['template'] ) ) ? strip_tags( $new_instance['template'] ) : '';
            $instance['template_custom'] = ( ! empty( $new_instance['template_custom'] ) ) ? strip_tags( $new_instance['template_custom'] ) : '';
            $instance['style'] = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : '';

            return $instance;
        }

        /**
         * Enqueue scripts
         */
        public function scripts() {

            if( !self::$did_script && is_active_widget(false, false, $this->id_base, true) ) {
                sfwp_load_scripts();
                self::$did_script = true;
            }
        }
    }

}