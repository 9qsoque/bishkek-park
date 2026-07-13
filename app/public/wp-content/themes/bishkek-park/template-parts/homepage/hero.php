<?php
/**
 * Homepage hero banner slider, pulled from the bp_banner post type.
 * Edit/add banners under WP Admin -> Баннеры; drag their "Order" field
 * (page-attributes) to control slide order.
 *
 * Falls back to a single hardcoded banner (identical to the original
 * static hero) when no bp_banner posts are published yet, so the
 * homepage never ends up with an empty hero section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bp_banners_query = new WP_Query(
	array(
		'post_type'      => 'bp_banner',
		'posts_per_page' => 4,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
		'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
	)
);

$bp_banner_count = $bp_banners_query->post_count;
?>
<section class="bp-container bp-hero-wrap">
	<?php if ( $bp_banner_count > 0 ) : ?>
		<div class="bp-hero" data-bp-hero-slider>
			<?php
			$bp_banner_index = 0;
			while ( $bp_banners_query->have_posts() ) :
				$bp_banners_query->the_post();

				$bp_banner_id = bishkek_park_get_localized_post_id( get_the_ID() );
				if ( $bp_banner_id !== get_the_ID() ) {
					setup_postdata( get_post( $bp_banner_id ) );
				}

				$bp_badge        = get_post_meta( get_the_ID(), '_bp_badge', true );
				$bp_text         = get_post_meta( get_the_ID(), '_bp_text', true );
				$bp_button_label = get_post_meta( get_the_ID(), '_bp_button_label', true );
				$bp_button_url   = get_post_meta( get_the_ID(), '_bp_button_url', true );
				?>
				<div class="bp-hero__slide<?php echo 0 === $bp_banner_index ? ' is-active' : ''; ?>">
					<div class="bp-hero__content">
						<?php if ( $bp_badge ) : ?>
							<span class="bp-badge"><?php echo esc_html( $bp_badge ); ?></span>
						<?php endif; ?>
						<h1 class="bp-hero__title"><?php the_title(); ?></h1>
						<?php if ( $bp_text ) : ?>
							<p class="bp-hero__text"><?php echo esc_html( $bp_text ); ?></p>
						<?php endif; ?>
						<?php if ( $bp_button_label && $bp_button_url ) : ?>
							<a href="<?php echo esc_url( $bp_button_url ); ?>" class="bp-btn bp-btn--primary"><?php echo esc_html( $bp_button_label ); ?></a>
						<?php endif; ?>
					</div>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="bp-hero__art">
							<?php the_post_thumbnail( 'large' ); ?>
						</div>
					<?php endif; ?>
				</div>
				<?php
				$bp_banner_index++;
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<?php if ( $bp_banner_count > 1 ) : ?>
			<div class="bp-hero__dots">
				<?php for ( $i = 0; $i < $bp_banner_count; $i++ ) : ?>
					<button
						type="button"
						class="bp-hero__dot<?php echo 0 === $i ? ' is-active' : ''; ?>"
						data-bp-hero-dot
						aria-label="<?php echo esc_attr( sprintf( /* translators: %d: banner slide number */ __( 'Баннер %d', 'bishkek-park' ), $i + 1 ) ); ?>"
					></button>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<div class="bp-hero">
			<div class="bp-hero__slide is-active">
				<div class="bp-hero__content">
					<span class="bp-badge"><?php pll_esc_html_e( 'СЕЗОННАЯ РАСПРОДАЖА' ); ?></span>
					<h1 class="bp-hero__title"><?php pll_e( 'Скидки до 30% в&nbsp;любимых магазинах' ); ?></h1>
					<p class="bp-hero__text"><?php pll_esc_html_e( 'Покупайте онлайн и возвращайте деньги за каждую покупку' ); ?></p>
					<a href="#" class="bp-btn bp-btn--primary"><?php pll_esc_html_e( 'Подробнее' ); ?></a>
				</div>
				<div class="bp-hero__art" aria-hidden="true">
					<img src="<?php echo esc_url( BISHKEK_PARK_URI . '/assets/images/heroImg.png' ); ?>" width="400" height="200" alt="">
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
