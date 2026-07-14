<?php
/**
 * Archive template for the "Магазины" (bp_shop) post type: the full shop
 * catalog, filterable by floor. Linked from the homepage shops section.
 * Loaded from here (instead of the theme root) via the `archive_template`
 * filter in inc/post-types.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$bp_catalog_shops = array();

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();

		$bp_shop_localized_id = bishkek_park_get_localized_post_id( get_the_ID() );
		if ( $bp_shop_localized_id !== get_the_ID() ) {
			setup_postdata( get_post( $bp_shop_localized_id ) );
		}

		$bp_catalog_shops[] = array(
			'title'     => get_the_title(),
			'permalink' => get_permalink(),
			'thumbnail' => get_the_post_thumbnail( get_the_ID(), 'medium', array( 'class' => 'bp-shop-card__photo' ) ),
			'category'  => get_post_meta( get_the_ID(), '_bp_category', true ),
			'floor'     => get_post_meta( get_the_ID(), '_bp_floor', true ),
		);
	}
	wp_reset_postdata();
}

$bp_catalog_floors = array_unique( array_filter( wp_list_pluck( $bp_catalog_shops, 'floor' ) ) );
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
			<h1 class="bp-section__title"><?php pll_esc_html_e( 'Каталог магазинов' ); ?></h1>
		</div>

		<?php if ( ! empty( $bp_catalog_floors ) ) : ?>
			<div class="bp-shop-catalog__filters" data-bp-shop-filter role="group" aria-label="<?php pll_esc_attr_e( 'Фильтр по этажам' ); ?>">
				<button type="button" class="bp-shop-catalog__filter is-active" data-bp-shop-filter-value="all"><?php pll_esc_html_e( 'Все этажи' ); ?></button>
				<?php foreach ( $bp_catalog_floors as $bp_floor_value ) : ?>
					<button type="button" class="bp-shop-catalog__filter" data-bp-shop-filter-value="<?php echo esc_attr( $bp_floor_value ); ?>"><?php echo bishkek_park_get_shop_floor_label( $bp_floor_value ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $bp_catalog_shops ) ) : ?>
			<div class="bp-shops-grid" data-bp-shop-grid>
				<?php foreach ( $bp_catalog_shops as $bp_shop ) : ?>
					<a href="<?php echo esc_url( $bp_shop['permalink'] ); ?>" class="bp-shop-card" data-bp-shop-floor="<?php echo esc_attr( $bp_shop['floor'] ); ?>">
						<span class="bp-shop-card__image">
							<?php if ( $bp_shop['thumbnail'] ) : ?>
								<?php echo $bp_shop['thumbnail']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<span class="bp-shop-card__logo"><?php echo esc_html( $bp_shop['title'] ); ?></span>
							<?php endif; ?>
						</span>
						<h3 class="bp-shop-card__name"><?php echo esc_html( $bp_shop['title'] ); ?></h3>
						<?php if ( $bp_shop['category'] || $bp_shop['floor'] ) : ?>
							<span class="bp-shop-card__meta">
								<?php if ( $bp_shop['category'] ) : ?>
									<span class="bp-shop-card__category"><?php echo esc_html( $bp_shop['category'] ); ?></span>
								<?php endif; ?>
								<?php if ( $bp_shop['floor'] ) : ?>
									<span class="bp-shop-card__floor"><?php echo bishkek_park_get_shop_floor_label( $bp_shop['floor'] ); ?></span>
								<?php endif; ?>
							</span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="bp-shop-catalog__empty"><?php pll_esc_html_e( 'Магазины пока не добавлены.' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
