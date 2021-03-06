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

<div class="sfwp-standard<?php if ( isset( $style ) ) echo ' sfwp-style-' . $style; ?>">

    <?php foreach ( $courses as $course ) { ?>

        <?php if ( is_string ( $course ) ) { echo '<p>' . $course . '</p>'; } else { ?>

            <div class="sfwp-course"<?php $course->the_container(); ?>>
                <a class="sfwp-course__link" href="<?php echo $course->get_url(); ?>" target="_blank" rel="nofollow" title="<?php echo $course->get_title(); ?>">
                    <span class="sfwp-course__img">
                        <img src="<?php echo $course->get_image(); ?>" alt="<?php echo $course->get_image_alt(); ?>">
                    </span>

                    <span class="sfwp-course__content">
                        <span class="sfwp-course__title"><?php echo $course->get_title(); ?></span>
                        <span class="sfwp-course__instructors"><?php echo $course->get_instructors(); ?></span>
                        <span class="sfwp-course__headline"><?php echo $course->get_headline(); ?></span>

                        <span class="sfwp-course__footer">
                            <span class="sfwp-course__price"><?php echo $course->get_price(); ?></span>
                            <span class="sfwp-course__rating"><?php $course->the_star_rating(); ?> <?php echo $course->get_rating(); ?> (<?php printf( esc_html__( '%1$s ratings', 'wp-sendy' ), $course->get_reviews() ); ?>)</span>
                            <?php if ( $course->show_meta() ) { ?>
                                <span class="sfwp-course__meta"><?php printf( esc_html__( '%1$s lectures', 'wp-sendy' ), $course->get_lectures() ); ?>, <?php printf( esc_html__( '%1$s hours', 'wp-sendy' ), $course->get_playing_time() ); ?></span>
                            <?php } ?>
                        </span>
                    </span>
                </a>
            </div>

        <?php } ?>

    <?php } ?>

</div>
