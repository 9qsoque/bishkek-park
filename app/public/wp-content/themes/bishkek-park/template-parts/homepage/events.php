<?php
/**
 * Homepage "Мероприятия" section, pulled from the bp_event post type.
 * Edit/add events under WP Admin -> Мероприятия.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bp_events_query = new WP_Query(
	array(
		'post_type'      => 'bp_event',
		'posts_per_page' => 4,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( ! $bp_events_query->have_posts() ) {
	return;
}

// Fallback gradients for events without a featured image, cycled by index.
$bp_event_gradients = array(
	'linear-gradient(135deg, #2b1330 0%, #6b2a52 45%, #b0446b 100%)',
	'linear-gradient(135deg, #14171c 0%, #2c2416 55%, #4a3a1e 100%)',
);
$bp_event_index     = 0;
?>
<section class="bp-container bp-section">
	<h2 class="bp-section__title">Мероприятия</h2>
	<div class="bp-events-grid">
		<?php
		while ( $bp_events_query->have_posts() ) :
			$bp_events_query->the_post();

			$subtitle = get_post_meta( get_the_ID(), '_bp_subtitle', true );

			if ( has_post_thumbnail() ) {
				$background = 'url(' . esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ) . ')';
			} else {
				$background = $bp_event_gradients[ $bp_event_index % count( $bp_event_gradients ) ];
			}
			++$bp_event_index;
			?>
			<a href="<?php the_permalink(); ?>" class="bp-event-card" style="background-image: <?php echo esc_attr( $background ); ?>;">
				<span class="bp-event-card__title"><?php the_title(); ?></span>
				<?php if ( $subtitle ) : ?>
					<span class="bp-event-card__subtitle"><?php echo esc_html( $subtitle ); ?></span>
				<?php endif; ?>
			</a>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
