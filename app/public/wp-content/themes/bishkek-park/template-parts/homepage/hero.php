<?php
/**
 * Homepage hero banner.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="bp-container bp-hero-wrap">
	<div class="bp-hero">
		<div class="bp-hero__content">
			<span class="bp-badge">СЕЗОННАЯ РАСПРОДАЖА</span>
			<h1 class="bp-hero__title">Скидки до 30% в&nbsp;любимых магазинах</h1>
			<p class="bp-hero__text">Покупайте онлайн и возвращайте деньги за каждую покупку</p>
			<a href="#" class="bp-btn bp-btn--primary">Подробнее</a>
		</div>
		<div class="bp-hero__art" aria-hidden="true">
			<img src="<?php echo esc_url( BISHKEK_PARK_URI . '/assets/images/heroImg.png' ); ?>" width="400" height="200" alt="">
		</div>
	</div>
	<div class="bp-hero__dots">
		<span class="is-active"></span>
		<span></span>
	</div>
</section>
