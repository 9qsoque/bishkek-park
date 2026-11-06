<?php
/**
 * Template Name: Funcity
 *
 * Funcity (детская площадка) landing page: gradient hero with the Funcity
 * logo, a full-bleed photo collage of the play zones (desktop), the zones
 * described as cards (photos shown on mobile instead of the collage), the
 * visiting rules, and a contact CTA. Lives here (instead of the theme root)
 * per the template-parts/{something}/ convention; WordPress only
 * auto-discovers `Template Name:` headers one directory deep, so the
 * template is registered explicitly via the `theme_page_templates` filter
 * in functions.php (bishkek_park_register_page_templates()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Every text, photo and the CTA phone is editable from this page's "Данные
// для сайта" box (bishkek_park_funcity_meta_fields() in inc/meta-boxes.php,
// which also holds each text's default). An empty text field falls back to
// its default, translated via Polylang; an empty photo/phone falls back to
// the RU page's, then to the theme's own.
$bp_funcity_id     = get_queried_object_id();
$bp_funcity_fields = bishkek_park_funcity_meta_fields();

$bp_funcity_text = function ( $key ) use ( $bp_funcity_id, $bp_funcity_fields ) {
	return bishkek_park_get_page_text( $bp_funcity_id, $key, $bp_funcity_fields[ $key ]['default'] );
};

// One entry per play zone. The desktop collage and the "Игровые зоны" cards
// render the same data in the different orders the design uses for each
// context.
$bp_funcity_zones = array(
	'maze'       => array(
		'image' => 'funcity/mazeFuncity.png',
		'icon'  => 'icon-check-circle.svg',
	),
	'trampoline' => array(
		'image' => 'funcity/trampolineFuncity.png',
		'icon'  => 'icon-zap.svg',
	),
	'animators'  => array(
		'image' => 'funcity/animatorFuncity.png',
		'icon'  => 'icon-sparkles.svg',
	),
);

foreach ( $bp_funcity_zones as $bp_funcity_zone_key => $bp_funcity_zone ) {
	$bp_funcity_image_id = (int) bishkek_park_get_page_shared_meta( $bp_funcity_id, "_bp_funcity_zone_{$bp_funcity_zone_key}_image" );
	$bp_funcity_image    = $bp_funcity_image_id ? wp_get_attachment_image_url( $bp_funcity_image_id, 'large' ) : '';

	$bp_funcity_zones[ $bp_funcity_zone_key ] += array(
		'label'     => $bp_funcity_text( "_bp_funcity_zone_{$bp_funcity_zone_key}_label" ),
		'text'      => $bp_funcity_text( "_bp_funcity_zone_{$bp_funcity_zone_key}_text" ),
		'image_url' => $bp_funcity_image ? $bp_funcity_image : bishkek_park_image_url( $bp_funcity_zone['image'] ),
	);
}

$bp_funcity_gallery_order = array( 'trampoline', 'animators', 'maze' );
$bp_funcity_zones_order   = array( 'maze', 'trampoline', 'animators' );

$bp_funcity_rules = array(
	array(
		'icon' => 'icon-footprints.svg',
		'text' => $bp_funcity_text( '_bp_funcity_rule_1' ),
	),
	array(
		'icon' => 'icon-user.svg',
		'text' => $bp_funcity_text( '_bp_funcity_rule_2' ),
	),
	array(
		'icon' => 'icon-camera-off.svg',
		'text' => $bp_funcity_text( '_bp_funcity_rule_3' ),
	),
);

$bp_funcity_phone = bishkek_park_get_page_shared_meta( $bp_funcity_id, '_bp_funcity_cta_phone' );
if ( ! $bp_funcity_phone ) {
	$bp_funcity_phone = bishkek_park_get_contact_phone( 'main' );
}

get_header();
?>
<main class="bp-main bp-funcity">
	<div class="bp-container bp-back-link-wrap" data-bp-reveal>
		<a class="bp-back-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
			<?php pll_esc_html_e( 'Назад на главную' ); ?>
		</a>
	</div>

	<section class="bp-container bp-funcity-hero bp-pattern bp-pattern--light">
		<div class="bp-container bp-funcity-hero__inner" data-bp-reveal>
			<div class="bp-funcity-hero__heading">
				<img class="bp-funcity-hero__logo" src="<?php echo esc_url( bishkek_park_icon_url( 'funcityLogo.svg' ) ); ?>" width="121" height="90" alt="">
				<h1 class="bp-funcity-hero__title"><?php echo esc_html( $bp_funcity_text( '_bp_funcity_title' ) ); ?></h1>
			</div>
			<p class="bp-funcity-hero__subtitle"><?php echo esc_html( $bp_funcity_text( '_bp_funcity_subtitle' ) ); ?></p>
			<p class="bp-funcity-hero__text"><?php echo esc_html( $bp_funcity_text( '_bp_funcity_text' ) ); ?></p>
		</div>
	</section>

	<div class="bp-funcity-gallery" data-bp-reveal-stagger>
		<?php foreach ( $bp_funcity_gallery_order as $bp_funcity_zone_key ) : ?>
			<?php $bp_funcity_zone = $bp_funcity_zones[ $bp_funcity_zone_key ]; ?>
			<figure class="bp-funcity-gallery__item bp-funcity-gallery__item--<?php echo esc_attr( $bp_funcity_zone_key ); ?>">
				<img
					class="bp-funcity-gallery__photo"
					src="<?php echo esc_url( $bp_funcity_zone['image_url'] ); ?>"
					alt="<?php echo esc_attr( $bp_funcity_zone['label'] ); ?>"
				>
				<span class="bp-funcity-badge bp-funcity-badge--<?php echo esc_attr( $bp_funcity_zone_key ); ?>">
					<img src="<?php echo esc_url( bishkek_park_icon_url( $bp_funcity_zone['icon'] ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					<?php echo esc_html( $bp_funcity_zone['label'] ); ?>
				</span>
			</figure>
		<?php endforeach; ?>
	</div>

	<section id="funcity-zones" class="bp-container bp-section bp-funcity-zones">
		<h2 class="bp-section__title" data-bp-reveal><?php echo esc_html( $bp_funcity_text( '_bp_funcity_zones_title' ) ); ?></h2>
		<p class="bp-funcity-section-text" data-bp-reveal><?php echo esc_html( $bp_funcity_text( '_bp_funcity_zones_text' ) ); ?></p>

		<div class="bp-funcity-zones__grid" data-bp-reveal-stagger>
			<?php foreach ( $bp_funcity_zones_order as $bp_funcity_zone_key ) : ?>
				<?php $bp_funcity_zone = $bp_funcity_zones[ $bp_funcity_zone_key ]; ?>
				<article class="bp-funcity-zone bp-funcity-zone--<?php echo esc_attr( $bp_funcity_zone_key ); ?>">
					<?php // alt="" is deliberate: the badge right below repeats the zone name. ?>
					<span class="bp-funcity-zone__image">
						<img src="<?php echo esc_url( $bp_funcity_zone['image_url'] ); ?>" alt="">
					</span>
					<span class="bp-funcity-badge bp-funcity-badge--<?php echo esc_attr( $bp_funcity_zone_key ); ?>">
						<img src="<?php echo esc_url( bishkek_park_icon_url( $bp_funcity_zone['icon'] ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
						<?php echo esc_html( $bp_funcity_zone['label'] ); ?>
					</span>
					<p class="bp-funcity-zone__text"><?php echo esc_html( $bp_funcity_zone['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="bp-container bp-section bp-funcity-rules">
		<h2 class="bp-section__title" data-bp-reveal><?php echo esc_html( $bp_funcity_text( '_bp_funcity_rules_title' ) ); ?></h2>
		<p class="bp-funcity-section-text" data-bp-reveal><?php echo esc_html( $bp_funcity_text( '_bp_funcity_rules_text' ) ); ?></p>

		<ul class="bp-funcity-rules__list" data-bp-reveal-stagger>
			<?php foreach ( $bp_funcity_rules as $bp_funcity_rule ) : ?>
				<li class="bp-funcity-rule">
					<span class="bp-funcity-rule__icon">
						<img src="<?php echo esc_url( bishkek_park_icon_url( $bp_funcity_rule['icon'] ) ); ?>" width="20" height="20" alt="" aria-hidden="true">
					</span>
					<span class="bp-funcity-rule__text"><?php echo esc_html( $bp_funcity_rule['text'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>

	<section class="bp-funcity-cta bp-pattern">
		<div class="bp-container bp-funcity-cta__inner" data-bp-reveal>
			<h2 class="bp-funcity-cta__title"><?php echo esc_html( $bp_funcity_text( '_bp_funcity_cta_title' ) ); ?></h2>
			<p class="bp-funcity-cta__subtitle"><?php echo esc_html( $bp_funcity_text( '_bp_funcity_cta_subtitle' ) ); ?></p>
			<p class="bp-funcity-cta__text"><?php echo esc_html( $bp_funcity_text( '_bp_funcity_cta_text' ) ); ?></p>
			<a class="bp-btn bp-btn--primary" href="<?php echo esc_attr( bishkek_park_get_tel_href( $bp_funcity_phone ) ); ?>"><?php echo esc_html( $bp_funcity_text( '_bp_funcity_cta_button' ) ); ?></a>
		</div>
	</section>
</main>
<?php
get_footer();
