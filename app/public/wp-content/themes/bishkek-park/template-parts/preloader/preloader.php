<?php
/**
 * Full-screen loading screen shown until the page's `load` event: the logo
 * (its five petals bloom in and then pulse in sequence, the wordmark fades
 * in) plus an indeterminate progress bar.
 *
 * Only visible under html.bp-preloading, which bishkek_park_preloader_head_script()
 * (functions.php) adds before first paint and removes once the page has
 * loaded, so with JS off this markup simply never shows.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="bp-preloader" role="status" aria-live="polite">
	<div class="bp-preloader__logo" aria-hidden="true">
		<?php echo bishkek_park_get_logo_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<span class="bp-preloader__bar" aria-hidden="true"></span>
	<span class="bp-preloader__label"><?php pll_esc_html_e( 'Загрузка…' ); ?></span>
</div>
