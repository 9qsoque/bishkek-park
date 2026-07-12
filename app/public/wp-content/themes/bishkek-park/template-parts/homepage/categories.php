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
			<img src="<?php echo esc_url( bishkek_park_image_url( 'shops.png' ) ); ?>" width="110" height="110" alt="">
		</span>
		<span class="bp-category-card__title">Магазины</span>
		<span class="bp-category-card__text desktop">Шопинг и покупки с кешбэком 10%</span>
		<span class="bp-category-card__text mobile">Кешбэк 10%</span>
	</a>
	<a href="#" class="bp-category-card">
		<span class="bp-category-card__icon">
			<img src="<?php echo esc_url( bishkek_park_image_url( 'cafe.png' ) ); ?>" width="110" height="110" alt="">
		</span>
		<span class="bp-category-card__title">Кафе</span>
		<span class="bp-category-card__text desktop">Еда и напитки с кешбэком 10%</span>
		<span class="bp-category-card__text mobile">Кешбэк 10%</span>
	</a>
	<a href="#" class="bp-category-card">
		<span class="bp-category-card__icon">
			<img src="<?php echo esc_url( bishkek_park_image_url( 'cinema.png' ) ); ?>" width="110" height="110" alt="">
		</span>
		<span class="bp-category-card__title">Синематика</span>
		<span class="bp-category-card__text desktop">Кино и развлечения с кешбэком 10%</span>
		<span class="bp-category-card__text mobile">Кешбэк 10%</span>
	</a>
</section>
