<?php
/**
 * Homepage category shortcuts (Shops / Cafes / Cinema).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="bp-container bp-categories">
	<a href="#" class="bp-category-card">
		<span class="bp-category-card__icon">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-category-shopping.svg' ) ); ?>" width="28" height="28" alt="">
		</span>
		<span class="bp-category-card__title">Магазины</span>
		<span class="bp-category-card__text">Шопинг и покупки с кешбэком 10%</span>
	</a>
	<a href="#" class="bp-category-card">
		<span class="bp-category-card__icon">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-category-cafe.svg' ) ); ?>" width="28" height="28" alt="">
		</span>
		<span class="bp-category-card__title">Кафе и рестораны</span>
		<span class="bp-category-card__text">Еда и напитки с кешбэком 10%</span>
	</a>
	<a href="#" class="bp-category-card">
		<span class="bp-category-card__icon">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-category-cinema.svg' ) ); ?>" width="28" height="28" alt="">
		</span>
		<span class="bp-category-card__title">Синематика</span>
		<span class="bp-category-card__text">Кино и развлечения с кешбэком 10%</span>
	</a>
</section>
