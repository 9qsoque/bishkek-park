<?php
/**
 * Archive template for the "Кафе и рестораны" (bp_cafe) post type: the full
 * cafe/restaurant catalog, filterable by floor. Linked from the header nav.
 * Loaded from here (instead of the theme root) via the `archive_template`
 * filter in inc/post-types.php.
 *
 * Deliberately mirrors template-parts/shop/archive-bp_shop.php — same
 * .bp-shops-grid/.bp-shop-card markup and data-bp-shop-filter/-grid/-floor
 * attributes — so it reuses shop-catalog.css/.js and front-page.css as-is
 * instead of duplicating them (see the enqueue logic in functions.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bp_catalog_cafes = array();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		$bp_cafe_localized_id = bishkek_park_get_localized_post_id( get_the_ID() );
		if ( $bp_cafe_localized_id !== get_the_ID() ) {
			setup_postdata( get_post( $bp_cafe_localized_id ) );
		}

		$bp_catalog_cafes[] = array(
			'title'     => get_the_title(),
			'permalink' => get_permalink(),
			'thumbnail' => get_the_post_thumbnail( get_the_ID(), 'medium', array( 'class' => 'bp-shop-card__photo' ) ),
			'category'  => get_post_meta( get_the_ID(), '_bp_category', true ),
			'floor'     => get_post_meta( get_the_ID(), '_bp_floor', true ),
		);
	}
	wp_reset_postdata();
}

$bp_catalog_floors = array_unique( array_filter( wp_list_pluck( $bp_catalog_cafes, 'floor' ) ) );
natsort( $bp_catalog_floors );
?>

<main class="bp-main">
	<div class="bp-container bp-back-link-wrap">
		<a class="bp-back-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
			<?php pll_esc_html_e( 'Назад на главную' ); ?>
		</a>
	</div>

	<div class="bp-container bp-section bp-shop-catalog">
		<div class="bp-section__header">
			<h1 class="bp-section__title"><?php pll_esc_html_e( 'Кафе и рестораны' ); ?></h1>
		</div>

		<?php if ( ! empty( $bp_catalog_floors ) ) : ?>
			<div class="bp-shop-catalog__filters" data-bp-shop-filter role="group" aria-label="<?php pll_esc_attr_e( 'Фильтр по этажам' ); ?>">
				<button type="button" class="bp-shop-catalog__filter is-active" data-bp-shop-filter-value="all"><?php pll_esc_html_e( 'Все этажи' ); ?></button>
				<?php foreach ( $bp_catalog_floors as $bp_floor_value ) : ?>
					<button type="button" class="bp-shop-catalog__filter" data-bp-shop-filter-value="<?php echo esc_attr( $bp_floor_value ); ?>"><?php echo bishkek_park_get_shop_floor_label( $bp_floor_value ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $bp_catalog_cafes ) ) : ?>
			<div class="bp-shops-grid" data-bp-shop-grid>
				<?php foreach ( $bp_catalog_cafes as $bp_cafe ) : ?>
					<a href="<?php echo esc_url( $bp_cafe['permalink'] ); ?>" class="bp-shop-card" data-bp-shop-floor="<?php echo esc_attr( $bp_cafe['floor'] ); ?>">
						<span class="bp-shop-card__image">
							<?php if ( $bp_cafe['thumbnail'] ) : ?>
								<?php echo $bp_cafe['thumbnail']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<span class="bp-shop-card__logo"><?php echo esc_html( $bp_cafe['title'] ); ?></span>
							<?php endif; ?>
						</span>
						<h3 class="bp-shop-card__name"><?php echo esc_html( $bp_cafe['title'] ); ?></h3>
						<span class="bp-shop-card__meta">
							<span class="bp-shop-card__category">
								<?php if ( $bp_cafe['category'] ) : ?>
									<?php echo esc_html( $bp_cafe['category'] ); ?>
								<?php else : ?>
									<?php pll_esc_html_e( 'Ресторан' ); ?>
								<?php endif; ?>
							</span>
							<?php if ( $bp_cafe['floor'] ) : ?>
								<span class="bp-shop-card__floor"><?php echo bishkek_park_get_shop_floor_label( $bp_cafe['floor'] ); ?></span>
							<?php endif; ?>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="bp-shop-catalog__empty"><?php pll_esc_html_e( 'Кафе и рестораны пока не добавлены.' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
