<?php
/**
 * Site header component: utility bar, primary nav, info bar, and mobile nav.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bp_nav_items = array(
	array(
		'label' => 'Шопинг',
		'icon'  => 'icon-shopping-bag.svg',
		'url'   => get_post_type_archive_link( 'bp_shop' ),
	),
	array(
		'label'    => 'Синематика',
		'icon'     => 'icon-clapperboard.svg',
		'url'      => bishkek_park_get_cinematica_link_url(),
		'external' => true,
	),
	array(
		'label' => 'Funcity',
		'icon'  => 'icon-star.svg',
		'url'   => bishkek_park_get_funcity_page_url(),
	),
	array(
		'label' => 'Кафе и рестораны',
		'icon'  => 'icon-utensils.svg',
		'url'   => get_post_type_archive_link( 'bp_cafe' ),
	),
	array(
		'label' => 'Мероприятия',
		'icon'  => 'icon-calendar.svg',
		'url'   => get_post_type_archive_link( 'bp_event' ),
	),
	array(
		'label' => 'Контакты',
		'icon'  => 'icon-phone-outline.svg',
		'url'   => bishkek_park_get_contacts_page_url(),
	),
);
// Nav item labels above are registered as translatable strings in
// inc/translatable-strings.php and translated at output time below.

$bp_lang_switch_languages = function_exists( 'pll_the_languages' )
	? pll_the_languages(
		array(
			'raw'           => 1,
			'hide_if_empty' => 0,
		)
	)
	: array();

// Polylang links a language with no translation of the current post to that
// language's homepage by default. Link to the current (default-language)
// URL instead, so switching language keeps showing this content — in the
// default language, per the fallback pattern documented in CLAUDE.md —
// instead of bouncing the visitor to the homepage.
if ( $bp_lang_switch_languages ) {
	$bp_lang_switch_current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	foreach ( $bp_lang_switch_languages as $bp_lang_switch_key => $bp_lang_switch_language ) {
		if ( ! empty( $bp_lang_switch_language['no_translation'] ) ) {
			$bp_lang_switch_languages[ $bp_lang_switch_key ]['url'] = $bp_lang_switch_current_url;
		}
	}
}

$bp_lang_switch_current = null;
foreach ( $bp_lang_switch_languages as $bp_lang_switch_language ) {
	if ( ! empty( $bp_lang_switch_language['current_lang'] ) ) {
		$bp_lang_switch_current = $bp_lang_switch_language;
		break;
	}
}
?>
<header class="bp-site-header">

	<div class="bp-utility-bar">
		<div class="bp-container bp-utility-bar__inner">
			<button type="button" class="bp-menu-toggle" aria-expanded="false" aria-controls="bp-mobile-menu">
				<span class="bp-menu-toggle__icon" aria-hidden="true">
					<img class="bp-menu-toggle__icon-open" src="<?php echo esc_url( bishkek_park_icon_url( 'icon-menu.svg' ) ); ?>" width="20" height="14" alt="">
					<img class="bp-menu-toggle__icon-close" src="<?php echo esc_url( bishkek_park_icon_url( 'icon-close.svg' ) ); ?>" width="16" height="16" alt="">
				</span>
				<span class="bp-menu-toggle__label"><span class="bp-menu-toggle__label-open"><?php pll_esc_html_e( 'Меню' ); ?></span><span class="bp-menu-toggle__label-close"><?php pll_esc_html_e( 'Закрыть' ); ?></span></span>
			</button>

			<a class="bp-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Bishkek Park">
				<?php echo bishkek_park_get_logo_svg(); ?>
			</a>

			<div class="bp-lang-switch" data-bp-lang-switch>
				<?php if ( count( $bp_lang_switch_languages ) > 1 ) : ?>
					<button type="button" class="bp-lang-switch__toggle" aria-expanded="false" aria-haspopup="true" aria-controls="bp-lang-switch-menu">
						<img class="bp-lang-switch__icon" src="<?php echo esc_url( bishkek_park_icon_url( 'icon-language.svg' ) ); ?>" width="20" height="14" alt="">
						<span class="bp-lang-switch__label"><?php echo $bp_lang_switch_current ? esc_html( $bp_lang_switch_current['name'] ) : pll_esc_html__( 'Русский' ); ?></span>
						<span class="bp-lang-switch__short"><?php echo esc_html( strtoupper( $bp_lang_switch_current ? $bp_lang_switch_current['slug'] : 'ru' ) ); ?></span>
						<img class="bp-lang-switch__chevron" src="<?php echo esc_url( bishkek_park_icon_url( 'icon-chevron-down.svg' ) ); ?>" width="10" height="6" alt="">
					</button>
					<ul class="bp-lang-switch__menu" id="bp-lang-switch-menu" role="menu">
						<?php foreach ( $bp_lang_switch_languages as $bp_lang_switch_language ) : ?>
							<li role="none">
								<a
									role="menuitem"
									href="<?php echo esc_url( $bp_lang_switch_language['url'] ); ?>"
									class="bp-lang-switch__option<?php echo ! empty( $bp_lang_switch_language['current_lang'] ) ? ' is-active' : ''; ?>"
									hreflang="<?php echo esc_attr( $bp_lang_switch_language['slug'] ); ?>"
								>
									<span class="bp-lang-switch__option-short"><?php echo esc_html( strtoupper( $bp_lang_switch_language['slug'] ) ); ?></span>
									<span class="bp-lang-switch__option-name"><?php echo esc_html( $bp_lang_switch_language['name'] ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<img class="bp-lang-switch__icon" src="<?php echo esc_url( bishkek_park_icon_url( 'icon-language.svg' ) ); ?>" width="20" height="14" alt="">
					<span class="bp-lang-switch__label"><?php pll_esc_html_e( 'Русский' ); ?></span>
					<span class="bp-lang-switch__short">RU</span>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<nav id="bp-mobile-menu" class="bp-mobile-menu" aria-hidden="true">
		<div class="bp-container bp-mobile-menu__inner">
			<ul class="bp-mobile-menu__list">
				<?php foreach ( $bp_nav_items as $bp_nav_item ) : ?>
					<li>
						<a href="<?php echo esc_url( $bp_nav_item['url'] ); ?>"<?php echo ! empty( $bp_nav_item['external'] ) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
							<img src="<?php echo esc_url( bishkek_park_icon_url( $bp_nav_item['icon'] ) ); ?>" width="22" height="22" alt="" aria-hidden="true">
							<?php pll_esc_html_e( $bp_nav_item['label'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</nav>

	<div class="bp-info-bar">
		<div class="bp-container bp-info-bar__inner">
			<div class="bp-info-bar__left">
				<span class="bp-info-bar__item">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-clock.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					<?php pll_esc_html_e( 'Сегодня до 22:00' ); ?>
				</span>
				<a class="bp-info-bar__item" href="<?php echo esc_url( bishkek_park_get_mall_map_page_url() ); ?>">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-map-pin.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					<?php pll_esc_html_e( 'Карта Молла' ); ?>
				</a>
			</div>
			<div class="bp-info-bar__right">
				<span class="bp-info-bar__item">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-parking.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					<?php pll_esc_html_e( 'Парковка' ); ?>
				</span>
			</div>
		</div>
	</div>
</header>
