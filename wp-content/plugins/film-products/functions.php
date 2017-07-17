<?php
/**
 * @file functions.php
 *
 * Implements functions for plugin.
 */


/**
 * Retrieves a template part.
 * @param $slug
 * @param null $name
 * @param bool $load
 * @return string
 */
function fp_get_template_part( $slug, $name = null, $load = true ) {
    // Execute code for this part
    do_action( 'get_template_part_' . $slug, $slug, $name );

    // Setup possible parts
    $templates = array();
    if ( isset( $name ) )
        $templates[] = $slug . '-' . $name . '.php';
    $templates[] = $slug . '.php';

    // Allow template parts to be filtered
    $templates = apply_filters( 'fp_get_template_part', $templates, $slug, $name );

    // Return the part that is found
    return fp_locate_template( $templates, $load, false );
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the STYLESHEETPATH before TEMPLATEPATH so that themes which
 * inherit from a parent theme can just overload one file. If the template is
 * not found in either of those, it looks in the theme-compat folder last.
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @param bool $require_once Whether to require_once or require. Default true.
 *                            Has no effect if $load is false.
 * @return string The template filename if one is located.
 */
function fp_locate_template( $template_names, $load = false, $require_once = true ) {
    // No file found yet
    $located = false;

    // Try to find a template file
    foreach ( (array) $template_names as $template_name ) {

        // Continue if template is empty
        if ( empty( $template_name ) )
            continue;

        // Trim off any slashes from the template name
        $template_name = ltrim( $template_name, '/' );

        // Check child theme first
        if ( file_exists( trailingslashit( get_stylesheet_directory() ) . 'fp-templates/' . $template_name ) ) {
            $located = trailingslashit( get_stylesheet_directory() ) . 'fp-templates/' . $template_name;
            break;

            // Check parent theme next
        } elseif ( file_exists( trailingslashit( get_template_directory() ) . 'fp-templates/' . $template_name ) ) {
            $located = trailingslashit( get_template_directory() ) . 'fp-templates/' . $template_name;
            break;

            // Check theme compatibility last
        } elseif ( file_exists(  FP_TEMPLATE_PATH . $template_name ) ) {
            $located = FP_TEMPLATE_PATH . $template_name;
            break;
        }
    }

    if ( ( true == $load ) && ! empty( $located ) )
        load_template( $located, $require_once );

    return $located;
}

