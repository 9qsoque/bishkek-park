<?php
/**
 * Archive template for the "Мероприятия" (bp_event) post type: the full
 * events catalog, filterable by category. Loaded from here (instead of the
 * theme root) via the `archive_template` filter in inc/post-types.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Fallback gradients for events without a featured image, cycled by index —
// same set used on the homepage teaser (template-parts/homepage/events.php).
$bp_event_gradients = array(
	'linear-gradient(135deg, #2b1330 0%, #6b2a52 45%, #b0446b 100%)',
	'linear-gradient(135deg, #14171c 0%, #2c2416 55%, #4a3a1e 100%)',
);

$bp_catalog_events = array();

if ( have_posts() ) {
	$bp_event_index = 0;

	while ( have_posts() ) {
		the_post();

		$bp_event_localized_id = bishkek_park_get_localized_post_id( get_the_ID() );
		if ( $bp_event_localized_id !== get_the_ID() ) {
			setup_postdata( get_post( $bp_event_localized_id ) );
		}

		$bp_catalog_events[] = array(
			'title'       => get_the_title(),
			'permalink'   => get_permalink(),
			'thumbnail'   => get_the_post_thumbnail( get_the_ID(), 'large', array( 'class' => 'bp-event-card__photo' ) ),
			'category'    => get_post_meta( get_the_ID(), '_bp_category', true ),
			'date'        => bishkek_park_format_event_date( get_post_meta( get_the_ID(), '_bp_date', true ) ),
			'description' => get_post_meta( get_the_ID(), '_bp_description', true ),
			'gradient'    => $bp_event_gradients[ $bp_event_index % count( $bp_event_gradients ) ],
		);

		++$bp_event_index;
	}
	wp_reset_postdata();
}

$bp_catalog_categories = array_unique( array_filter( wp_list_pluck( $bp_catalog_events, 'category' ) ) );
natsort( $bp_catalog_categories );
?>

<main class="bp-main">
	<div class="bp-container bp-back-link-wrap">
		<a class="bp-back-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
			<?php pll_esc_html_e( 'Назад на главную' ); ?>
		</a>
	</div>

	<div class="bp-container bp-section bp-event-catalog">
		<div class="bp-section__header">
			<h1 class="bp-section__title"><?php pll_esc_html_e( 'Мероприятия' ); ?></h1>
		</div>

		<?php if ( ! empty( $bp_catalog_categories ) ) : ?>
			<div class="bp-event-catalog__filters" data-bp-event-filter role="group" aria-label="<?php pll_esc_attr_e( 'Фильтр по категориям' ); ?>">
				<button type="button" class="bp-event-catalog__filter is-active" data-bp-event-filter-value="all"><?php pll_esc_html_e( 'Все' ); ?></button>
				<?php foreach ( $bp_catalog_categories as $bp_category_value ) : ?>
					<button type="button" class="bp-event-catalog__filter" data-bp-event-filter-value="<?php echo esc_attr( $bp_category_value ); ?>"><?php echo esc_html( $bp_category_value ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $bp_catalog_events ) ) : ?>
			<div class="bp-events-grid" data-bp-event-grid>
				<?php foreach ( $bp_catalog_events as $bp_event ) : ?>
					<a href="<?php echo esc_url( $bp_event['permalink'] ); ?>" class="bp-event-card" data-bp-event-category="<?php echo esc_attr( $bp_event['category'] ); ?>">
						<span class="bp-event-card__image">
							<?php if ( $bp_event['thumbnail'] ) : ?>
								<?php echo $bp_event['thumbnail']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<span class="bp-event-card__placeholder" style="background-image: <?php echo esc_attr( $bp_event['gradient'] ); ?>;"></span>
							<?php endif; ?>
						</span>
						<span class="bp-event-card__body">
							<?php if ( $bp_event['category'] ) : ?>
								<span class="bp-event-card__category"><?php echo esc_html( $bp_event['category'] ); ?></span>
							<?php endif; ?>
							<?php if ( $bp_event['date'] ) : ?>
								<span class="bp-event-card__date"><?php echo esc_html( $bp_event['date'] ); ?></span>
							<?php endif; ?>
							<h3 class="bp-event-card__title"><?php echo esc_html( $bp_event['title'] ); ?></h3>
							<?php if ( $bp_event['description'] ) : ?>
								<span class="bp-event-card__description"><?php echo esc_html( $bp_event['description'] ); ?></span>
							<?php endif; ?>
							<span class="bp-event-card__link"><?php pll_esc_html_e( 'Подробнее' ); ?> <span aria-hidden="true">→</span></span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="bp-event-catalog__empty"><?php pll_esc_html_e( 'Мероприятия пока не добавлены.' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
