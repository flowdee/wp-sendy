<?php
/**
 * Functions
 *
 * @package     SFWP\Functions
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/*
 * Build course objects from result arrays
 */
function sfwp_get_course_objects_from_array( $items = array(), $args = array() ) {

    $objects = array();

    if ( sizeof( $items ) > 0 ) {

        foreach ( $items as $item ) {
            $objects[] = ( is_array( $item ) ) ? new SFWP_Course( $item, $args ) : $item;
        }
    }

    return $objects;
}

/*
 * Cache structure
 */
function sfwp_get_cache_structure() {
    return array(
        'items' => array(),
        'lists' => array(),
        'last_update' => 0
    );
}

/*
 * Update cache
 */
function sfwp_update_cache( $items, $key = false ) {

    $cache = get_option( 'sfwp_cache', sfwp_get_cache_structure() );

    // List of courses
    if ( $key ) {

        $serialized_key = serialize( $key );

        $cache['lists'][$serialized_key] = $items;

    // Single or multiple courses
    } else {

        if ( isset ( $items['id'] ) ) {
            $cache['items'][$items['id']] = $items;
        }
    }

    update_option( 'sfwp_cache', $cache );
}

/*
 * Get cache
 */
function sfwp_get_cache( $key ) {

    $cache = get_option( 'sfwp_cache', sfwp_get_cache_structure() );

    //sfwp_debug( $cache );

    // List of items
    if ( is_array( $key ) ) {

        $serialized_key = serialize( $key );

        if ( isset ( $cache['lists'][$serialized_key] ) ) {
            return $cache['lists'][$serialized_key];
        }

    // Single item
    } else {

        if ( isset ( $cache['items'][$key] ) ) {
            return $cache['items'][$key];
        }
    }

    return false;
}

/*
 * Build cache key
 */
function sfwp_get_cache_key( $args ) {
    return ( is_numeric( $args ) ) ? $args : serialize( $args );
}

/*
 * Delete cache
 */
function sfwp_delete_cache() {
    sfwp_addlog( '*** CACHE MANUALLY DELETED ***' );
    delete_option( 'sfwp_cache' );
}

/*
 * Cleanup cache event
 */
function sfwp_cleanup_cache() {

    $cache = get_option( 'sfwp_cache', sfwp_get_cache_structure() );

    $last_update = ( isset ( $cache['last_update'] ) ) ? $cache['last_update'] : 0;

    $debug = false;

    if ( ( time() - $last_update ) > ( 7 * 60 * 60 * 60 ) || $debug ) {

        $cache = sfwp_get_cache_structure();
        $cache['last_update'] = $last_update;

        // Reset cache
        update_option( 'sfwp_cache', $cache );
    }
}

/*
 * Update cache event
 */
function sfwp_update_cache_event() {

    $options = sfwp_get_options();

    $cache = get_option( 'sfwp_cache', sfwp_get_cache_structure() );

    $cache_duration = ( ! empty ( $options['cache_duration'] ) ) ? intval( $options['cache_duration'] ) : 1440;
    $last_update = ( isset ( $cache['last_update'] ) ) ? intval( $cache['last_update'] ) : 0;

    $debug = false;

    if ( ( time() - $last_update ) > ( $cache_duration * 60 ) || $debug ) {

        $debug_start_time = microtime( true );

        sfwp_addlog( '*** START *** UPDATING CACHE ***' );

        // Single items
        $cache['items'] = sfwp_bulk_update_items( $cache['items'] );

        // Lists
        $cache['lists'] = sfwp_bulk_update_lists( $cache['lists'] );

        // Update timestamp
        $cache['last_update'] = time();

        // Update cache
        update_option( 'sfwp_cache', $cache );

        $debug_execution_time = microtime(true) - $debug_start_time;

        sfwp_addlog( '*** END *** UPDATING CACHE *** EXECUTION TIME: ' . $debug_execution_time . ' SECONDS ***' );
    }
}

/*
 * Bulk update items via API
 */
function sfwp_bulk_update_items( $items ) {

    sfwp_addlog( 'BULK UPDATING ITEMS' );

    $i = 1;

    foreach ( $items as $id => $data ) {

        if ( is_numeric( $id ) ) {

            // Go easy on API and hold on after every 10 items
            if ($i > 0 && $i % 10 == 0) {
                sfwp_addlog( 'UPDATING PAUSED AFTER ' . $i . ' ITEMS' );
                sleep(5);
            }

            // Fetch course
            $course = sfwp_get_course_from_api( $id );

            if ( is_array( $course ) )
                $items[$id] = $course;

            // Update item count
            $i++;
        }
    }

    sfwp_addlog( 'BULK UPDATED ' . ( $i - 1 ) . ' ITEMS' );

    return $items;
}

/*
 * Bulk update lists via API
 */
function sfwp_bulk_update_lists( $lists ) {

    sfwp_addlog( 'BULK UPDATING LISTS' );

    $i = 1;

    foreach ( $lists as $id => $items ) {

        $args = unserialize( $id );

        if ( sizeof( $args ) > 0 ) {

            // Go easy on API and hold on after every 5 lists
            if ($i > 0 && $i % 5 == 0) {
                sfwp_addlog( 'UPDATING PAUSED AFTER ' . $i . ' LISTS' );
                sleep(5);
            }

            // Fetch courses
            $courses = sfwp_get_courses_from_api( $args );

            if ( is_array( $courses ) )
                $lists[$id] = $courses;

            // Update list count
            $i++;
        }
    }

    sfwp_addlog( 'BULK UPDATED ' . ( $i - 1 ) . ' LISTS' );

    return $lists;
}

/*
 * Handle scheduled events
 */
function sfwp_scheduled_events() {

    // Cleanup cache
    sfwp_cleanup_cache();

    // Handle cache updates
    sfwp_update_cache_event();
}
add_action('sfwp_wp_scheduled_events', 'sfwp_scheduled_events');

/*
 * Get courses
 */
function sfwp_get_courses( $atts ) {

    if ( ! function_exists('curl_version') )
        return '<p style="color: darkorange; font-weight: bold;">' . __( 'Please activate PHP curl in order to display Udemy courses.', 'wp-sendy' ) . '</p>';

    // Defaults
    $args = array();
    $courses = array();

    // IDs
    if ( isset ( $atts['id'] ) ) {

        $course_ids = explode(',', str_replace( array( ' ', ';'), array( '', ','), sanitize_text_field( $atts['id'] ) ) );

        foreach ( $course_ids as $id ) {

            $course_cache = sfwp_get_cache( $id );

            // Cache available
            if ( $course_cache ) {

                $courses[] = $course_cache;

                // Cache not available, fetch from API
            } else {

                $course = sfwp_get_course_from_api( $id );
                $courses[] = $course;

                if ( is_array( $course ) ) {
                    sfwp_update_cache( $course );
                }
            }
        }

    // Lists
    } else {

        // Page size
        if ( isset ( $atts['items'] ) && is_numeric( $atts['items'] ) )
            $args['page_size'] = $atts['items'];

        // Language
        if ( isset ( $atts['lang'] ) )
            $args['language'] = $atts['lang'];

        // Order
        if ( isset ( $atts['orderby'] ) ) {

            if ( 'sales' === $atts['orderby'] )
                $orderby = 'best_seller';

            if ( 'date' === $atts['orderby'] )
                $orderby = 'enrollment';

            if ( 'trends' === $atts['orderby'] )
                $orderby = 'trending';

            if ( ! empty ( $orderby ) )
                $args['ordering'] = $orderby;
        }

        // Categories
        if ( isset ( $atts['category'] ) ) {

            $category = sfwp_cleanup_category_name ( sanitize_text_field( $atts['category'] ) );
            $categories = sfwp_get_categories();

            if ( in_array( $category, $categories ) ) {
                $args['category'] = $category;
            } else {
                $args['subcategory'] = $category;
            }
        }

        // Search
        if ( isset ( $atts['search'] ) )
            $args['search'] = sanitize_text_field( $atts['search'] );

        // Get courses
        if ( sizeof( $args ) > 0 ) {

            $courses_cache = sfwp_get_cache( $args );

            // Cache available
            if ( $courses_cache ) {
                $courses = $courses_cache;
            } else {
                $courses = sfwp_get_courses_from_api($args);

                if ( is_array( $courses ) ) {
                    sfwp_update_cache( $courses, $args );
                }
            }
        }
    }

    return $courses;
}

/*
 * Display courses
 */
function sfwp_display_courses( $courses = array(), $args = array() ) {

    //sfwp_debug($courses);

    $options = sfwp_get_options();

    // Defaults
    $type = ( isset ( $args['type'] ) ) ? $args['type'] : 'single';
    $grid = ( isset ( $args['grid'] ) && is_numeric( $args['grid'] ) ) ? $args['grid'] : '3';

    if ( isset ( $args['style'] ) )
        $style = $args['style'];

    // Prepare courses
    $courses = sfwp_get_course_objects_from_array( $courses, $args );

    // Template
    $template_course = ( isset ( $options['template_course'] ) ) ? $options['template_course'] : 'standard';
    $template_courses = ( isset ( $options['template_courses'] ) ) ? $options['template_courses'] : 'list';

    if ( isset ( $args['template'] ) ) {
        $template = str_replace(' ', '', $args['template']);
    } elseif ( 'widget' === $type ) {
        $template = 'widget';
    } else {
        $template = ( sizeof( $courses ) > 1 ) ? $template_courses : $template_course;
    }

    // Get template file
    $file = sfwp_get_template_file( $template, $type );

    // Output
    ob_start();

    echo '<div class="ufwp">';

    if ( file_exists( $file ) ) {
        include( $file );
    } else {
        _e('Template not found.', 'wp-sendy');
    }

    echo '</div>';

    $output = ob_get_clean();

    return $output;
}

/*
 * Get template file
 */
function sfwp_get_template_file( $template, $type ) {

    $template_file = SFWP_DIR . 'templates/' . $template . '.php';

    $template_file = apply_filters( 'sfwp_template_file', $template_file, $template, $type );

    if ( file_exists( $template_file ) )
        return $template_file;

    return ( 'widget' === $type ) ? SFWP_DIR . 'templates/widget.php' : SFWP_DIR . 'templates/standard.php';
}

/*
 * Main categories
 */
function sfwp_get_categories() {
    return array('Academics','Business','Crafts-and-Hobbies','Design','Development','Games','Health-and-Fitness','Humanities','IT-and-Software','Language','Lifestyle','Marketing','Math-and-Science','Music','Office-Productivity','Other','Personal-Development','Photography','Social-Science','Sports','Teacher-Training','Technology','Test','Test-Prep');
}

/**
 * Check content if scripts must be loaded
 */
function sfwp_has_plugin_content() {

    global $post;

    if( ( is_a( $post, 'WP_Post' ) && ( has_shortcode( $post->post_content, 'ufwp') || has_shortcode( $post->post_content, 'udemy') ) ) ) {
        return true;
    }

    return false;
}