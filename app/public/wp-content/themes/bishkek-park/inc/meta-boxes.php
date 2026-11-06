<?php
/**
 * Admin meta boxes (custom fields) for the Shops and Events post types,
 * plus pages (currently just the SEO description field), and the
 * sanitize/save logic behind them. Синематика (bp_movie) intentionally has
 * no fields here — its homepage section is populated live from the
 * Cinematica.kg API instead of wp-admin entry, see inc/cinematica-api.php
 * and inc/cinematica-settings.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Editable fields for the Контакты page template only (address/hours/phone,
 * the "Арендаторам" block, and the 6 FAQ pairs) — merged into the 'page'
 * entry below when the page being edited uses
 * template-parts/contacts/page-contacts.php, so other pages (Главная,
 * Funcity, ...) don't get a wall of irrelevant fields in their "Данные для
 * сайта" box. Rendered with the same defaults the template itself falls
 * back to when a field is left empty — see template-parts/contacts/page-contacts.php.
 */
function bishkek_park_contacts_meta_fields() {
	$fields = array(
		'_bp_contacts_address'                => array( 'label' => __( 'Адрес', 'bishkek-park' ), 'description' => __( 'Если не заполнено, показывается "148В, ул. Киевская, 720001 Бишкек / Кыргызстан"', 'bishkek-park' ) ),
		'_bp_contacts_hours'                   => array( 'label' => __( 'Часы работы ТЦ', 'bishkek-park' ), 'description' => __( 'Если не заполнено, показывается "Ежедневно: 10:00 — 22:00"', 'bishkek-park' ) ),
		'_bp_contacts_tenants_text'            => array( 'label' => __( 'Арендаторам: текст', 'bishkek-park' ), 'description' => __( 'Если не заполнено, показывается стандартный текст с адресом info@bishkekpark.kg', 'bishkek-park' ), 'type' => 'textarea' ),
		'_bp_contacts_tenants_download_label'  => array( 'label' => __( 'Арендаторам: текст ссылки на заявку', 'bishkek-park' ), 'description' => __( 'Если не заполнено, показывается "Скачать заявку на аренду помещения"', 'bishkek-park' ) ),
		'_bp_contacts_tenants_download_url'    => array( 'label' => __( 'Арендаторам: ссылка на файл заявки', 'bishkek-park' ), 'description' => __( 'Куда ведёт ссылка "Скачать заявку". Если не заполнено, ссылка никуда не ведёт (#)', 'bishkek-park' ) ),
		'_bp_contacts_tenants_hours'           => array( 'label' => __( 'Арендаторам: часы приёма', 'bishkek-park' ), 'description' => __( 'По одной строке на часы работы (показывается только на десктопе). Если не заполнено, показывается стандартный график', 'bishkek-park' ), 'type' => 'textarea' ),
	);

	for ( $i = 1; $i <= 6; $i++ ) {
		$fields[ "_bp_contacts_faq_q{$i}" ] = array(
			// translators: %d is the FAQ item number (1-6).
			'label'       => sprintf( __( 'Вопрос %d', 'bishkek-park' ), $i ),
			'description' => '',
		);
		$fields[ "_bp_contacts_faq_a{$i}" ] = array(
			// translators: %d is the FAQ item number (1-6).
			'label'       => sprintf( __( 'Ответ %d', 'bishkek-park' ), $i ),
			'description' => '',
			'type'        => 'textarea',
		);
	}

	return $fields;
}

/**
 * Editable fields for the Funcity page template only — merged into the 'page'
 * entry below the same way as bishkek_park_contacts_meta_fields(). Every text
 * field's `default` is the text the template shows while the field is empty
 * (shown greyed out as the field's placeholder); those defaults are the
 * Polylang-registered strings in inc/translatable-strings.php, so their KY
 * versions come from Languages -> String translations. The template reads
 * the defaults from here (template-parts/funcity/page-funcity.php), so this
 * is the one place they're defined.
 */
function bishkek_park_funcity_meta_fields() {
	$empty_hint = __( 'Пустое поле — показывается текст, видный серым. Его перевод на кыргызский — в Languages → Переводы строк.', 'bishkek-park' );
	$image_hint = __( 'Если не выбрано, показывается стандартное фото. На кыргызской версии страницы используется фото с русской, если там не выбрано своё.', 'bishkek-park' );

	$fields = array(
		'_bp_funcity_title'       => array( 'label' => __( 'Первый экран: заголовок', 'bishkek-park' ), 'description' => $empty_hint, 'default' => 'FUNCITY' ),
		'_bp_funcity_subtitle'    => array( 'label' => __( 'Первый экран: подзаголовок', 'bishkek-park' ), 'default' => 'Большая детская площадка в БишкекПарке' ),
		'_bp_funcity_text'        => array( 'label' => __( 'Первый экран: текст', 'bishkek-park' ), 'type' => 'textarea', 'default' => 'Игровые зоны, батуты и аниматоры - всё для незабываемого дня в торговом центре.' ),
		'_bp_funcity_zones_title' => array( 'label' => __( 'Игровые зоны: заголовок раздела', 'bishkek-park' ), 'default' => 'Игровые зоны' ),
		'_bp_funcity_zones_text'  => array( 'label' => __( 'Игровые зоны: текст раздела', 'bishkek-park' ), 'type' => 'textarea', 'default' => 'Подберите зону по возрасту и интересам - для малышей, старших детей и всей семьи.' ),
	);

	$zones = array(
		'maze'       => array(
			'label' => 'Мягкий лабиринт',
			'text'  => 'Специальная зона для малышей: мягкие туннели, блоки и безопасные площадки для первых шагов.',
		),
		'trampoline' => array(
			'label' => 'Батутный парк',
			'text'  => 'Прыжки, сальто и весёлая физическая нагрузка под музыку и в хорошей компании.',
		),
		'animators'  => array(
			'label' => 'Аниматоры и развлечения',
			'text'  => 'Шоу, мастер-классы и интерактивные программы для детей и взрослых вместе.',
		),
	);

	$zone_number = 0;
	foreach ( $zones as $zone => $defaults ) {
		$zone_number++;
		// translators: %d is the play zone number (1-3).
		$prefix = sprintf( __( 'Зона %d', 'bishkek-park' ), $zone_number );

		$fields[ "_bp_funcity_zone_{$zone}_label" ] = array( 'label' => $prefix . ': ' . __( 'название', 'bishkek-park' ), 'default' => $defaults['label'] );
		$fields[ "_bp_funcity_zone_{$zone}_text" ]  = array( 'label' => $prefix . ': ' . __( 'описание', 'bishkek-park' ), 'type' => 'textarea', 'default' => $defaults['text'] );
		$fields[ "_bp_funcity_zone_{$zone}_image" ] = array( 'label' => $prefix . ': ' . __( 'фото', 'bishkek-park' ), 'type' => 'image', 'description' => $image_hint );
	}

	$fields += array(
		'_bp_funcity_rules_title' => array( 'label' => __( 'Правила: заголовок раздела', 'bishkek-park' ), 'default' => 'Правила посещения' ),
		'_bp_funcity_rules_text'  => array( 'label' => __( 'Правила: текст раздела', 'bishkek-park' ), 'type' => 'textarea', 'default' => 'Чтобы всем было комфортно и безопасно, просим соблюдать простые правила.' ),
		'_bp_funcity_rule_1'      => array( 'label' => __( 'Правило 1', 'bishkek-park' ), 'type' => 'textarea', 'default' => 'Обувь: носки обязательны. Взрослым рекомендуется сменная обувь.' ),
		'_bp_funcity_rule_2'      => array( 'label' => __( 'Правило 2', 'bishkek-park' ), 'type' => 'textarea', 'default' => 'Возраст без сопровождения: от 12 лет. Младшие дети должны находиться под присмотром взрослого.' ),
		'_bp_funcity_rule_3'      => array( 'label' => __( 'Правило 3', 'bishkek-park' ), 'type' => 'textarea', 'default' => 'Запрещено: еда и напитки в игровых зонах, фото со вспышкой и любые действия, создающие опасность.' ),
		'_bp_funcity_cta_title'   => array( 'label' => __( 'Блок «Остались вопросы?»: заголовок', 'bishkek-park' ), 'default' => 'Остались вопросы?' ),
		'_bp_funcity_cta_subtitle' => array( 'label' => __( 'Блок «Остались вопросы?»: подзаголовок', 'bishkek-park' ), 'default' => 'Свяжитесь с нами' ),
		'_bp_funcity_cta_text'    => array( 'label' => __( 'Блок «Остались вопросы?»: текст', 'bishkek-park' ), 'default' => 'Мы готовы ответить на любые ваши вопросы' ),
		'_bp_funcity_cta_button'  => array( 'label' => __( 'Блок «Остались вопросы?»: текст кнопки', 'bishkek-park' ), 'default' => 'Связаться с нами' ),
		'_bp_funcity_cta_phone'   => array(
			'label'       => __( 'Блок «Остались вопросы?»: телефон для кнопки', 'bishkek-park' ),
			'description' => __( 'Например: +996 (312) 312 031. Если не заполнено, используется основной телефон со страницы «Номера для связи». На кыргызской версии страницы используется номер с русской, если там не указан свой.', 'bishkek-park' ),
		),
	);

	return $fields;
}

/**
 * Value => label options for the `_bp_floor` select field, shared by
 * `bp_shop` and `bp_cafe`. Values match `bishkek_park_get_map_floors()`
 * (inc/mall-map.php) so shop/cafe floor assignments always line up with the
 * floors the interactive map knows about; labels go through
 * `bishkek_park_get_floor_number_label()` (functions.php) so the basement
 * floor reads "М1" the same way it does everywhere else on the site. The
 * leading blank option lets a shop/cafe be saved with no floor set.
 */
function bishkek_park_shop_floor_field_options() {
	$options = array( '' => __( '— Не выбрано —', 'bishkek-park' ) );

	foreach ( bishkek_park_get_map_floors() as $floor ) {
		$options[ $floor ] = bishkek_park_get_floor_number_label( $floor );
	}

	return $options;
}

/**
 * Field definitions per post type: meta key => [ label, description, type,
 * max_length, sanitize_callback, default ]. `type` is 'text' (default),
 * 'textarea', 'select' (dropdown; needs an `options` array of value =>
 * label), 'date' (native HTML5 date-picker input; stored as an ISO `Y-m-d`
 * string — see bishkek_park_format_event_date() in functions.php for turning
 * that into the displayed "15 июля 2025" string), or 'image' (a media
 * library picker; stores the attachment ID).
 * `default` (optional) is shown as the text/textarea's placeholder — the text
 * the template falls back to when the field is left empty.
 * `max_length` (optional) caps the field at N characters — enforced both as
 * the textarea's `maxlength` attribute and as a hard truncation on save, so
 * long-winded content can't break the layout it's rendered into.
 * `sanitize_callback` (optional) is called on the value after the base
 * sanitize, for one-off input normalization (see `_bp_floor` below). Keeping
 * this data-driven means one render + one save function covers every post
 * type instead of duplicating boilerplate for each one.
 * `$post` (optional) is only used to detect the Контакты page template —
 * see bishkek_park_contacts_meta_fields() above.
 */
function bishkek_park_meta_fields( $post_type, $post = null ) {
	$fields = array(
		'bp_shop'  => array(
			'_bp_category'    => array( 'label' => __( 'Категория', 'bishkek-park' ), 'description' => __( 'Например: Одежда и аксессуары', 'bishkek-park' ) ),
			'_bp_floor'       => array(
				'label'             => __( 'Этаж', 'bishkek-park' ),
				'description'       => __( 'Слово «Этаж» добавляется на сайте автоматически', 'bishkek-park' ),
				'type'              => 'select',
				'options'           => bishkek_park_shop_floor_field_options(),
				'sanitize_callback' => 'bishkek_park_sanitize_shop_floor',
			),
			'_bp_description' => array( 'label' => __( 'Описание', 'bishkek-park' ), 'description' => __( 'Подробное описание магазина для его страницы. Не более 400 символов', 'bishkek-park' ), 'type' => 'textarea', 'max_length' => 400 ),
			'_bp_hours'       => array( 'label' => __( 'Время работы', 'bishkek-park' ), 'description' => __( 'Например: Пн - Вс: 10:00 - 22:00. Если не заполнено, показывается "Пн - Вс: 10:00 - 22:00"', 'bishkek-park' ) ),
			'_bp_website'     => array( 'label' => __( 'Сайт', 'bishkek-park' ), 'description' => __( 'Сайт или email магазина. Если не заполнено, показывается "info@bishkekpark.kg"', 'bishkek-park' ) ),
		),
		'bp_cafe'  => array(
			'_bp_category'    => array( 'label' => __( 'Категория', 'bishkek-park' ), 'description' => __( 'Например: Ресторан', 'bishkek-park' ) ),
			'_bp_floor'       => array(
				'label'             => __( 'Этаж', 'bishkek-park' ),
				'description'       => __( 'Слово «Этаж» добавляется на сайте автоматически', 'bishkek-park' ),
				'type'              => 'select',
				'options'           => bishkek_park_shop_floor_field_options(),
				'sanitize_callback' => 'bishkek_park_sanitize_shop_floor',
			),
			'_bp_description' => array( 'label' => __( 'Описание', 'bishkek-park' ), 'description' => __( 'Подробное описание кафе/ресторана для его страницы. Не более 400 символов', 'bishkek-park' ), 'type' => 'textarea', 'max_length' => 400 ),
			'_bp_hours'       => array( 'label' => __( 'Время работы', 'bishkek-park' ), 'description' => __( 'Например: Пн - Вс: 10:00 - 22:00. Если не заполнено, показывается "Пн - Вс: 10:00 - 22:00"', 'bishkek-park' ) ),
			'_bp_website'     => array( 'label' => __( 'Сайт', 'bishkek-park' ), 'description' => __( 'Сайт или email кафе/ресторана. Если не заполнено, показывается "info@bishkekpark.kg"', 'bishkek-park' ) ),
		),
		'bp_event' => array(
			'_bp_category'    => array( 'label' => __( 'Категория', 'bishkek-park' ), 'description' => __( 'Например: Концерт', 'bishkek-park' ) ),
			'_bp_date'        => array( 'label' => __( 'Дата', 'bishkek-park' ), 'description' => __( 'Выберите дату мероприятия в календаре', 'bishkek-park' ), 'type' => 'date' ),
			'_bp_description' => array( 'label' => __( 'Описание', 'bishkek-park' ), 'description' => __( 'Короткое описание мероприятия', 'bishkek-park' ) ),
		),
		'bp_banner' => array(
			'_bp_badge'        => array( 'label' => __( 'Метка (необязательно)', 'bishkek-park' ), 'description' => __( 'Например: СЕЗОННАЯ РАСПРОДАЖА', 'bishkek-park' ) ),
			'_bp_text'         => array( 'label' => __( 'Текст', 'bishkek-park' ), 'description' => __( 'Короткое описание под заголовком', 'bishkek-park' ), 'type' => 'textarea' ),
			'_bp_button_label' => array( 'label' => __( 'Текст кнопки (необязательно)', 'bishkek-park' ), 'description' => __( 'Например: Подробнее. Кнопка не показывается, если текст или ссылка не заполнены', 'bishkek-park' ) ),
			'_bp_button_url'   => array( 'label' => __( 'Ссылка кнопки', 'bishkek-park' ), 'description' => __( 'Куда ведёт кнопка', 'bishkek-park' ) ),
		),
		'bp_leader' => array(
			'_bp_position'    => array( 'label' => __( 'Должность', 'bishkek-park' ), 'description' => __( 'Например: Генеральный директор. Имя сотрудника вводится как заголовок записи', 'bishkek-park' ) ),
			'_bp_description' => array( 'label' => __( 'Описание', 'bishkek-park' ), 'description' => __( 'Короткое описание обязанностей сотрудника', 'bishkek-park' ), 'type' => 'textarea', 'max_length' => 300 ),
		),
		'bp_milestone' => array(
			'_bp_year'        => array( 'label' => __( 'Год', 'bishkek-park' ), 'description' => __( 'Например: 2013. Название этапа вводится как заголовок записи', 'bishkek-park' ) ),
			'_bp_description' => array( 'label' => __( 'Описание', 'bishkek-park' ), 'description' => __( 'Короткое описание этапа', 'bishkek-park' ), 'type' => 'textarea', 'max_length' => 300 ),
		),
		'page'     => array(
			'_bp_seo_description' => array(
				'label'       => __( 'SEO описание (meta description)', 'bishkek-park' ),
				'description' => __( 'Показывается в результатах поиска и соцсетях. Рекомендуемая длина — 120–160 символов.', 'bishkek-park' ),
				'type'        => 'textarea',
			),
		),
	);

	if ( 'page' === $post_type && $post instanceof WP_Post && 'template-parts/contacts/page-contacts.php' === get_page_template_slug( $post ) ) {
		$fields['page'] = array_merge( $fields['page'], bishkek_park_contacts_meta_fields() );
	}

	if ( 'page' === $post_type && $post instanceof WP_Post && 'template-parts/funcity/page-funcity.php' === get_page_template_slug( $post ) ) {
		$fields['page'] = array_merge( $fields['page'], bishkek_park_funcity_meta_fields() );
	}

	return isset( $fields[ $post_type ] ) ? $fields[ $post_type ] : array();
}

/**
 * Register the meta box on each of our post types' edit screens.
 */
function bishkek_park_add_meta_boxes() {
	foreach ( array( 'bp_shop', 'bp_cafe', 'bp_event', 'bp_banner', 'bp_leader', 'bp_milestone', 'page' ) as $post_type ) {
		add_meta_box(
			'bishkek_park_fields',
			__( 'Данные для сайта', 'bishkek-park' ),
			'bishkek_park_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'bishkek_park_add_meta_boxes' );

/**
 * 'image' fields open the media library (wp.media) — make sure it's loaded
 * on edit screens even where the editor itself wouldn't load it.
 */
function bishkek_park_enqueue_meta_box_media( $hook ) {
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'bishkek_park_enqueue_meta_box_media' );

/**
 * Render the meta box fields for the current post type.
 */
function bishkek_park_render_meta_box( $post ) {
	$fields = bishkek_park_meta_fields( $post->post_type, $post );

	wp_nonce_field( 'bishkek_park_save_meta', 'bishkek_park_meta_nonce' );

	foreach ( $fields as $key => $field ) {
		$value       = get_post_meta( $post->ID, $key, true );
		$type        = isset( $field['type'] ) ? $field['type'] : 'text';
		$max_length  = isset( $field['max_length'] ) ? (int) $field['max_length'] : 0;
		$placeholder = isset( $field['default'] ) ? $field['default'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $field['label'] ); ?></strong></label><br>
			<?php if ( 'textarea' === $type ) : ?>
				<textarea
					id="<?php echo esc_attr( $key ); ?>"
					name="<?php echo esc_attr( $key ); ?>"
					class="widefat"
					rows="3"
					placeholder="<?php echo esc_attr( $placeholder ); ?>"
					<?php echo $max_length ? 'maxlength="' . esc_attr( $max_length ) . '"' : ''; ?>
				><?php echo esc_textarea( $value ); ?></textarea>
			<?php elseif ( 'image' === $type ) : ?>
				<?php $image_url = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : ''; ?>
				<span class="bishkek-park-image-field" data-bishkek-park-image-field>
					<input type="hidden" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
					<img
						src="<?php echo esc_url( $image_url ); ?>"
						alt=""
						style="display:<?php echo $image_url ? 'block' : 'none'; ?>;max-width:240px;height:auto;margin:6px 0;"
						data-bishkek-park-image-preview
					>
					<button type="button" class="button" data-bishkek-park-image-select><?php esc_html_e( 'Выбрать фото', 'bishkek-park' ); ?></button>
					<button type="button" class="button-link button-link-delete" data-bishkek-park-image-remove style="margin-left:8px;<?php echo $value ? '' : 'display:none;'; ?>"><?php esc_html_e( 'Убрать', 'bishkek-park' ); ?></button>
				</span><br>
			<?php elseif ( 'date' === $type ) : ?>
				<input
					type="date"
					id="<?php echo esc_attr( $key ); ?>"
					name="<?php echo esc_attr( $key ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
				>
			<?php elseif ( 'select' === $type ) : ?>
				<select
					id="<?php echo esc_attr( $key ); ?>"
					name="<?php echo esc_attr( $key ); ?>"
					class="widefat"
				>
					<?php foreach ( $field['options'] as $option_value => $option_label ) : ?>
						<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $value, $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
					<?php endforeach; ?>
				</select>
			<?php else : ?>
				<input
					type="text"
					id="<?php echo esc_attr( $key ); ?>"
					name="<?php echo esc_attr( $key ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					class="widefat"
					placeholder="<?php echo esc_attr( $placeholder ); ?>"
					<?php echo $max_length ? 'maxlength="' . esc_attr( $max_length ) . '"' : ''; ?>
				>
			<?php endif; ?>
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<span class="description"><?php echo esc_html( $field['description'] ); ?></span>
			<?php endif; ?>
			<?php if ( $max_length ) : ?>
				<span class="description bishkek-park-char-count" data-bishkek-park-char-count="<?php echo esc_attr( $key ); ?>" data-max="<?php echo esc_attr( $max_length ); ?>">
					<?php echo esc_html( mb_strlen( $value ) ); ?>/<?php echo esc_html( $max_length ); ?>
				</span>
			<?php endif; ?>
		</p>
		<?php
	}
	?>
	<script>
	( function () {
		document.querySelectorAll( '[data-bishkek-park-char-count]' ).forEach( function ( counter ) {
			var field = document.getElementById( counter.getAttribute( 'data-bishkek-park-char-count' ) );
			var max = counter.getAttribute( 'data-max' );
			if ( ! field ) {
				return;
			}
			field.addEventListener( 'input', function () {
				counter.textContent = field.value.length + '/' + max;
			} );
		} );

		// 'image' fields: pick/remove an attachment from the media library.
		document.querySelectorAll( '[data-bishkek-park-image-field]' ).forEach( function ( wrap ) {
			var input = wrap.querySelector( 'input' );
			var preview = wrap.querySelector( '[data-bishkek-park-image-preview]' );
			var remove = wrap.querySelector( '[data-bishkek-park-image-remove]' );
			var frame = null;

			wrap.querySelector( '[data-bishkek-park-image-select]' ).addEventListener( 'click', function () {
				if ( ! window.wp || ! wp.media ) {
					return;
				}
				if ( ! frame ) {
					frame = wp.media( { library: { type: 'image' }, multiple: false } );
					frame.on( 'select', function () {
						var attachment = frame.state().get( 'selection' ).first().toJSON();
						var sizes = attachment.sizes || {};
						input.value = attachment.id;
						preview.src = ( sizes.medium || sizes.full || attachment ).url;
						preview.style.display = 'block';
						remove.style.display = '';
					} );
				}
				frame.open();
			} );

			remove.addEventListener( 'click', function () {
				input.value = '';
				preview.removeAttribute( 'src' );
				preview.style.display = 'none';
				remove.style.display = 'none';
			} );
		} );
	}() );
	</script>
	<?php
}

/**
 * Save the meta box fields, scoped to whichever post type is being saved.
 */
function bishkek_park_save_meta( $post_id ) {
	if ( ! isset( $_POST['bishkek_park_meta_nonce'] ) ||
		! wp_verify_nonce( $_POST['bishkek_park_meta_nonce'], 'bishkek_park_save_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	$fields    = bishkek_park_meta_fields( $post_type, get_post( $post_id ) );

	foreach ( $fields as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$raw  = wp_unslash( $_POST[ $key ] );
		$type = isset( $field['type'] ) ? $field['type'] : 'text';

		if ( 'image' === $type ) {
			$clean = absint( $raw ) ? (string) absint( $raw ) : '';
		} elseif ( 'textarea' === $type ) {
			$clean = sanitize_textarea_field( $raw );
		} else {
			$clean = sanitize_text_field( $raw );
		}

		if ( ! empty( $field['sanitize_callback'] ) && is_callable( $field['sanitize_callback'] ) ) {
			$clean = call_user_func( $field['sanitize_callback'], $clean );
		}

		if ( ! empty( $field['max_length'] ) ) {
			$clean = mb_substr( $clean, 0, (int) $field['max_length'] );
		}

		update_post_meta( $post_id, $key, $clean );
	}
}
add_action( 'save_post', 'bishkek_park_save_meta' );
