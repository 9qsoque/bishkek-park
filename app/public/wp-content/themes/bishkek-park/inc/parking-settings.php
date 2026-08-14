<?php
/**
 * Admin settings page for the homepage parking widget
 * (template-parts/homepage/parking.php). "Всего мест" and "Свободно мест"
 * aren't tied to any post — they're a standalone site setting, so they get
 * their own top-level admin menu page rather than a post meta box, the same
 * way inc/cinematica-settings.php does for the Cinematica.kg integration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_PARKING_DEFAULT_TOTAL', 840 );
define( 'BISHKEK_PARK_PARKING_DEFAULT_FREE', 245 );

/**
 * Total parking spots shown in the homepage parking widget. Falls back to
 * the current default if no override has been saved.
 */
function bishkek_park_get_parking_total() {
	$value = (int) get_option( 'bishkek_park_parking_total' );

	return $value ? $value : BISHKEK_PARK_PARKING_DEFAULT_TOTAL;
}

/**
 * Free parking spots shown in the homepage parking widget. Falls back to
 * the current default if no override has been saved.
 */
function bishkek_park_get_parking_free() {
	$value = (int) get_option( 'bishkek_park_parking_free' );

	return $value ? $value : BISHKEK_PARK_PARKING_DEFAULT_FREE;
}

/**
 * Adds a top-level "Паркинг" admin menu page. Its raw registration position
 * here doesn't determine where it actually ends up in the sidebar — see
 * inc/admin-menu.php, which reorders the whole top-level menu explicitly —
 * this just needs to avoid colliding with another page's position.
 */
function bishkek_park_register_parking_settings_page() {
	add_menu_page(
		__( 'Паркинг', 'bishkek-park' ),
		__( 'Паркинг', 'bishkek-park' ),
		'manage_options',
		'bishkek-park-parking',
		'bishkek_park_render_parking_settings_page',
		'dashicons-car',
		23
	);
}
add_action( 'admin_menu', 'bishkek_park_register_parking_settings_page' );

/**
 * Registers the two settings this page edits, both non-negative integers.
 */
function bishkek_park_register_parking_settings() {
	register_setting(
		'bishkek_park_parking_settings',
		'bishkek_park_parking_total',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 0,
		)
	);
	register_setting(
		'bishkek_park_parking_settings',
		'bishkek_park_parking_free',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 0,
		)
	);
}
add_action( 'admin_init', 'bishkek_park_register_parking_settings' );

/**
 * Renders the settings form. Both fields are optional — left blank (0), the
 * getters above fall back to the current defaults.
 */
function bishkek_park_render_parking_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Паркинг', 'bishkek-park' ); ?></h1>
		<p>
			<?php esc_html_e( 'Эти числа показываются в блоке "Паркинг в ТЦ" на главной странице.', 'bishkek-park' ); ?>
		</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'bishkek_park_parking_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="bishkek_park_parking_total"><?php esc_html_e( 'Всего мест', 'bishkek-park' ); ?></label>
					</th>
					<td>
						<input
							type="number"
							min="0"
							class="regular-text"
							id="bishkek_park_parking_total"
							name="bishkek_park_parking_total"
							value="<?php echo esc_attr( get_option( 'bishkek_park_parking_total', '' ) ); ?>"
							placeholder="<?php echo esc_attr( BISHKEK_PARK_PARKING_DEFAULT_TOTAL ); ?>"
						>
						<p class="description">
							<?php esc_html_e( 'По умолчанию:', 'bishkek-park' ); ?>
							<?php echo esc_html( BISHKEK_PARK_PARKING_DEFAULT_TOTAL ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="bishkek_park_parking_free"><?php esc_html_e( 'Свободно мест', 'bishkek-park' ); ?></label>
					</th>
					<td>
						<input
							type="number"
							min="0"
							class="regular-text"
							id="bishkek_park_parking_free"
							name="bishkek_park_parking_free"
							value="<?php echo esc_attr( get_option( 'bishkek_park_parking_free', '' ) ); ?>"
							placeholder="<?php echo esc_attr( BISHKEK_PARK_PARKING_DEFAULT_FREE ); ?>"
						>
						<p class="description">
							<?php esc_html_e( 'По умолчанию:', 'bishkek-park' ); ?>
							<?php echo esc_html( BISHKEK_PARK_PARKING_DEFAULT_FREE ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
