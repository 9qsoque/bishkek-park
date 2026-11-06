<?php
/**
 * Admin settings page "Номера для связи": every phone number the site links
 * to (header/footer, contacts page, about page, homepage leasing CTA,
 * footer's Рекламные услуги/Вакансии/Обратная связь links), edited in one
 * place. Each number is either a plain call (`tel:`) or a WhatsApp chat
 * (`wa.me`) — fixed per number, see bishkek_park_contact_phone_defaults().
 * Same standalone-settings-page pattern as inc/parking-settings.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The site's contact numbers, keyed by purpose. `label` is the wp-admin
 * field label, `used` says where on the site the number appears, `number`
 * is shown while nothing has been saved, and `type` ('tel' or 'whatsapp')
 * is what kind of link it becomes.
 */
function bishkek_park_contact_phone_defaults() {
	return array(
		'main'        => array(
			'label'  => __( 'Основной телефон («Связь»)', 'bishkek-park' ),
			'used'   => __( 'Звонок. Футер, страница «Контакты», кнопка на странице «О нас», блок «Остались вопросы?» на странице Funcity (если там не указан свой номер).', 'bishkek-park' ),
			'number' => '+996 (312) 312 031',
			'type'   => 'tel',
		),
		'leasing'     => array(
			'label'  => __( 'Аренда', 'bishkek-park' ),
			'used'   => __( 'WhatsApp. Футер, страница «Контакты», кнопка «Оставить заявку» на главной.', 'bishkek-park' ),
			'number' => '+996 555 777 945',
			'type'   => 'whatsapp',
		),
		'sales'       => array(
			'label'  => __( 'Продажи', 'bishkek-park' ),
			'used'   => __( 'WhatsApp. Футер, страница «Контакты».', 'bishkek-park' ),
			'number' => '+996 998 111 333',
			'type'   => 'whatsapp',
		),
		'advertising' => array(
			'label'  => __( 'Рекламные услуги', 'bishkek-park' ),
			'used'   => __( 'WhatsApp. Ссылка «Рекламные услуги» в футере.', 'bishkek-park' ),
			'number' => '+996 998 111 333',
			'type'   => 'whatsapp',
		),
		'vacancies'   => array(
			'label'  => __( 'Вакансии', 'bishkek-park' ),
			'used'   => __( 'WhatsApp. Ссылка «Вакансии» в футере.', 'bishkek-park' ),
			'number' => '+996 509 111 365',
			'type'   => 'whatsapp',
		),
		'feedback'    => array(
			'label'  => __( 'Обратная связь', 'bishkek-park' ),
			'used'   => __( 'Звонок. Ссылка «Обратная связь» в футере.', 'bishkek-park' ),
			'number' => '+996 998 111 333',
			'type'   => 'tel',
		),
	);
}

/**
 * Display form of one contact number (e.g. "+996 (312) 312 031"): the saved
 * value, or the default if the field was left blank.
 */
function bishkek_park_get_contact_phone( $key ) {
	$defaults = bishkek_park_contact_phone_defaults();
	$number   = get_option( "bishkek_park_phone_{$key}" );

	return $number ? $number : $defaults[ $key ]['number'];
}

/**
 * Href for one contact number — a `tel:` link or a wa.me chat, depending
 * on the number's type.
 */
function bishkek_park_get_contact_phone_url( $key ) {
	$defaults = bishkek_park_contact_phone_defaults();
	$number   = bishkek_park_get_contact_phone( $key );

	if ( 'whatsapp' === $defaults[ $key ]['type'] ) {
		return 'https://wa.me/' . preg_replace( '/\D/', '', $number );
	}

	return bishkek_park_get_tel_href( $number );
}

/**
 * Prints the href (plus target/rel for WhatsApp links, which open in a new
 * tab) for an <a> pointing at one contact number.
 */
function bishkek_park_contact_phone_link_attrs( $key ) {
	$defaults = bishkek_park_contact_phone_defaults();

	echo 'href="' . esc_url( bishkek_park_get_contact_phone_url( $key ), array( 'tel', 'https' ) ) . '"';

	if ( 'whatsapp' === $defaults[ $key ]['type'] ) {
		echo ' target="_blank" rel="noopener noreferrer"';
	}
}

/**
 * Adds a top-level "Номера для связи" admin menu page. Its position is
 * decided by inc/admin-menu.php, not the number here.
 */
function bishkek_park_register_contact_phones_settings_page() {
	add_menu_page(
		__( 'Номера для связи', 'bishkek-park' ),
		__( 'Номера для связи', 'bishkek-park' ),
		BISHKEK_PARK_PHONES_CAPABILITY,
		'bishkek-park-phones',
		'bishkek_park_render_contact_phones_settings_page',
		'dashicons-phone',
		24
	);
}
add_action( 'admin_menu', 'bishkek_park_register_contact_phones_settings_page' );

/**
 * Registers one plain-text setting per contact number.
 */
function bishkek_park_register_contact_phones_settings() {
	foreach ( array_keys( bishkek_park_contact_phone_defaults() ) as $key ) {
		register_setting(
			'bishkek_park_phones_settings',
			"bishkek_park_phone_{$key}",
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
	}
}
add_action( 'admin_init', 'bishkek_park_register_contact_phones_settings' );

/**
 * Renders the settings form. Every field is optional — left blank, the
 * number shown greyed out (the default) is used.
 */
function bishkek_park_render_contact_phones_settings_page() {
	if ( ! current_user_can( BISHKEK_PARK_PHONES_CAPABILITY ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Номера для связи', 'bishkek-park' ); ?></h1>
		<p><?php esc_html_e( 'Все номера телефонов, на которые ведут ссылки на сайте. Номер можно вводить в любом виде, например +996 (312) 312 031 — для ссылки лишние символы убираются автоматически. Пустое поле — используется номер, видный серым.', 'bishkek-park' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'bishkek_park_phones_settings' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( bishkek_park_contact_phone_defaults() as $key => $phone ) : ?>
					<tr>
						<th scope="row">
							<label for="bishkek_park_phone_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $phone['label'] ); ?></label>
						</th>
						<td>
							<input
								type="text"
								class="regular-text"
								id="bishkek_park_phone_<?php echo esc_attr( $key ); ?>"
								name="bishkek_park_phone_<?php echo esc_attr( $key ); ?>"
								value="<?php echo esc_attr( get_option( "bishkek_park_phone_{$key}", '' ) ); ?>"
								placeholder="<?php echo esc_attr( $phone['number'] ); ?>"
							>
							<p class="description"><?php echo esc_html( $phone['used'] ); ?></p>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
