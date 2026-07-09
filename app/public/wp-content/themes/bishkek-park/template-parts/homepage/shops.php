<?php
/**
 * Homepage "Магазины" section, pulled from the bp_shop post type.
 * Edit/add shops under WP Admin -> Магазины.
 *
 * Shows one row (BISHKEK_PARK_SHOPS_PER_ROW cards) as a static grid.
 * If more shops are published than that, it becomes a horizontally
 * scrollable slider with prev/next controls instead of wrapping rows.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_SHOPS_PER_ROW', 4 );

$bp_shops_query = new WP_Query(
	array(
		'post_type'      => 'bp_shop',
		'posts_per_page' => 24,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( ! $bp_shops_query->have_posts() ) {
	return;
}

$bp_shops_is_slider = $bp_shops_query->post_count > BISHKEK_PARK_SHOPS_PER_ROW;
?>
<section class="bp-container bp-section">
	<div class="bp-section__header">
		<h2 class="bp-section__title">Магазины</h2>
		<?php if ( $bp_shops_is_slider ) : ?>
			<div class="bp-slider-nav" data-bp-slider-nav="bp-shops-track">
				<button type="button" class="bp-slider-nav__btn" data-bp-slider-prev aria-label="Предыдущие магазины">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
				</button>
				<button type="button" class="bp-slider-nav__btn" data-bp-slider-next aria-label="Следующие магазины">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-right.svg' ) ); ?>" width="7" height="12" alt="">
				</button>
			</div>
		<?php endif; ?>
	</div>

	<div
		class="bp-shops-grid<?php echo $bp_shops_is_slider ? ' bp-shops-grid--slider' : ''; ?>"
		<?php echo $bp_shops_is_slider ? 'id="bp-shops-track"' : ''; ?>
	>
		<?php
		while ( $bp_shops_query->have_posts() ) :
			$bp_shops_query->the_post();

			$category = get_post_meta( get_the_ID(), '_bp_category', true );
			$floor    = get_post_meta( get_the_ID(), '_bp_floor', true );
			?>
			<a href="<?php the_permalink(); ?>" class="bp-shop-card">
				<span class="bp-shop-card__image">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'medium', array( 'class' => 'bp-shop-card__photo' ) ); ?>
					<?php else : ?>
						<span class="bp-shop-card__logo"><?php the_title(); ?></span>
					<?php endif; ?>
					<?php if ( $floor ) : ?>
						<span class="bp-shop-card__floor"><?php echo esc_html( $floor ); ?></span>
					<?php endif; ?>
				</span>
				<span class="bp-shop-card__name"><?php the_title(); ?></span>
				<?php if ( $category ) : ?>
					<span class="bp-shop-card__category"><?php echo esc_html( $category ); ?></span>
				<?php endif; ?>
			</a>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
