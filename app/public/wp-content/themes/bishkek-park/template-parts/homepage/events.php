<?php
/**
 * Homepage "Мероприятия" section, pulled from the bp_event post type.
 * Edit/add events under WP Admin -> Мероприятия.
 *
 * Shows one row (BISHKEK_PARK_EVENTS_PER_ROW cards) as a static grid.
 * If more events are published than that, it becomes a horizontally
 * scrollable slider with prev/next controls instead of wrapping rows.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_EVENTS_PER_ROW', 2 );
define( 'BISHKEK_PARK_EVENTS_PER_MOBILE_PAGE', 1 );

$bp_events_query = new WP_Query(
	array(
		'post_type'      => 'bp_event',
		'posts_per_page' => 10,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
		// Always query the default (RU) language as the canonical list, then
		// swap in the current-language translation per post where it exists —
		// keeps untranslated events visible instead of vanishing on other languages.
		'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
	)
);

if ( ! $bp_events_query->have_posts() ) {
	return;
}

$bp_events_is_slider = $bp_events_query->post_count > BISHKEK_PARK_EVENTS_PER_ROW
	|| $bp_events_query->post_count > BISHKEK_PARK_EVENTS_PER_MOBILE_PAGE;

// Fallback gradients for events without a featured image, cycled by index.
$bp_event_gradients = array(
	'linear-gradient(135deg, #2b1330 0%, #6b2a52 45%, #b0446b 100%)',
	'linear-gradient(135deg, #14171c 0%, #2c2416 55%, #4a3a1e 100%)',
);
$bp_event_index     = 0;
?>
<section class="bp-container bp-section bp-events-section">
	<div class="bp-section__header">
		<h2 class="bp-section__title"><?php pll_esc_html_e( 'Мероприятия' ); ?></h2>
		<div class="bp-section__header-actions">
			<a class="bp-section__view-all" href="<?php echo esc_url( get_post_type_archive_link( 'bp_event' ) ); ?>">
				<span class="bp-section__view-all-full"><?php pll_esc_html_e( 'Все мероприятия' ); ?></span>
				<span class="bp-section__view-all-short"><?php pll_esc_html_e( 'Все' ); ?></span>
			</a>
			<?php if ( $bp_events_is_slider ) : ?>
				<div class="bp-slider-nav" data-bp-slider-nav="bp-events-track">
					<button type="button" class="bp-slider-nav__btn" data-bp-slider-prev aria-label="<?php pll_esc_attr_e( 'Предыдущие мероприятия' ); ?>">
						<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
					</button>
					<button type="button" class="bp-slider-nav__btn" data-bp-slider-next aria-label="<?php pll_esc_attr_e( 'Следующие мероприятия' ); ?>">
						<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-right.svg' ) ); ?>" width="7" height="12" alt="">
					</button>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div
		class="bp-events-grid<?php echo $bp_events_is_slider ? ' bp-events-grid--slider' : ''; ?>"
		<?php echo $bp_events_is_slider ? 'id="bp-events-track"' : ''; ?>
	>
		<?php
		while ( $bp_events_query->have_posts() ) :
			$bp_events_query->the_post();

			global $post;
			$bp_event_localized_id = bishkek_park_get_localized_post_id( get_the_ID() );
			if ( $bp_event_localized_id !== get_the_ID() ) {
				$post = get_post( $bp_event_localized_id );
				setup_postdata( $post );
			}

			$category    = get_post_meta( get_the_ID(), '_bp_category', true );
			$date        = bishkek_park_format_event_date( get_post_meta( get_the_ID(), '_bp_date', true ) );
			$description = get_post_meta( get_the_ID(), '_bp_description', true );
			?>
			<a href="<?php the_permalink(); ?>" class="bp-event-card">
				<span class="bp-event-card__image">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'large', array( 'class' => 'bp-event-card__photo' ) ); ?>
					<?php else : ?>
						<span
							class="bp-event-card__placeholder"
							style="background-image: <?php echo esc_attr( $bp_event_gradients[ $bp_event_index % count( $bp_event_gradients ) ] ); ?>;"
						></span>
					<?php endif; ?>
				</span>
				<span class="bp-event-card__body">
					<?php if ( $category ) : ?>
						<span class="bp-event-card__category"><?php echo esc_html( $category ); ?></span>
					<?php endif; ?>
					<?php if ( $date ) : ?>
						<span class="bp-event-card__date"><?php echo esc_html( $date ); ?></span>
					<?php endif; ?>
					<h3 class="bp-event-card__title"><?php the_title(); ?></h3>
					<?php if ( $description ) : ?>
						<span class="bp-event-card__description"><?php echo esc_html( $description ); ?></span>
					<?php endif; ?>
					<span class="bp-event-card__link"><?php pll_esc_html_e( 'Подробнее' ); ?> <span aria-hidden="true">→</span></span>
				</span>
			</a>
			<?php
			++$bp_event_index;
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
