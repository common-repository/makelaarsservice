<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>

	<section id="primary" class="content-area nl-pnt-ms-content-area">
		<main id="main" class="site-main" role="main">
            <h2><?php _e( 'Objecten', 'makelaarsservice' ); ?></h2>
            <div class="columns is-multiline">
                <?php
                while ( have_posts() ) {
                    the_post();
                    $city = get_the_terms( $post->ID, 'property_city' );
                    
                ?>
                <div class="column is-half-tablet is-one-quarter-widescreen">
                    <div class="card">
                        
                        <div class="card-image" style="background: <?php echo (get_post_meta( $post->ID, 'ms_property_sold', true) == 'true') ? "url('" . plugin_dir_url(__FILE__) . "/img/img_verkocht_sides.png') no-repeat 0% 0%/225px, " : "" ?> url('<?php echo get_the_post_thumbnail_url( $post->ID ); ?>') center/cover;"></div>
                            
                        <div class="card-content">
                            <p>
                                <a href="<?php the_permalink(); ?>"><strong class='nl-pnt-ms-main-text'><?php echo the_title(); ?></strong></a>
                            </p>

                            <p>
                                <span><strong><?php echo get_post_meta( $post->ID, 'property_label_before', true ); ?></strong></span>
                                <span>€<?php echo number_format( get_post_meta( $post->ID, 'property_price', true ), ( substr( get_post_meta( $post->ID, 'property_price', true ), -2 ) == '00' ? 0 : 2 ), ',', '.' ); ?></span>                          
                                <span><strong><?php echo get_post_meta( $post->ID, 'property_label', true ); ?></strong></span>
                            </p>
                            
                            <div class="columns">
                                <div class="column">
                                    <span class="inline-block"><strong><?php echo get_post_meta( $post->ID, 'property_size', true ); ?>m2</strong></span>
                                    <?php _e( 'Woon opp.', 'makelaarsservice' ); ?>
                                </div>
                                <div class="column">
                                    <span class="inline-block"><strong><?php echo get_post_meta( $post->ID, 'property_lot_size', true ); ?>m2</strong></span>
                                    <?php _e( 'Perceel opp.', 'makelaarsservice' ); ?>
                                </div>
                                <div class="column">
                                    <span class="inline-block"><strong><?php echo get_post_meta( $post->ID, 'property_bedrooms', true ); ?></strong></span>
                                    <?php _e( 'Slaapkamers', 'makelaarsservice' ); ?>
                                </div>
                                
                            </div>

                            
                        </div>                        
                    </div>
                </div>

                <?php
                }
                ?>
                
            </div><!-- .row -->

		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();
