<?php
/**
 * Easy Digital Downloads Integration
 *
 * @package     SFWP\Integrations
 * @since       1.0.0
 */


// Exit if accessed directly
if (!defined('ABSPATH')) exit;


if (!class_exists('SFWP_Easy_Digital_Downloads_Integration')) {

    class SFWP_Easy_Digital_Downloads_Integration extends SFWP_Integration
    {
        /**
         * @var string
         */
        public $slug = "edd";

        /**
         * @var string
         */
        public $name = "Easy Digital Downloads";

        /**
         * @var string
         */
        public $description = "Subscribes your Easy Digital Downloads customers.";

        /**
         *
         */
        public function add_hooks()
        {


            // TODO: Allow more positions
            //add_action('edd_purchase_form_user_info_fields', array($this, 'output_checkbox'), 1);
            //add_action('edd_payment_meta', array($this, 'save_checkbox_value'));

            //add_action('edd_complete_purchase', array($this, 'subscribe_from_edd'), 50);
        }

        public function settings_render() {

            sfwp_debug( $this->options );

            $this->settings_header_render();
            ?>
            <!-- Status -->
            <?php $this->settings_status_render(); ?>
            <tr>
                <th scope="row"><?php _e( 'Test', 'wp-sendy' ); ?></th>
                <td>
                    <input type="text" name="sfwp_integrations[<?php echo $this->slug; ?>_test]" value="" />
                </td>
            </tr>
            <!-- Lists -->
            <tr>
                <th scope="row">
                    <?php _e( 'Sendy List(s)', 'wp-sendy' ); ?>
                    <span class="sfwp-th-subtitle"><?php _e( 'Subscribe', 'wp-sendy' ); ?></span>
                </th>
                <td>
                    <ul class="sfwp-lists">
                        <?php $max = 3; ?>
                        <?php for( $i = 0; $i < 3; $i++ ) { ?>
                            <?php $list = ( ! empty( $this->options[$this->slug . '_lists']['subscribe'][$i] ) ) ? esc_html( $this->options[$this->slug . '_lists']['subscribe'][$i] ) : ''; ?>
                            <li class="sfwp-lists__item<?php if ( $i == 0 || ! empty( $list ) ) echo ' sfwp-lists__item--active'; ?>"
                                data-sfwp-list-container="true">
                                <input type="text" name="sfwp_integrations[<?php echo $this->slug; ?>_lists]['subscribe'][<?php echo $i; ?>]" value="" />
                                <?php if ( $i != ( $max - 1 ) ) { ?>
                                    <span class="sfwp-lists__action sfwp-lists__action--add" data-sfwp-add-list="true">+</span>
                                <?php } ?>
                                <?php if ( $i != 0 ) { ?>
                                    <span class="sfwp-lists__action sfwp-lists__action--remove" data-sfwp-remove-list="true">-</span>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>
                </td>
            </tr>
            <?php
            $this->settings_footer_render();
        }

        /**
         * @param array $meta
         *
         * @return array
         */
        public function save_checkbox_value($meta)
        {

            // don't save anything if the checkbox was not checked
            if (!$this->checkbox_was_checked()) {
                return $meta;
            }

            $meta['_mc4wp_optin'] = 1;
            return $meta;
        }

        /**
         * {@inheritdoc}
         *
         * @param $object_id
         *
         * @return bool
         */
        public function triggered($object_id = null)
        {

            if ($this->options['implicit']) {
                return true;
            }

            if (!$object_id) {
                return false;
            }

            $meta = edd_get_payment_meta($object_id);
            if (is_array($meta) && isset($meta['_mc4wp_optin']) && $meta['_mc4wp_optin']) {
                return true;
            }

            return false;
        }

        /**
         * @param int $payment_id The ID of the payment
         *
         * @return bool|string
         */
        public function subscribe_from_edd($payment_id)
        {

            if (!$this->triggered($payment_id)) {
                return false;
            }

            $email = (string)edd_get_payment_user_email($payment_id);
            $data = array(
                'EMAIL' => $email
            );

            // add first and last name to merge vars, if given
            $user_info = (array)edd_get_payment_meta_user_info($payment_id);

            if (!empty($user_info['first_name']) && !empty($user_info['last_name'])) {
                $data['NAME'] = $user_info['first_name'] . ' ' . $user_info['last_name'];
            }

            if (!empty($user_info['first_name'])) {
                $data['FNAME'] = $user_info['first_name'];
            }

            if (!empty($user_info['last_name'])) {
                $data['LNAME'] = $user_info['last_name'];
            }

            return $this->subscribe($data, $payment_id);
        }

        /**
         * @return bool
         */
        public function is_installed()
        {
            return class_exists( 'Easy_Digital_Downloads' );
        }
    }
}

new SFWP_Easy_Digital_Downloads_Integration();