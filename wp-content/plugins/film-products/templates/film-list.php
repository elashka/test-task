<?php
/**
* The Template for displaying [film_list] shortcode
* This template can be overridden by copying it to yourtheme/fp-templates/film-list.php.
*
* @version    1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}?>
<article <?php post_class(); ?>>
    <a href="<?php the_permalink(); ?>">
        <?php if( has_post_thumbnail() ):?>
            <?php the_post_thumbnail( );?>
        <?php endif;?>
        <?php the_title( '<h3>', '</h3>' );?>
    </a>
    <?php $subtitle = get_post_meta( $post->ID, 'film_subtitle', true );?>
    <?php the_excerpt(); ?>
    <?php do_action( 'fp_buy_now' );?>
</article>
