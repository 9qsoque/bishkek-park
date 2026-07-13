<?php
/**
 * Homepage parking widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="bp-container">
	<div class="bp-parking">
		<div class="bp-parking__text">
			<h2><?php pll_esc_html_e( 'Паркинг в ТЦ' ); ?></h2>
			<p><?php pll_esc_html_e( 'Зарегистрируйтесь и совершите покупку в любом магазине-партнёре.' ); ?></p>
			<a href="#" class="bp-btn bp-btn--primary"><?php pll_esc_html_e( 'Смотреть' ); ?></a>
		</div>
		<div class="bp-parking__stats">
			<div class="bp-parking__stat">
				<span class="bp-parking__stat-num">840</span>
				<span class="bp-parking__stat-label"><?php pll_esc_html_e( 'мест всего' ); ?></span>
			</div>
			<div class="bp-parking__stat">
				<span class="bp-parking__stat-num">245</span>
				<span class="bp-parking__stat-label"><?php pll_esc_html_e( 'свободно' ); ?></span>
			</div>
		</div>
	</div>
</section>
