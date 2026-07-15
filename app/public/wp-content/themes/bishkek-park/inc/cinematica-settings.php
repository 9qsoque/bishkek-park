<?php
/**
 * Admin settings page for the Cinematica.kg integration (see
 * inc/cinematica-api.php). Синематика no longer has an editable post type —
 * showtimes come live from the API — so the only thing left to configure
 * from wp-admin is the API endpoint and the public cinema page link, in case
 * Bishkek Park's hall ID (currently 3) ever changes on Cinematica.kg's side.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_CINEMATICA_DEFAULT_API_URL', 'https://cinematica.kg/api/v1/repertory/cinema/3/grouped' );
define( 'BISHKEK_PARK_CINEMATICA_DEFAULT_LINK_URL', 'https://cinematica.kg/cinema/3' );

/**
 * The API endpoint bishkek_park_fetch_cinematica_sessions() (in
 * inc/cinematica-api.php) fetches showtimes from. Falls back to the current
 * default (hall ID 3) if no override has been saved.
 */
function bishkek_park_get_cinematica_api_url() {
	$url = get_option( 'bishkek_park_cinematica_api_url' );

	return $url ? $url : BISHKEK_PARK_CINEMATICA_DEFAULT_API_URL;
}

/**
 * The public Cinematica.kg page for Bishkek Park's hall — used as the
 * click-through target for homepage movie cards and the header's
 * "Синематика" nav item. Falls back to the current default (hall ID 3) if
 * no override has been saved.
 */
function bishkek_park_get_cinematica_link_url() {
	$url = get_option( 'bishkek_park_cinematica_link_url' );

	return $url ? $url : BISHKEK_PARK_CINEMATICA_DEFAULT_LINK_URL;
}

/**
 * Adds a top-level "Синематика" admin menu page — the same slot/icon the
 * old bp_movie CPT used to occupy — now hosting these two settings instead
 * of a post list.
 */
function bishkek_park_register_cinematica_settings_page() {
	add_menu_page(
		__( 'Синематика', 'bishkek-park' ),
		__( 'Синематика', 'bishkek-park' ),
		'manage_options',
		'bishkek-park-cinematica',
		'bishkek_park_render_cinematica_settings_page',
		'dashicons-tickets-alt',
		21
	);
}
add_action( 'admin_menu', 'bishkek_park_register_cinematica_settings_page' );

/**
 * Registers the two settings this page edits, both plain URLs.
 */
function bishkek_park_register_cinematica_settings() {
	register_setting(
		'bishkek_park_cinematica_settings',
		'bishkek_park_cinematica_api_url',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
		)
	);
	register_setting(
		'bishkek_park_cinematica_settings',
		'bishkek_park_cinematica_link_url',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'esc_url_raw',
			'default'           => '',
		)
	);
}
add_action( 'admin_init', 'bishkek_park_register_cinematica_settings' );

/**
 * Renders the settings form. Both fields are optional — left blank, the
 * getters above fall back to the current hall-ID-3 defaults.
 */
function bishkek_park_render_cinematica_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Синематика', 'bishkek-park' ); ?></h1>
		<p>
			<?php
			esc_html_e(
				'Сеансы в разделе "Синематика" на главной странице подтягиваются напрямую из API Cinematica.kg — редактирование сеансов через wp-admin недоступно. Здесь можно указать другой адрес API и ссылку на страницу кинотеатра, если у Bishkek Park в системе Cinematica.kg изменится ID зала (сейчас 3).',
				'bishkek-park'
			);
			?>
		</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'bishkek_park_cinematica_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="bishkek_park_cinematica_api_url"><?php esc_html_e( 'Адрес API', 'bishkek-park' ); ?></label>
					</th>
					<td>
						<input
							type="url"
							class="regular-text code"
							id="bishkek_park_cinematica_api_url"
							name="bishkek_park_cinematica_api_url"
							value="<?php echo esc_attr( get_option( 'bishkek_park_cinematica_api_url', '' ) ); ?>"
							placeholder="<?php echo esc_attr( BISHKEK_PARK_CINEMATICA_DEFAULT_API_URL ); ?>"
						>
						<p class="description">
							<?php esc_html_e( 'По умолчанию:', 'bishkek-park' ); ?>
							<?php echo esc_html( BISHKEK_PARK_CINEMATICA_DEFAULT_API_URL ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="bishkek_park_cinematica_link_url"><?php esc_html_e( 'Ссылка на страницу кинотеатра', 'bishkek-park' ); ?></label>
					</th>
					<td>
						<input
							type="url"
							class="regular-text code"
							id="bishkek_park_cinematica_link_url"
							name="bishkek_park_cinematica_link_url"
							value="<?php echo esc_attr( get_option( 'bishkek_park_cinematica_link_url', '' ) ); ?>"
							placeholder="<?php echo esc_attr( BISHKEK_PARK_CINEMATICA_DEFAULT_LINK_URL ); ?>"
						>
						<p class="description">
							<?php esc_html_e( 'Куда ведут карточки фильмов на главной и пункт меню "Синематика". По умолчанию:', 'bishkek-park' ); ?>
							<?php echo esc_html( BISHKEK_PARK_CINEMATICA_DEFAULT_LINK_URL ); ?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
