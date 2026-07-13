<?php
/**
 * Homepage leasing CTA section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="bp-container">
	<div class="bp-leasing">
		<h2><?php pll_esc_html_e( 'Аренда помещений в торговом центре' ); ?></h2>
		<p><?php pll_esc_html_e( 'Откройте свой магазин в одном из лучших ТЦ города. Мы предлагаем помещения под любой формат бизнеса.' ); ?></p>

		<div class="bp-leasing__features">
			<div class="bp-leasing__feature">
				<span class="bp-leasing__icon">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-area.svg' ) ); ?>" width="20" height="20" alt="">
				</span>
				<div>
					<strong><?php pll_esc_html_e( 'Площадь от 20 м²' ); ?></strong>
					<span><?php pll_esc_html_e( 'Помещения для малого бизнеса, островки, корнеры и якорные магазины' ); ?></span>
				</div>
			</div>
			<div class="bp-leasing__feature">
				<span class="bp-leasing__icon">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-visitors.svg' ) ); ?>" width="20" height="20" alt="">
				</span>
				<div>
					<strong><?php pll_esc_html_e( 'Высокая проходимость' ); ?></strong>
					<span><?php pll_esc_html_e( 'Более 50 000 посетителей ежедневно, развитая инфраструктура' ); ?></span>
				</div>
			</div>
			<div class="bp-leasing__feature">
				<span class="bp-leasing__icon">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-flexible-terms.svg' ) ); ?>" width="20" height="20" alt="">
				</span>
				<div>
					<strong><?php pll_esc_html_e( 'Гибкие условия' ); ?></strong>
					<span><?php pll_esc_html_e( 'Краткосрочная и долгосрочная аренда, помощь с оформлением' ); ?></span>
				</div>
			</div>
		</div>

		<div class="bp-leasing__actions">
			<a href="#" class="bp-btn bp-btn--primary"><?php pll_esc_html_e( 'Оставить заявку' ); ?></a>
			<a href="#" class="bp-btn bp-btn--outline">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-download.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
				<?php pll_esc_html_e( 'Скачать медиакит' ); ?>
			</a>
		</div>
	</div>
</section>
