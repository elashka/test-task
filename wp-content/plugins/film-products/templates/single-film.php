<?php
/**
 * The Template for displaying all single films
 *
 * This template can be overridden by copying it to yourtheme/fp-templates/single-film.php.
 *
 * @version     1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();
?>
    <div class="wrap">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

                <?php do_action( 'fp_before_main_content' ); ?>

                <?php while (have_posts()) : the_post(); ?>

                    <?php fp_get_template_part('content', 'single-film'); ?>

                <?php endwhile; // end of the loop. ?>

                <?php do_action( 'fp_after_main_content' ); ?>
            </main><!-- #main -->
        </div><!-- #primary -->
        <?php get_sidebar(); ?>
    </div>
<?php get_footer();

