<?php
/**
 * Homepage category shortcuts (Shops / Cafes / Cinema).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="bp-container bp-categories">
	<a href="<?php echo esc_url( get_post_type_archive_link( 'bp_shop' ) ); ?>" class="bp-category-card">
		<span class="bp-category-card__icon">
			<img src="<?php echo esc_url( bishkek_park_image_url( 'shops.png' ) ); ?>" width="110" height="110" alt="">
		</span>
		<span class="bp-category-card__title"><?php pll_esc_html_e( 'Магазины' ); ?></span>
		<span class="bp-category-card__text desktop"><?php pll_esc_html_e( 'Шопинг и покупки с кешбэком 10%' ); ?></span>
		<span class="bp-category-card__text mobile"><?php pll_esc_html_e( 'Кешбэк 10%' ); ?></span>
	</a>
	<a href="<?php echo esc_url( get_post_type_archive_link( 'bp_cafe' ) ); ?>" class="bp-category-card">
		<span class="bp-category-card__icon">
			<img src="<?php echo esc_url( bishkek_park_image_url( 'cafe.png' ) ); ?>" width="110" height="110" alt="">
		</span>
		<span class="bp-category-card__title"><?php pll_esc_html_e( 'Кафе' ); ?></span>
		<span class="bp-category-card__text desktop"><?php pll_esc_html_e( 'Еда и напитки с кешбэком 10%' ); ?></span>
		<span class="bp-category-card__text mobile"><?php pll_esc_html_e( 'Кешбэк 10%' ); ?></span>
	</a>
	<a href="<?php echo esc_url( bishkek_park_get_cinematica_link_url() ); ?>" class="bp-category-card" target="_blank" rel="noopener noreferrer">
		<span class="bp-category-card__icon">
			<img src="<?php echo esc_url( bishkek_park_image_url( 'cinema.png' ) ); ?>" width="110" height="110" alt="">
		</span>
		<span class="bp-category-card__title"><?php pll_esc_html_e( 'Синематика' ); ?></span>
		<span class="bp-category-card__text desktop"><?php pll_esc_html_e( 'Кино и развлечения с кешбэком 10%' ); ?></span>
		<span class="bp-category-card__text mobile"><?php pll_esc_html_e( 'Кешбэк 10%' ); ?></span>
	</a>
</section>
