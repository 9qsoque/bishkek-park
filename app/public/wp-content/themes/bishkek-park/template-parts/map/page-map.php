<?php
/**
 * Template Name: Карта ТЦ
 *
 * Mall map: one floor plan per floor (-1 to 2), switched via prev/next
 * controls without a page reload. Each floor's plan is the theme's own
 * hand-traced vector artwork at assets/icons/{floor}-floor.svg (despite
 * living in /icons/, these are full floor-plan drawings, not small icons —
 * see bishkek_park_icon_url()). Lives here (instead of the theme root) per
 * the template-parts/{something}/ convention; registered explicitly via the
 * `theme_page_templates` filter in functions.php
 * (bishkek_park_register_page_templates()) the same way the Funcity/
 * Контакты templates are.
 *
 * The floor switching itself is handled by assets/js/mall-map.js, driven
 * entirely by the data-bp-mall-map-* attributes below (same generic,
 * data-attribute-driven pattern as the homepage sliders in front-page.js and
 * the shop-catalog floor filter) — the default floor below is rendered
 * server-side so the page works before JS runs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Order matches the mall's actual floors, bottom to top.
$bp_map_floors        = array( '-1', '0', '1', '2' );
$bp_map_default_floor = '-1';

$bp_map_shop_archive_url = get_post_type_archive_link( 'bp_shop' );

// Shop counts per floor, for the "Магазины на этаже" link below the plan.
// Guarded with the `lang` param the same way every other bp_shop query in
// the theme is — see CLAUDE.md "Polylang CPT translation".
$bp_map_floor_shop_counts = array();
foreach ( $bp_map_floors as $bp_map_floor ) {
	$bp_map_floor_query = new WP_Query(
		array(
			'post_type'      => 'bp_shop',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => false,
			'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
			'meta_key'       => '_bp_floor',
			'meta_value'     => $bp_map_floor,
		)
	);
	$bp_map_floor_shop_counts[ $bp_map_floor ] = $bp_map_floor_query->found_posts;
}

get_header();
?>
<main class="bp-main bp-mall-map">
	<div class="bp-container bp-back-link-wrap">
		<a class="bp-back-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
			<?php pll_esc_html_e( 'Назад на главную' ); ?>
		</a>
	</div>

	<div class="bp-container bp-mall-map-heading">
		<button type="button" class="bp-mall-map-nav__btn bp-mall-map-nav__btn--mobile-prev" data-bp-mall-map-prev aria-label="<?php pll_esc_attr_e( 'Предыдущий этаж' ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'arrow-map.svg' ) ); ?>" width="14" height="17" alt="">
		</button>
		<h1 class="bp-mall-map-heading__title" data-bp-mall-map-title><?php echo esc_html( $bp_map_default_floor . ' ' . pll_esc_html__( 'Этаж' ) ); ?></h1>
		<button type="button" class="bp-mall-map-nav__btn bp-mall-map-nav__btn--mobile-next" data-bp-mall-map-next aria-label="<?php pll_esc_attr_e( 'Следующий этаж' ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'arrow-map.svg' ) ); ?>" width="14" height="17" alt="">
		</button>
	</div>

	<div class="bp-container bp-mall-map-stage" data-bp-mall-map>
		<button type="button" class="bp-mall-map-nav__btn bp-mall-map-nav__btn--prev" data-bp-mall-map-prev aria-label="<?php pll_esc_attr_e( 'Предыдущий этаж' ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'arrow_back_ios.svg' ) ); ?>" width="14" height="24" alt="">
		</button>
		<?php foreach ( $bp_map_floors as $bp_map_floor ) : ?>
			<?php
			$bp_map_floor_count = $bp_map_floor_shop_counts[ $bp_map_floor ];
			$bp_map_floor_title = $bp_map_floor . ' ' . pll_esc_html__( 'Этаж' );
			$bp_map_floor_url   = $bp_map_shop_archive_url ? add_query_arg( 'floor', $bp_map_floor, $bp_map_shop_archive_url ) : '#';
			?>
			<figure
				class="bp-mall-map-floor<?php echo $bp_map_floor === $bp_map_default_floor ? ' is-active' : ''; ?>"
				data-bp-mall-map-floor="<?php echo esc_attr( $bp_map_floor ); ?>"
				data-bp-mall-map-title="<?php echo esc_attr( $bp_map_floor_title ); ?>"
				data-bp-mall-map-shop-count="<?php echo esc_attr( $bp_map_floor_count ); ?>"
				data-bp-mall-map-shop-url="<?php echo esc_url( $bp_map_floor_url ); ?>"
			>
				<img
					class="bp-mall-map-floor__img"
					src="<?php echo esc_url( bishkek_park_icon_url( $bp_map_floor . '-floor.svg' ) ); ?>"
					alt="<?php echo esc_attr( pll_esc_html__( 'План этажа' ) . ' ' . $bp_map_floor ); ?>"
				>
			</figure>
		<?php endforeach; ?>
		<button type="button" class="bp-mall-map-nav__btn bp-mall-map-nav__btn--next" data-bp-mall-map-next aria-label="<?php pll_esc_attr_e( 'Следующий этаж' ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'arrow_back_ios.svg' ) ); ?>" width="14" height="24" alt="">
		</button>
	</div>

	<?php
	$bp_map_default_count = $bp_map_floor_shop_counts[ $bp_map_default_floor ];
	$bp_map_default_url   = $bp_map_shop_archive_url ? add_query_arg( 'floor', $bp_map_default_floor, $bp_map_shop_archive_url ) : '#';
	?>
	<div class="bp-container bp-mall-map-shops<?php echo $bp_map_default_count > 0 ? '' : ' is-hidden'; ?>" data-bp-mall-map-shops>
		<h2 class="bp-mall-map-shops__heading"><?php pll_esc_html_e( 'Магазины на этаже' ); ?></h2>
		<a class="bp-mall-map-shops__link" data-bp-mall-map-shops-link href="<?php echo esc_url( $bp_map_default_url ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-map-pin-outline.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
			<span class="bp-mall-map-shops__count" data-bp-mall-map-shops-count><?php echo esc_html( $bp_map_default_count ); ?></span>
			<span class="bp-mall-map-shops__label"><?php pll_esc_html_e( 'магазинов и сервисов' ); ?></span>
		</a>
	</div>
</main>
<?php
get_footer();
