<?php
/**
 * Site header component: utility bar, info bar, and mobile nav.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="bp-site-header">

	<div class="bp-utility-bar">
		<div class="bp-container bp-utility-bar__inner">
			<button type="button" class="bp-menu-toggle" aria-expanded="false" aria-controls="bp-mobile-menu">
				<span class="bp-menu-toggle__icon" aria-hidden="true">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-menu.svg' ) ); ?>" width="20" height="14" alt="">
				</span>
				<span>Меню</span>
			</button>

			<a class="bp-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Bishkek Park">
				<?php echo bishkek_park_get_logo_svg(); ?>
			</a>

			<div class="bp-lang-switch">
				<img class="bp-lang-switch__icon" src="<?php echo esc_url( bishkek_park_icon_url( 'icon-language.svg' ) ); ?>" width="20" height="14" alt="">
				<span class="bp-lang-switch__label">Русский</span>
				<span class="bp-lang-switch__short">RU</span>
				<img class="bp-lang-switch__chevron" src="<?php echo esc_url( bishkek_park_icon_url( 'icon-chevron-down.svg' ) ); ?>" width="10" height="6" alt="">
			</div>
		</div>
	</div>

	<div class="bp-info-bar">
		<div class="bp-container bp-info-bar__inner">
			<div class="bp-info-bar__left">
				<span class="bp-info-bar__item">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-clock.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					Сегодня до 22:00
				</span>
				<span class="bp-info-bar__item">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-map-pin.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					Карта Молла
				</span>
			</div>
			<div class="bp-info-bar__right">
				<span class="bp-info-bar__item">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-parking.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					Парковка
				</span>
			</div>
		</div>
	</div>

	<nav id="bp-mobile-menu" class="bp-mobile-menu" aria-hidden="true">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => false,
				'items_wrap'     => '<ul class="bp-mobile-menu__list">%3$s</ul>',
			)
		);
		?>
	</nav>
</header>
