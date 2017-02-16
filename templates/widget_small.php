<?php
/*
 * Standard template
 *
 * @package Udemy
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Check if course was forwarded
if ( ! isset ( $courses ) )
    return;
?>

<div class="sfwp-widget-small<?php if ( isset( $style ) ) echo ' sfwp-style-' . $style; ?>">

    <?php foreach ( $courses as $course ) { ?>

        <?php if ( is_string ( $course ) ) { echo '<p>' . $course . '</p>'; } else { ?>

            <div class="sfwp-course"<?php $course->the_container(); ?>>
                <a class="sfwp-course__link" href="<?php echo $course->get_url(); ?>" target="_blank" rel="nofollow" title="<?php echo $course->get_title(); ?>">
                    <span class="sfwp-course__img" style="background-image: url('<?php echo $course->get_image('widget_small'); ?>');"></span>

                    <span class="sfwp-course__content">
                        <span class="sfwp-course__title"><?php echo $course->get_title(); ?></span>

                        <span class="sfwp-course__footer">
                            <span class="sfwp-course__price"><?php echo $course->get_price(); ?></span>
                            <span class="sfwp-course__rating"><?php $course->the_star_rating(); ?></span>
                        </span>
                    </span>
                </a>
            </div>

        <?php } ?>

    <?php } ?>

</div>
