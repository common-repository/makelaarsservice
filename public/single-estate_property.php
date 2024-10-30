<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */
// Set default options
$option_defaults = array(
	'color' => '#2babe2',
);

// Set labels for garage property
$labels_garage = array(
	'attached_stone' 			=> __( 'Aanbouw steen', 'makelaarsservice' ),
	'option' 					=> __( 'Garage mogelijk', 'makelaarsservice' ),
	'attached_wood' 			=> __( 'Aanbouw hout', 'makelaarsservice' ),
	'detached_stone' 			=> __( 'Vrijstaand steen', 'makelaarsservice' ),
	'detached_wood' 			=> __( 'Vrijstaand hout', 'makelaarsservice' ),
	'indoor' 					=> __( 'Inpandig', 'makelaarsservice' ),
	'garagebox' 				=> __( 'Garagebox', 'makelaarsservice' ),
	'basement_car_park' 		=> __( 'Parkeerkelder', 'makelaarsservice' ),
	'carport' 					=> __( 'Carport', 'makelaarsservice' ),
	'parking_place' 			=> __( 'Parkeerplaats', 'makelaarsservice' ),
	'garage_with_carport' 		=> __( 'Garage met carport', 'makelaarsservice' ),
);

// Set labels for garden property
$labels_garden = array(
	'back_yard' 				=> __( 'Achtertuin', 'makelaarsservice' ),
	'front_yard'				=> __( 'Voortuin', 'makelaarsservice' ),
	'side_yard'					=> __( 'Zijtuin', 'makelaarsservice' ),
	'garden_around' 			=> __( 'Tuin rondom', 'makelaarsservice' ),
	'patio_atrium'				=> __( 'Patio', 'makelaarsservice' ),
	'open_area'					=> __( 'Plaats', 'makelaarsservice' ),
	'sun_terrace'				=> __( 'Zonterras', 'makelaarsservice' ),
);

// Set labels for insulation property
$labels_insulation = array(
	'roof'						=> __( 'Dakisolatie', 'makelaarsservice' ),
	'wall'						=> __( 'Muurisolatie', 'makelaarsservice' ),
	'floor'						=> __( 'Vloerisolatie', 'makelaarsservice' ),
	'full'						=> __( 'Volledig geïsoleerd', 'makelaarsservice' ),
	'cavity_wall'				=> __( 'Spouwmuur', 'makelaarsservice' ),
	'anchor_free_cavity_wall' 	=> __( 'Ankerloze spouwmuur', 'makelaarsservice' ),
	'eco_construction'			=> __( 'Ecobouw', 'makelaarsservice' ),
	'insulated_glazing'			=> __( 'Dubbelglas', 'makelaarsservice' ),
	'partly_insulated_glazing'	=> __( 'Gedeeltelijk dubbelglas', 'makelaarsservice' ),
	'secondary_glazing'			=> __( 'Voorzetramen', 'makelaarsservice' ),
);

// Get plugin options from database
$options = get_option( 'makelaarsservice-settings-options', $option_defaults );

get_header(); ?>
	<style>
	.card .card-header {
		background-color: <?php echo $options['color']; ?>
	}

	.nl-pnt-ms-main-text, .icon i {
		color: <?php echo $options['color']; ?>
	}
	</style>

	<section id="primary" class="content-area nl-pnt-ms-content-area">
		<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) : the_post();

			$city = get_the_terms( $post->ID, 'property_city' );
			$url = get_the_post_thumbnail_url( $post->ID );
			$agent = get_post( get_post_meta( $post->ID, 'property_agent', true ) );
			$agent_city = get_the_terms( $agent->ID, 'property_city_agent' );

			$images = get_posts(array(
				'post_parent'		=> $post->ID,
				'post_type'			=> 'attachment'
			));
			
			$count = 0;
			?>
			<?php echo ( get_post_meta( $post->ID, 'ms_property_sold', true) == 'true' ) ? '<img src="' . plugin_dir_url(__FILE__) . '/img/img_verkocht_bg_rood.png" width="200px"></img>' : ''; ?>
			<article id="post-<?php the_ID(); ?>">
				<div id="ms-property-carousel" class="carousel" <?php if( count( $images ) > 1 ) { echo 'data-infinite="true"'; } ?> >
					<?php foreach($images as $image) : ?>
						<div class="item-<?php echo $count; ?>">
							<img src="<?php echo wp_get_attachment_url($image->ID); ?>" class="" alt="...">
						</div>

					<?php 
					$count++;
					endforeach; 
					?>
				</div>

				<h1>
					<?php the_title(); ?>
				</h1>
				

				<div class="columns">
					<div class="column">
						<div id="nl-pnt-ms-card-desc" class="card">
							<header class="card-header">
								<p class="card-header-title"><?php _e( 'Beschrijving', 'makelaarsservice' ); ?></p>	
							</header>
						
							<div class="card-content">
								<div class="content">
									<?php the_content(); ?>
								</div>
							</div>
						</div>
					</div>

					<div class="column is-one-third">
						<div id="card-properties" class="card">
							<header class="card-header">
								<p class="card-header-title"><?php _e( 'Eigenschappen', 'makelaarsservice' ); ?></p>
							</header>

							<div class="card-content">
								<div class="content">
									<ul>
										<li class="">
											<strong><?php _e( 'Adres', 'makelaarsservice' ); ?>:</strong>
											<?php echo get_post_meta( $post->ID, 'property_address', true ); ?>
										</li>

										<li class="">
											<strong><?php _e( 'Postcode', 'makelaarsservice' ); ?>:</strong>
											<?php echo get_post_meta( $post->ID, 'property_zip', true ); ?>
										</li>

										<li class="">
											<strong><?php _e( 'Plaats', 'makelaarsservice' ); ?>:</strong>
											<?php echo $city[0]->name; ?>
										</li>

										<li class="">
											<strong><?php echo get_post_meta( $post->ID, 'property_label_before', true ); ?></strong>
											
											€<?php echo number_format( get_post_meta( $post->ID, 'property_price', true ), ( substr( get_post_meta( $post->ID, 'property_price', true ), -2 ) == '00' ? 0 : 2 ), ',', '.' ); ?>
											<strong><?php echo get_post_meta( $post->ID, 'property_label', true ); ?></strong>
										</li>

										<li class="">
											<strong><?php _e( 'Slaapkamers', 'makelaarsservice' ); ?>:</strong>
											<?php echo get_post_meta( $post->ID, 'property_bedrooms', true ); ?>
										</li>

										<li class="">
											<strong><?php _e( 'Bouwjaar', 'makelaarsservice' ); ?>:</strong>
											<?php echo date( "Y", strtotime( get_post_meta( $post->ID, 'property-year', true ) ) ); ?>
										</li>

										<li class="">
											<strong><?php _e( 'Woonoppervlakte', 'makelaarsservice' ); ?>:</strong>
											<?php echo get_post_meta( $post->ID, 'property_size', true ); ?>m2
										</li>

										<li class="">
											<strong><?php _e( 'Perceeloppervlakte', 'makelaarsservice' ); ?>:</strong>
											<?php echo get_post_meta( $post->ID, 'property_lot_size', true ); ?>m2
										</li>

										<li class="">
											<strong><?php _e( 'Energielabel', 'makelaarsservice' ); ?>:</strong>
											<?php echo get_post_meta( $post->ID, 'ms_property_energy_label', true ); ?>
										</li>

										<li class="">
											<strong><?php _e( 'Garage', 'makelaarsservice' ); ?>:</strong>
											<?php echo $labels_garage[get_post_meta( $post->ID, 'property-garage', true )]; ?>
										</li>

										<li class="">
											<strong><?php _e( 'Tuin', 'makelaarsservice' ); ?>:</strong>
											<?php echo $labels_garden[get_post_meta( $post->ID, 'ms_property_garden', true )]; ?>
										</li>

										<li>
											<strong><?php _e( 'Isolatie', 'makelaarsservice' ); ?>:</strong>
											<?php echo $labels_insulation[get_post_meta( $post->ID, 'ms_property_insulation', true )]; ?>
										</li>

										<li class="">
											<strong><?php _e( 'Nieuwbouw', 'makelaarsservice' ); ?>:</strong>
											<?php echo ( get_post_meta( $post->ID, 'ms_property_energy_label', true ) == 'yes' ? __( 'Ja', 'makelaarsservice' ) : __( 'Nee', 'makelaarsservice' ) ); ?>
										</li>

									</ul>
								</div>
							</div>
						</div>

						<div id="card-agent" class="card">
							<header class="card-header">
								<p class="card-header-title"><?php echo $agent->post_title; ?></p>
							</header>

							<div class="card-content">
								<div class="content">
									<ul>
										<li>
											<span class="icon"><i class="fa fa-building"></i></span>	
											<?php echo $agent_city[0]->name; ?>
										</li>
										<li>
											<span class="icon"><i class="fa fa-phone"></i></span>	
											<?php echo get_post_meta( $agent->ID, 'agent_phone', true ); ?>
										</li>
										<li>
											<span class="icon"><i class="fa fa-envelope"></i></span>	
											<?php echo get_post_meta( $agent->ID, 'agent_email', true ); ?>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</article><!-- #post-## -->
			<?php


		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();
