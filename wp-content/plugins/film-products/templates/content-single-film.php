<?php
/**
 * The Template for displaying all single film content.
 *fp-templates
 * This template can be overridden by copying it to yourtheme/fp-templates/content-single-film.php.
 *
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<article <?php post_class(); ?>>
    <?php if( has_post_thumbnail() ):?>
        <?php the_post_thumbnail( );?>
    <?php endif;?>
    <?php the_title( '<h1>', '</h1>' );?>
    <?php $subtitle = get_post_meta( $post->ID, 'film_subtitle', true );?>
    <?php if ( !empty( $subtitle )):?>
        <h3><?php echo $subtitle;?></h3>
    <?php endif;?>
    <?php the_content();?>
    <?php do_action( 'fp_buy_now' );?>
</article>
