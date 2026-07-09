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
		<h2>Аренда помещений в торговом центре</h2>
		<p>Откройте свой магазин в одном из лучших ТЦ города. Мы предлагаем помещения под любой формат бизнеса.</p>

		<div class="bp-leasing__features">
			<div class="bp-leasing__feature">
				<span class="bp-leasing__icon">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-area.svg' ) ); ?>" width="20" height="20" alt="">
				</span>
				<div>
					<strong>Площадь от 20 м²</strong>
					<span>Помещения для малого бизнеса, островки, корнеры и якорные магазины</span>
				</div>
			</div>
			<div class="bp-leasing__feature">
				<span class="bp-leasing__icon">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-visitors.svg' ) ); ?>" width="20" height="20" alt="">
				</span>
				<div>
					<strong>Высокая проходимость</strong>
					<span>Более 50 000 посетителей ежедневно, развитая инфраструктура</span>
				</div>
			</div>
			<div class="bp-leasing__feature">
				<span class="bp-leasing__icon">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-flexible-terms.svg' ) ); ?>" width="20" height="20" alt="">
				</span>
				<div>
					<strong>Гибкие условия</strong>
					<span>Краткосрочная и долгосрочная аренда, помощь с оформлением</span>
				</div>
			</div>
		</div>

		<div class="bp-leasing__actions">
			<a href="#" class="bp-btn bp-btn--primary">Оставить заявку</a>
			<a href="#" class="bp-btn bp-btn--outline">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-download.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
				Скачать медиакит
			</a>
		</div>
	</div>
</section>
