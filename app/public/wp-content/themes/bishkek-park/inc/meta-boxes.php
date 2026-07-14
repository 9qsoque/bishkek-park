<?php
/**
 * Admin meta boxes (custom fields) for the Shops, Movies, and Events
 * post types, plus pages (currently just the SEO description field),
 * and the sanitize/save logic behind them.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field definitions per post type: meta key => [ label, description, type,
 * max_length, sanitize_callback ]. `type` is 'text' (default), 'textarea', or
 * 'date' (native HTML5 date-picker input; stored as an ISO `Y-m-d` string —
 * see bishkek_park_format_event_date() in functions.php for turning that
 * into the displayed "15 июля 2025" string).
 * `max_length` (optional) caps the field at N characters — enforced both as
 * the textarea's `maxlength` attribute and as a hard truncation on save, so
 * long-winded content can't break the layout it's rendered into.
 * `sanitize_callback` (optional) is called on the value after the base
 * sanitize, for one-off input normalization (see `_bp_floor` below). Keeping
 * this data-driven means one render + one save function covers every post
 * type instead of duplicating boilerplate for each one.
 */
function bishkek_park_meta_fields( $post_type ) {
	$fields = array(
		'bp_shop'  => array(
			'_bp_category'    => array( 'label' => __( 'Категория', 'bishkek-park' ), 'description' => __( 'Например: Одежда и аксессуары', 'bishkek-park' ) ),
			'_bp_floor'       => array(
				'label'             => __( 'Этаж', 'bishkek-park' ),
				'description'       => __( 'Только номер этажа, например: 2 — слово «Этаж» добавляется на сайте автоматически', 'bishkek-park' ),
				'sanitize_callback' => 'bishkek_park_sanitize_shop_floor',
			),
			'_bp_description' => array( 'label' => __( 'Описание', 'bishkek-park' ), 'description' => __( 'Подробное описание магазина для его страницы. Не более 400 символов', 'bishkek-park' ), 'type' => 'textarea', 'max_length' => 400 ),
			'_bp_hours'       => array( 'label' => __( 'Время работы', 'bishkek-park' ), 'description' => __( 'Например: Пн - Вс: 10:00 - 23:00. Если не заполнено, показывается "Пн - Вс: 10:00 - 22:00"', 'bishkek-park' ) ),
			'_bp_website'     => array( 'label' => __( 'Сайт', 'bishkek-park' ), 'description' => __( 'Сайт или email магазина. Если не заполнено, показывается "info@bishkekpark.kg"', 'bishkek-park' ) ),
		),
		'bp_cafe'  => array(
			'_bp_category'    => array( 'label' => __( 'Категория', 'bishkek-park' ), 'description' => __( 'Например: Ресторан', 'bishkek-park' ) ),
			'_bp_floor'       => array(
				'label'             => __( 'Этаж', 'bishkek-park' ),
				'description'       => __( 'Только номер этажа, например: 2 — слово «Этаж» добавляется на сайте автоматически', 'bishkek-park' ),
				'sanitize_callback' => 'bishkek_park_sanitize_shop_floor',
			),
			'_bp_description' => array( 'label' => __( 'Описание', 'bishkek-park' ), 'description' => __( 'Подробное описание кафе/ресторана для его страницы. Не более 400 символов', 'bishkek-park' ), 'type' => 'textarea', 'max_length' => 400 ),
			'_bp_hours'       => array( 'label' => __( 'Время работы', 'bishkek-park' ), 'description' => __( 'Например: Пн - Вс: 10:00 - 23:00. Если не заполнено, показывается "Пн - Вс: 10:00 - 22:00"', 'bishkek-park' ) ),
			'_bp_website'     => array( 'label' => __( 'Сайт', 'bishkek-park' ), 'description' => __( 'Сайт или email кафе/ресторана. Если не заполнено, показывается "info@bishkekpark.kg"', 'bishkek-park' ) ),
		),
		'bp_movie' => array(
			'_bp_label'       => array( 'label' => __( 'Метка (необязательно)', 'bishkek-park' ), 'description' => __( 'Например: СЕГОДНЯ В КИНО', 'bishkek-park' ) ),
			'_bp_subtitle'    => array( 'label' => __( 'Подзаголовок', 'bishkek-park' ), 'description' => __( 'Например: МДМ Театр • Каждую пятницу', 'bishkek-park' ) ),
			'_bp_times'       => array( 'label' => __( 'Сеансы', 'bishkek-park' ), 'description' => __( 'Через запятую, например: 10:30, 14:00, 19:30', 'bishkek-park' ) ),
			'_bp_active_time' => array( 'label' => __( 'Выделенное время', 'bishkek-park' ), 'description' => __( 'Должно совпадать с одним из сеансов выше', 'bishkek-park' ) ),
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
		'page'     => array(
			'_bp_seo_description' => array(
				'label'       => __( 'SEO описание (meta description)', 'bishkek-park' ),
				'description' => __( 'Показывается в результатах поиска и соцсетях. Рекомендуемая длина — 120–160 символов.', 'bishkek-park' ),
				'type'        => 'textarea',
			),
		),
	);

	return isset( $fields[ $post_type ] ) ? $fields[ $post_type ] : array();
}

/**
 * Register the meta box on each of our post types' edit screens.
 */
function bishkek_park_add_meta_boxes() {
	foreach ( array( 'bp_shop', 'bp_cafe', 'bp_movie', 'bp_event', 'bp_banner', 'page' ) as $post_type ) {
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
 * Render the meta box fields for the current post type.
 */
function bishkek_park_render_meta_box( $post ) {
	$fields = bishkek_park_meta_fields( $post->post_type );

	wp_nonce_field( 'bishkek_park_save_meta', 'bishkek_park_meta_nonce' );

	foreach ( $fields as $key => $field ) {
		$value      = get_post_meta( $post->ID, $key, true );
		$type       = isset( $field['type'] ) ? $field['type'] : 'text';
		$max_length = isset( $field['max_length'] ) ? (int) $field['max_length'] : 0;
		?>
		<p>
			<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $field['label'] ); ?></strong></label><br>
			<?php if ( 'textarea' === $type ) : ?>
				<textarea
					id="<?php echo esc_attr( $key ); ?>"
					name="<?php echo esc_attr( $key ); ?>"
					class="widefat"
					rows="3"
					<?php echo $max_length ? 'maxlength="' . esc_attr( $max_length ) . '"' : ''; ?>
				><?php echo esc_textarea( $value ); ?></textarea>
			<?php elseif ( 'date' === $type ) : ?>
				<input
					type="date"
					id="<?php echo esc_attr( $key ); ?>"
					name="<?php echo esc_attr( $key ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
				>
			<?php else : ?>
				<input
					type="text"
					id="<?php echo esc_attr( $key ); ?>"
					name="<?php echo esc_attr( $key ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
					class="widefat"
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
	$fields    = bishkek_park_meta_fields( $post_type );

	foreach ( $fields as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$raw   = wp_unslash( $_POST[ $key ] );
		$clean = ( isset( $field['type'] ) && 'textarea' === $field['type'] )
			? sanitize_textarea_field( $raw )
			: sanitize_text_field( $raw );

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
