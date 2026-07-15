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

// One entry per play zone; label/text values are registered with Polylang in
// inc/translatable-strings.php and translated at output time via pll_*().
// The desktop collage and the "Игровые зоны" cards render the same data in
// the different orders the design uses for each context.
$bp_funcity_zones = array(
	'maze'       => array(
		'label' => 'Мягкий лабиринт',
		'text'  => 'Специальная зона для малышей: мягкие туннели, блоки и безопасные площадки для первых шагов.',
		'image' => 'funcity/mazeFuncity.png',
		'icon'  => 'icon-check-circle.svg',
	),
	'trampoline' => array(
		'label' => 'Батутный парк',
		'text'  => 'Прыжки, сальто и весёлая физическая нагрузка под музыку и в хорошей компании.',
		'image' => 'funcity/trampolineFuncity.png',
		'icon'  => 'icon-zap.svg',
	),
	'animators'  => array(
		'label' => 'Аниматоры и развлечения',
		'text'  => 'Шоу, мастер-классы и интерактивные программы для детей и взрослых вместе.',
		'image' => 'funcity/animatorFuncity.png',
		'icon'  => 'icon-sparkles.svg',
	),
);

$bp_funcity_gallery_order = array( 'trampoline', 'animators', 'maze' );
$bp_funcity_zones_order   = array( 'maze', 'trampoline', 'animators' );

$bp_funcity_rules = array(
	array(
		'icon' => 'icon-footprints.svg',
		'text' => 'Обувь: носки обязательны. Взрослым рекомендуется сменная обувь.',
	),
	array(
		'icon' => 'icon-user.svg',
		'text' => 'Возраст без сопровождения: от 12 лет. Младшие дети должны находиться под присмотром взрослого.',
	),
	array(
		'icon' => 'icon-camera-off.svg',
		'text' => 'Запрещено: еда и напитки в игровых зонах, фото со вспышкой и любые действия, создающие опасность.',
	),
);

get_header();
?>
<main class="bp-main bp-funcity">
	<div class="bp-container bp-back-link-wrap">
		<a class="bp-back-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
			<?php pll_esc_html_e( 'Назад на главную' ); ?>
		</a>
	</div>

	<section class="bp-container bp-funcity-hero">
		<div class="bp-container bp-funcity-hero__inner">
			<div class="bp-funcity-hero__heading">
				<img class="bp-funcity-hero__logo" src="<?php echo esc_url( bishkek_park_icon_url( 'funcityLogo.svg' ) ); ?>" width="121" height="90" alt="">
				<h1 class="bp-funcity-hero__title"><?php pll_esc_html_e( 'FUNCITY' ); ?></h1>
			</div>
			<p class="bp-funcity-hero__subtitle"><?php pll_esc_html_e( 'Большая детская площадка в БишкекПарке' ); ?></p>
			<p class="bp-funcity-hero__text"><?php pll_esc_html_e( 'Игровые зоны, батуты и аниматоры - всё для незабываемого дня в торговом центре.' ); ?></p>
		</div>
	</section>

	<div class="bp-funcity-gallery">
		<?php foreach ( $bp_funcity_gallery_order as $bp_funcity_zone_key ) : ?>
			<?php $bp_funcity_zone = $bp_funcity_zones[ $bp_funcity_zone_key ]; ?>
			<figure class="bp-funcity-gallery__item bp-funcity-gallery__item--<?php echo esc_attr( $bp_funcity_zone_key ); ?>">
				<img
					class="bp-funcity-gallery__photo"
					src="<?php echo esc_url( bishkek_park_image_url( $bp_funcity_zone['image'] ) ); ?>"
					alt="<?php pll_esc_attr_e( $bp_funcity_zone['label'] ); ?>"
				>
				<span class="bp-funcity-badge bp-funcity-badge--<?php echo esc_attr( $bp_funcity_zone_key ); ?>">
					<img src="<?php echo esc_url( bishkek_park_icon_url( $bp_funcity_zone['icon'] ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					<?php pll_esc_html_e( $bp_funcity_zone['label'] ); ?>
				</span>
			</figure>
		<?php endforeach; ?>
	</div>

	<section id="funcity-zones" class="bp-container bp-section bp-funcity-zones">
		<h2 class="bp-section__title"><?php pll_esc_html_e( 'Игровые зоны' ); ?></h2>
		<p class="bp-funcity-section-text"><?php pll_esc_html_e( 'Подберите зону по возрасту и интересам - для малышей, старших детей и всей семьи.' ); ?></p>

		<div class="bp-funcity-zones__grid">
			<?php foreach ( $bp_funcity_zones_order as $bp_funcity_zone_key ) : ?>
				<?php $bp_funcity_zone = $bp_funcity_zones[ $bp_funcity_zone_key ]; ?>
				<article class="bp-funcity-zone">
					<?php // alt="" is deliberate: the badge right below repeats the zone name. ?>
					<span class="bp-funcity-zone__image">
						<img src="<?php echo esc_url( bishkek_park_image_url( $bp_funcity_zone['image'] ) ); ?>" alt="">
					</span>
					<span class="bp-funcity-badge bp-funcity-badge--<?php echo esc_attr( $bp_funcity_zone_key ); ?>">
						<img src="<?php echo esc_url( bishkek_park_icon_url( $bp_funcity_zone['icon'] ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
						<?php pll_esc_html_e( $bp_funcity_zone['label'] ); ?>
					</span>
					<p class="bp-funcity-zone__text"><?php pll_esc_html_e( $bp_funcity_zone['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="bp-container bp-section bp-funcity-rules">
		<h2 class="bp-section__title"><?php pll_esc_html_e( 'Правила посещения' ); ?></h2>
		<p class="bp-funcity-section-text"><?php pll_esc_html_e( 'Чтобы всем было комфортно и безопасно, просим соблюдать простые правила.' ); ?></p>

		<ul class="bp-funcity-rules__list">
			<?php foreach ( $bp_funcity_rules as $bp_funcity_rule ) : ?>
				<li class="bp-funcity-rule">
					<span class="bp-funcity-rule__icon">
						<img src="<?php echo esc_url( bishkek_park_icon_url( $bp_funcity_rule['icon'] ) ); ?>" width="20" height="20" alt="" aria-hidden="true">
					</span>
					<span class="bp-funcity-rule__text"><?php pll_esc_html_e( $bp_funcity_rule['text'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>

	<section class="bp-funcity-cta">
		<div class="bp-container bp-funcity-cta__inner">
			<h2 class="bp-funcity-cta__title"><?php pll_esc_html_e( 'Остались вопросы?' ); ?></h2>
			<p class="bp-funcity-cta__subtitle"><?php pll_esc_html_e( 'Свяжитесь с нами' ); ?></p>
			<p class="bp-funcity-cta__text"><?php pll_esc_html_e( 'Мы готовы ответить на любые ваши вопросы' ); ?></p>
			<a class="bp-btn bp-btn--primary" href="tel:+996312312031"><?php pll_esc_html_e( 'Связаться с нами' ); ?></a>
		</div>
	</section>
</main>
<?php
get_footer();
