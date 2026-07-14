<?php
/**
 * Single "Мероприятие" (bp_event) template: event detail (image, category,
 * date, description) plus a "Мероприятия" grid of other events below.
 * Loaded from here (instead of the theme root) via the `single_template`
 * filter in inc/post-types.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_RELATED_EVENTS_COUNT', 2 );

get_header();

// Fallback gradients for events without a featured image, cycled by index —
// same set used on the homepage teaser and catalog archive.
$bp_event_gradients = array(
	'linear-gradient(135deg, #2b1330 0%, #6b2a52 45%, #b0446b 100%)',
	'linear-gradient(135deg, #14171c 0%, #2c2416 55%, #4a3a1e 100%)',
);

while ( have_posts() ) :
	the_post();

	$bp_event_id          = get_the_ID();
	$bp_event_category    = get_post_meta( $bp_event_id, '_bp_category', true );
	$bp_event_date        = bishkek_park_format_event_date( get_post_meta( $bp_event_id, '_bp_date', true ) );
	$bp_event_description = get_post_meta( $bp_event_id, '_bp_description', true );
	?>
	<main class="bp-main">
		<div class="bp-container bp-back-link-wrap">
			<a class="bp-back-link" href="<?php echo esc_url( get_post_type_archive_link( 'bp_event' ) ); ?>">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
				<?php pll_esc_html_e( 'Назад в каталог' ); ?>
			</a>
		</div>

		<div class="bp-container bp-section bp-event-detail">
			<div class="bp-event-detail__image">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'medium', array( 'class' => 'bp-event-detail__photo' ) ); ?>
				<?php else : ?>
					<span class="bp-event-detail__placeholder" style="background-image: <?php echo esc_attr( $bp_event_gradients[ $bp_event_id % count( $bp_event_gradients ) ] ); ?>;"></span>
				<?php endif; ?>
			</div>

			<div class="bp-event-detail__content">
				<?php if ( $bp_event_category || $bp_event_date ) : ?>
					<span class="bp-event-detail__badge">
						<?php if ( $bp_event_category ) : ?>
							<span class="bp-event-detail__badge-category"><?php echo esc_html( $bp_event_category ); ?></span>
						<?php endif; ?>
						<?php if ( $bp_event_date ) : ?>
							<span class="bp-event-detail__badge-date"><?php echo esc_html( $bp_event_date ); ?></span>
						<?php endif; ?>
					</span>
				<?php endif; ?>

				<h1 class="bp-event-detail__title"><?php the_title(); ?></h1>

				<?php if ( $bp_event_description ) : ?>
					<p class="bp-event-detail__description"><?php echo esc_html( $bp_event_description ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<?php
		// Exclude the current event from "other events" below. Compare against
		// its default-language (canonical) ID since the related query below
		// queries the default language, same reasoning as elsewhere — see
		// CLAUDE.md "Polylang CPT translation".
		$bp_event_default_id = ( function_exists( 'pll_get_post' ) && function_exists( 'pll_default_language' ) )
			? ( pll_get_post( $bp_event_id, pll_default_language() ) ?: $bp_event_id )
			: $bp_event_id;

		$bp_related_events_query = new WP_Query(
			array(
				'post_type'      => 'bp_event',
				'posts_per_page' => BISHKEK_PARK_RELATED_EVENTS_COUNT,
				'post__not_in'   => array( $bp_event_default_id ),
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'no_found_rows'  => true,
				'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
			)
		);

		if ( $bp_related_events_query->have_posts() ) :
			?>
			<div class="bp-container bp-section">
				<div class="bp-section__header">
					<h2 class="bp-section__title"><?php pll_esc_html_e( 'Мероприятия' ); ?></h2>
				</div>

				<div class="bp-events-grid">
					<?php
					$bp_related_event_index = 0;

					while ( $bp_related_events_query->have_posts() ) :
						$bp_related_events_query->the_post();

						$bp_related_event_localized_id = bishkek_park_get_localized_post_id( get_the_ID() );
						if ( $bp_related_event_localized_id !== get_the_ID() ) {
							setup_postdata( get_post( $bp_related_event_localized_id ) );
						}

						$bp_related_category    = get_post_meta( get_the_ID(), '_bp_category', true );
						$bp_related_date        = bishkek_park_format_event_date( get_post_meta( get_the_ID(), '_bp_date', true ) );
						$bp_related_description = get_post_meta( get_the_ID(), '_bp_description', true );
						?>
						<a href="<?php the_permalink(); ?>" class="bp-event-card">
							<span class="bp-event-card__image">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'large', array( 'class' => 'bp-event-card__photo' ) ); ?>
								<?php else : ?>
									<span
										class="bp-event-card__placeholder"
										style="background-image: <?php echo esc_attr( $bp_event_gradients[ $bp_related_event_index % count( $bp_event_gradients ) ] ); ?>;"
									></span>
								<?php endif; ?>
							</span>
							<span class="bp-event-card__body">
								<?php if ( $bp_related_category ) : ?>
									<span class="bp-event-card__category"><?php echo esc_html( $bp_related_category ); ?></span>
								<?php endif; ?>
								<?php if ( $bp_related_date ) : ?>
									<span class="bp-event-card__date"><?php echo esc_html( $bp_related_date ); ?></span>
								<?php endif; ?>
								<h3 class="bp-event-card__title"><?php the_title(); ?></h3>
								<?php if ( $bp_related_description ) : ?>
									<span class="bp-event-card__description"><?php echo esc_html( $bp_related_description ); ?></span>
								<?php endif; ?>
								<span class="bp-event-card__link"><?php pll_esc_html_e( 'Подробнее' ); ?> <span aria-hidden="true">→</span></span>
							</span>
						</a>
						<?php
						++$bp_related_event_index;
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
			<?php
		endif;
		?>
	</main>
	<?php
endwhile;

get_footer();
