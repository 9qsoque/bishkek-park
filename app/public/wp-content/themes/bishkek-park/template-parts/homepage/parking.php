<?php
/**
 * Homepage parking widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bp_parking_total = bishkek_park_get_parking_total();
$bp_parking_free  = bishkek_park_get_parking_free();
?>
<section class="bp-container">
	<div class="bp-parking">
		<div class="bp-parking__text">
			<h2><?php pll_esc_html_e( 'Паркинг в ТЦ' ); ?></h2>
			<p><?php pll_esc_html_e( 'Зарегистрируйтесь и совершите покупку в любом магазине-партнёре.' ); ?></p>
		</div>
		<div class="bp-parking__stats">
			<div class="bp-parking__stat">
				<span class="bp-parking__stat-num"><?php echo esc_html( $bp_parking_total ); ?></span>
				<span class="bp-parking__stat-label"><?php pll_esc_html_e( 'мест всего' ); ?></span>
			</div>
			<div class="bp-parking__stat">
				<span class="bp-parking__stat-num"><?php echo esc_html( $bp_parking_free ); ?></span>
				<span class="bp-parking__stat-label"><?php pll_esc_html_e( 'свободно' ); ?></span>
			</div>
		</div>
	</div>
</section>
