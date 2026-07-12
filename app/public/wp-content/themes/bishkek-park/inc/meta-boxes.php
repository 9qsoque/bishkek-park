<?php
/**
 * Admin meta boxes (custom fields) for the Shops, Movies, and Events
 * post types, plus the sanitize/save logic behind them.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field definitions per post type: meta key => [ label, description ].
 * Keeping this data-driven means one render + one save function covers
 * all three post types instead of duplicating boilerplate three times.
 */
function bishkek_park_meta_fields( $post_type ) {
	$fields = array(
		'bp_shop'  => array(
			'_bp_category' => array( 'label' => __( 'Категория', 'bishkek-park' ), 'description' => __( 'Например: Одежда и аксессуары', 'bishkek-park' ) ),
			'_bp_floor'    => array( 'label' => __( 'Этаж', 'bishkek-park' ), 'description' => __( 'Например: Этаж 2', 'bishkek-park' ) ),
		),
		'bp_movie' => array(
			'_bp_label'       => array( 'label' => __( 'Метка (необязательно)', 'bishkek-park' ), 'description' => __( 'Например: СЕГОДНЯ В КИНО', 'bishkek-park' ) ),
			'_bp_subtitle'    => array( 'label' => __( 'Подзаголовок', 'bishkek-park' ), 'description' => __( 'Например: МДМ Театр • Каждую пятницу', 'bishkek-park' ) ),
			'_bp_times'       => array( 'label' => __( 'Сеансы', 'bishkek-park' ), 'description' => __( 'Через запятую, например: 10:30, 14:00, 19:30', 'bishkek-park' ) ),
			'_bp_active_time' => array( 'label' => __( 'Выделенное время', 'bishkek-park' ), 'description' => __( 'Должно совпадать с одним из сеансов выше', 'bishkek-park' ) ),
		),
		'bp_event' => array(
			'_bp_category'    => array( 'label' => __( 'Категория', 'bishkek-park' ), 'description' => __( 'Например: Концерт', 'bishkek-park' ) ),
			'_bp_date'        => array( 'label' => __( 'Дата', 'bishkek-park' ), 'description' => __( 'Например: 15 июля 2025', 'bishkek-park' ) ),
			'_bp_description' => array( 'label' => __( 'Описание', 'bishkek-park' ), 'description' => __( 'Короткое описание мероприятия', 'bishkek-park' ) ),
		),
	);

	return isset( $fields[ $post_type ] ) ? $fields[ $post_type ] : array();
}

/**
 * Register the meta box on each of our post types' edit screens.
 */
function bishkek_park_add_meta_boxes() {
	foreach ( array( 'bp_shop', 'bp_movie', 'bp_event' ) as $post_type ) {
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
		$value = get_post_meta( $post->ID, $key, true );
		?>
		<p>
			<label for="<?php echo esc_attr( $key ); ?>"><strong><?php echo esc_html( $field['label'] ); ?></strong></label><br>
			<input
				type="text"
				id="<?php echo esc_attr( $key ); ?>"
				name="<?php echo esc_attr( $key ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				class="widefat"
			>
			<?php if ( ! empty( $field['description'] ) ) : ?>
				<span class="description"><?php echo esc_html( $field['description'] ); ?></span>
			<?php endif; ?>
		</p>
		<?php
	}
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
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
		}
	}
}
add_action( 'save_post', 'bishkek_park_save_meta' );
