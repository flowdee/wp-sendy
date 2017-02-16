<?php
/*
 * Grid template
 *
 * @package Udemy
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Check if course was forwarded
if ( ! isset ( $courses ) )
    return;
?>

<div class="sfwp-grid<?php if ( isset( $style ) ) echo ' sfwp-style-' . $style; ?><?php if ( isset( $grid ) ) echo ' sfwp-grid--col-' . $grid; ?>">

    <?php foreach ( $courses as $course ) { ?>

        <?php if ( is_string ( $course ) ) continue; ?>

        <div class="sfwp-grid__item">

            <div class="sfwp-course"<?php $course->the_container(); ?>>
                <a class="sfwp-course__link" href="<?php echo $course->get_url(); ?>" target="_blank" rel="nofollow" title="<?php echo $course->get_title(); ?>">
                    <span class="sfwp-course__img">
                        <img src="<?php echo $course->get_image(); ?>" alt="<?php echo $course->get_image_alt(); ?>">
                    </span>

                    <span class="sfwp-course__content">
                        <span class="sfwp-course__title"><?php echo $course->get_title(); ?></span>
                        <span class="sfwp-course__details"><?php echo $course->get_details(); ?></span>
                        <span class="sfwp-course__rating"><?php $course->the_star_rating(); ?> <?php echo $course->get_rating(); ?></span>
                        <span class="sfwp-course__price"><?php echo $course->get_price(); ?></span>
                    </span>
                </a>
            </div>
        </div>

    <?php } ?>
</div>