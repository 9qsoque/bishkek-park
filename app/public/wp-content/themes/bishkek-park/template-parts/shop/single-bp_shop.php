<?php
/**
 * Single "Магазин" (bp_shop) template: shop detail (image, description,
 * website, hours) plus a "Магазины на этаже" grid of other shops on the
 * same floor below. Loaded from here (instead of the theme root) via the
 * `single_template` filter in inc/post-types.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_RELATED_SHOPS_COUNT', 4 );

get_header();

while ( have_posts() ) :
	the_post();

	$bp_shop_id          = get_the_ID();
	$bp_shop_category    = get_post_meta( $bp_shop_id, '_bp_category', true );
	$bp_shop_floor       = get_post_meta( $bp_shop_id, '_bp_floor', true );
	$bp_shop_description = get_post_meta( $bp_shop_id, '_bp_description', true );
	$bp_shop_hours       = get_post_meta( $bp_shop_id, '_bp_hours', true );
	$bp_shop_website     = get_post_meta( $bp_shop_id, '_bp_website', true );
	?>
	<main class="bp-main">
		<div class="bp-container bp-back-link-wrap">
			<a class="bp-back-link" href="<?php echo esc_url( get_post_type_archive_link( 'bp_shop' ) ); ?>">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
				<?php pll_esc_html_e( 'Назад в каталог' ); ?>
			</a>
		</div>

		<div class="bp-container bp-section bp-shop-detail">
			<div class="bp-shop-detail__image">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'medium', array( 'class' => 'bp-shop-detail__photo' ) ); ?>
				<?php else : ?>
					<span class="bp-shop-detail__logo"><?php the_title(); ?></span>
				<?php endif; ?>
			</div>

			<div class="bp-shop-detail__content">
				<h1 class="bp-shop-detail__title"><?php the_title(); ?></h1>

				<?php if ( $bp_shop_category || $bp_shop_floor ) : ?>
					<span class="bp-shop-card__meta">
						<?php if ( $bp_shop_category ) : ?>
							<span class="bp-shop-card__category"><?php echo esc_html( $bp_shop_category ); ?></span>
						<?php endif; ?>
						<?php if ( $bp_shop_floor ) : ?>
							<span class="bp-shop-card__floor bp-shop-card__floor--static"><?php echo bishkek_park_get_shop_floor_label( $bp_shop_floor ); ?></span>
						<?php endif; ?>
					</span>
				<?php endif; ?>

				<?php if ( $bp_shop_description ) : ?>
					<p class="bp-shop-detail__description"><?php echo esc_html( $bp_shop_description ); ?></p>
				<?php endif; ?>

				<div class="bp-shop-detail__meta">
					<div class="bp-shop-detail__meta-item">
						<span class="bp-shop-detail__meta-label"><?php pll_esc_html_e( 'Сайт' ); ?></span>
						<span class="bp-shop-detail__meta-value">
							<?php if ( $bp_shop_website ) : ?>
								<?php echo esc_html( $bp_shop_website ); ?>
							<?php else : ?>
								<?php pll_esc_html_e( 'info@bishkekpark.kg' ); ?>
							<?php endif; ?>
						</span>
					</div>
					<div class="bp-shop-detail__meta-item">
						<span class="bp-shop-detail__meta-label"><?php pll_esc_html_e( 'Время работы' ); ?></span>
						<span class="bp-shop-detail__meta-value">
							<?php if ( $bp_shop_hours ) : ?>
								<?php echo esc_html( $bp_shop_hours ); ?>
							<?php else : ?>
								<?php pll_esc_html_e( 'Пн - Вс: 10:00 - 22:00' ); ?>
							<?php endif; ?>
						</span>
					</div>
				</div>
			</div>
		</div>

		<?php
		// Exclude the current shop from "other shops" below. Compare against
		// its default-language (canonical) ID since the related query below
		// queries the default language, same reasoning as elsewhere — see
		// CLAUDE.md "Polylang CPT translation".
		$bp_shop_default_id = ( function_exists( 'pll_get_post' ) && function_exists( 'pll_default_language' ) )
			? ( pll_get_post( $bp_shop_id, pll_default_language() ) ?: $bp_shop_id )
			: $bp_shop_id;

		// Only shops on the same floor belong in "Магазины на этаже" — if the
		// current shop has no floor set, there's nothing to match against.
		$bp_related_shops_query = $bp_shop_floor ? new WP_Query(
			array(
				'post_type'      => 'bp_shop',
				'posts_per_page' => BISHKEK_PARK_RELATED_SHOPS_COUNT,
				'post__not_in'   => array( $bp_shop_default_id ),
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'no_found_rows'  => true,
				'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
				'meta_key'       => '_bp_floor',
				'meta_value'     => $bp_shop_floor,
			)
		) : null;

		if ( $bp_related_shops_query && $bp_related_shops_query->have_posts() ) :
			?>
			<div class="bp-container bp-section">
				<div class="bp-section__header">
					<h2 class="bp-section__title"><?php pll_esc_html_e( 'Магазины на этаже' ); ?></h2>
				</div>

				<div class="bp-shops-grid">
					<?php
					while ( $bp_related_shops_query->have_posts() ) :
						$bp_related_shops_query->the_post();

						$bp_related_shop_localized_id = bishkek_park_get_localized_post_id( get_the_ID() );
						if ( $bp_related_shop_localized_id !== get_the_ID() ) {
							setup_postdata( get_post( $bp_related_shop_localized_id ) );
						}

						$bp_related_category = get_post_meta( get_the_ID(), '_bp_category', true );
						$bp_related_floor    = get_post_meta( get_the_ID(), '_bp_floor', true );
						?>
						<a href="<?php the_permalink(); ?>" class="bp-shop-card">
							<span class="bp-shop-card__image">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail( 'medium', array( 'class' => 'bp-shop-card__photo' ) ); ?>
								<?php else : ?>
									<span class="bp-shop-card__logo"><?php the_title(); ?></span>
								<?php endif; ?>
							</span>
							<h3 class="bp-shop-card__name"><?php the_title(); ?></h3>
							<?php if ( $bp_related_category || $bp_related_floor ) : ?>
								<span class="bp-shop-card__meta">
									<?php if ( $bp_related_category ) : ?>
										<span class="bp-shop-card__category"><?php echo esc_html( $bp_related_category ); ?></span>
									<?php endif; ?>
									<?php if ( $bp_related_floor ) : ?>
										<span class="bp-shop-card__floor"><?php echo bishkek_park_get_shop_floor_label( $bp_related_floor ); ?></span>
									<?php endif; ?>
								</span>
							<?php endif; ?>
						</a>
						<?php
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
