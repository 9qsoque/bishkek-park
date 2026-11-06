<?php
/**
 * "Инструменты → Импорт магазинов и кафе": creates/updates bp_shop or bp_cafe
 * posts in bulk from a CSV export of the content spreadsheet (Google Sheets →
 * Файл → Скачать → CSV), so a full tenant list can be loaded onto the live
 * site without SSH/WP-CLI.
 *
 * Expected columns (matched by header text, case/`*`-insensitive, order
 * doesn't matter): Название, Категория, Этаж, Описание, Время работы,
 * Сайт / email, Файл фото, Название (KG), Категория (KG), Описание (KG).
 * Other columns (e.g. "Символов") are ignored.
 *
 * Each row becomes one default-language (RU) post plus its linked KY
 * translation (Polylang). A post whose title already exists in the RU list is
 * updated in place rather than duplicated, so re-importing a corrected sheet
 * is safe; post IDs (and so mall-map assignments) stay the same. Row order
 * becomes `menu_order`.
 *
 * Photos are not uploaded here: they're added to Медиафайлы first (normal
 * drag-and-drop uploader, which also generates thumbnail sizes), and the
 * "Файл фото" column is matched against the media library by file name —
 * this keeps the import request small and fast on shared hosting.
 *
 * Two steps: "Проверить" parses the file and shows what would happen without
 * writing anything (the parsed rows are kept in a per-user transient), then
 * "Импортировать" applies that same file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post types this importer can fill => admin label.
 */
function bishkek_park_import_post_types() {
	return array(
		'bp_shop' => __( 'Магазины', 'bishkek-park' ),
		'bp_cafe' => __( 'Кафе и рестораны', 'bishkek-park' ),
	);
}

/**
 * Internal column key => normalized spreadsheet header text.
 */
function bishkek_park_import_columns() {
	return array(
		'title'          => 'название',
		'category'       => 'категория',
		'floor'          => 'этаж',
		'description'    => 'описание',
		'hours'          => 'время работы',
		'website'        => 'сайт / email',
		'photo'          => 'файл фото',
		'title_ky'       => 'название (kg)',
		'category_ky'    => 'категория (kg)',
		'description_ky' => 'описание (kg)',
	);
}

function bishkek_park_import_transient_key() {
	return 'bishkek_park_import_' . get_current_user_id();
}

/**
 * Registers the page under Инструменты.
 */
function bishkek_park_register_import_page() {
	add_management_page(
		__( 'Импорт магазинов и кафе', 'bishkek-park' ),
		__( 'Импорт магазинов и кафе', 'bishkek-park' ),
		BISHKEK_PARK_IMPORT_CAPABILITY,
		'bishkek-park-import',
		'bishkek_park_render_import_page'
	);
}
add_action( 'admin_menu', 'bishkek_park_register_import_page' );

/**
 * Lowercases, drops the "*" required-markers and collapses whitespace, so
 * "Название *" and "название" match.
 */
function bishkek_park_import_normalize_header( $header ) {
	$header = str_replace( array( "\xEF\xBB\xBF", '*' ), '', $header );
	$header = preg_replace( '/\s+/u', ' ', $header );

	return mb_strtolower( trim( $header ) );
}

/**
 * Parses an uploaded CSV into a list of [ column key => value ] rows (row
 * numbers as in the spreadsheet, header = row 1). Returns WP_Error when the
 * file is unreadable or a required column is missing.
 */
function bishkek_park_import_parse_csv( $path ) {
	$handle = fopen( $path, 'r' );
	if ( ! $handle ) {
		return new WP_Error( 'bp_import_read', __( 'Не удалось прочитать файл.', 'bishkek-park' ) );
	}

	$header = fgetcsv( $handle, 0, ',', '"', '' );
	if ( ! $header ) {
		fclose( $handle );
		return new WP_Error( 'bp_import_empty', __( 'Файл пустой.', 'bishkek-park' ) );
	}

	$wanted = array_flip( bishkek_park_import_columns() );
	$map    = array();
	foreach ( $header as $index => $label ) {
		$label = bishkek_park_import_normalize_header( $label );
		if ( isset( $wanted[ $label ] ) ) {
			$map[ $wanted[ $label ] ] = $index;
		}
	}

	foreach ( array( 'title', 'category', 'floor' ) as $required ) {
		if ( ! isset( $map[ $required ] ) ) {
			fclose( $handle );
			$columns = bishkek_park_import_columns();
			// translators: %s is a spreadsheet column name.
			return new WP_Error( 'bp_import_columns', sprintf( __( 'В файле нет колонки «%s». Это точно CSV из таблицы?', 'bishkek-park' ), $columns[ $required ] ) );
		}
	}

	$rows   = array();
	$number = 1;
	while ( false !== ( $cells = fgetcsv( $handle, 0, ',', '"', '' ) ) ) {
		$number++;
		$row = array( '_row' => $number );
		foreach ( bishkek_park_import_columns() as $key => $label ) {
			$row[ $key ] = isset( $map[ $key ], $cells[ $map[ $key ] ] ) ? trim( $cells[ $map[ $key ] ] ) : '';
		}
		$rows[] = $row;
	}
	fclose( $handle );

	return $rows;
}

/**
 * Turns the sheet's floor value into a `_bp_floor` value: strips a leading
 * "Этаж", maps the basement's display name "М1"/"M1" to its stored value
 * "-1". Returns null when the result isn't a floor the map knows about.
 */
function bishkek_park_import_normalize_floor( $value ) {
	$value = trim( bishkek_park_sanitize_shop_floor( $value ) );

	if ( in_array( mb_strtolower( $value ), array( 'м1', 'm1', 'м-1', 'm-1' ), true ) ) {
		$value = '-1';
	}

	return in_array( $value, bishkek_park_get_map_floors(), true ) ? $value : null;
}

/**
 * File name => comparable key: no folder, no extension, no "-scaled" suffix
 * WordPress adds to big uploads, lowercase, letters/digits only — so
 * "Pierre Cardin.jpg" in the sheet matches an upload stored as
 * "pierre-cardin-scaled.jpg".
 */
function bishkek_park_import_photo_key( $name ) {
	$name = wp_basename( $name );
	$name = preg_replace( '/\.[a-z0-9]{2,5}$/i', '', $name );
	$name = preg_replace( '/-scaled$/i', '', $name );

	return preg_replace( '/[^\p{L}\p{N}]+/u', '', mb_strtolower( $name ) );
}

/**
 * Photo key => attachment ID for every image in the media library, by both
 * the stored file name and the attachment title (which WordPress takes from
 * the original, un-sanitized file name). Newest upload wins on a clash.
 */
function bishkek_park_import_photo_index() {
	$ids = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'post_status'    => 'inherit',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'date',
			'order'          => 'ASC',
			'lang'           => '', // Polylang: don't filter media by language.
		)
	);

	$index = array();
	foreach ( $ids as $id ) {
		$keys = array(
			bishkek_park_import_photo_key( (string) get_post_meta( $id, '_wp_attached_file', true ) ),
			bishkek_park_import_photo_key( get_the_title( $id ) ),
		);
		foreach ( array_filter( $keys ) as $key ) {
			$index[ $key ] = $id;
		}
	}

	return $index;
}

/**
 * The site's second (non-default) Polylang language slug, or '' without
 * Polylang / with only one language.
 */
function bishkek_park_import_secondary_language() {
	if ( ! function_exists( 'pll_languages_list' ) || ! function_exists( 'pll_default_language' ) ) {
		return '';
	}

	$others = array_diff( pll_languages_list( array( 'fields' => 'slug' ) ), array( pll_default_language() ) );

	return $others ? reset( $others ) : '';
}

/**
 * Existing default-language post with exactly this title, or 0.
 */
function bishkek_park_import_find_existing( $post_type, $title ) {
	$args = array(
		'post_type'      => $post_type,
		'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
		'title'          => $title,
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'orderby'        => 'ID',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	);

	if ( function_exists( 'pll_default_language' ) ) {
		$args['lang'] = pll_default_language();
	}

	$ids = get_posts( $args );

	return $ids ? (int) $ids[0] : 0;
}

/**
 * Validates every row and works out what would happen to it, without
 * writing anything. Used by both "Проверить" (shown as a table) and
 * "Импортировать" (executed), so the two can't disagree.
 */
function bishkek_park_import_plan( $post_type, $rows ) {
	$photos   = bishkek_park_import_photo_index();
	$max_desc = 400;
	$seen     = array();
	$plan     = array();
	$order    = 0;

	foreach ( $rows as $row ) {
		$title = sanitize_text_field( $row['title'] );
		$item  = array(
			'row'      => $row['_row'],
			'title'    => $title,
			'action'   => 'skip',
			'warnings' => array(),
		);

		if ( '' === $title ) {
			$other = array_diff_key( $row, array( '_row' => true ) );
			if ( array_filter( $other, 'strlen' ) ) {
				$item['warnings'][] = __( 'Нет названия — строка пропущена.', 'bishkek-park' );
				$plan[]             = $item;
			}
			continue;
		}

		$seen_key = mb_strtolower( $title );
		if ( isset( $seen[ $seen_key ] ) ) {
			// translators: %d is a spreadsheet row number.
			$item['warnings'][] = sprintf( __( 'Такое же название уже было в строке %d — строка пропущена.', 'bishkek-park' ), $seen[ $seen_key ] );
			$plan[]             = $item;
			continue;
		}
		$seen[ $seen_key ] = $row['_row'];

		$floor = bishkek_park_import_normalize_floor( $row['floor'] );
		if ( '' === $row['floor'] ) {
			$floor              = '';
			$item['warnings'][] = __( 'Этаж не указан.', 'bishkek-park' );
		} elseif ( null === $floor ) {
			$floor = '';
			// translators: %s is the floor value from the spreadsheet.
			$item['warnings'][] = sprintf( __( 'Непонятный этаж «%s» — оставлен пустым.', 'bishkek-park' ), $row['floor'] );
		}

		$category = sanitize_text_field( $row['category'] );
		if ( '' === $category ) {
			$item['warnings'][] = __( 'Категория не указана.', 'bishkek-park' );
		}

		$description = sanitize_textarea_field( $row['description'] );
		if ( mb_strlen( $description ) > $max_desc ) {
			$description = mb_substr( $description, 0, $max_desc );
			// translators: %d is the maximum description length.
			$item['warnings'][] = sprintf( __( 'Описание длиннее %d символов — обрезано.', 'bishkek-park' ), $max_desc );
		}

		$description_ky = sanitize_textarea_field( $row['description_ky'] );
		if ( mb_strlen( $description_ky ) > $max_desc ) {
			$description_ky = mb_substr( $description_ky, 0, $max_desc );
			// translators: %d is the maximum description length.
			$item['warnings'][] = sprintf( __( 'Описание (KG) длиннее %d символов — обрезано.', 'bishkek-park' ), $max_desc );
		}

		$photo_id = 0;
		$photo    = sanitize_text_field( $row['photo'] );
		if ( '' === $photo ) {
			$item['warnings'][] = __( 'Фото не указано.', 'bishkek-park' );
		} else {
			$key = bishkek_park_import_photo_key( $photo );
			if ( isset( $photos[ $key ] ) ) {
				$photo_id = $photos[ $key ];
			} else {
				// translators: %s is a photo file name.
				$item['warnings'][] = sprintf( __( 'Фото «%s» не найдено в Медиафайлах.', 'bishkek-park' ), $photo );
			}
		}

		$existing_id = bishkek_park_import_find_existing( $post_type, $title );
		$ky_title    = sanitize_text_field( $row['title_ky'] );
		$ky_category = sanitize_text_field( $row['category_ky'] );

		$item = array_merge(
			$item,
			array(
				'action'      => $existing_id ? 'update' : 'create',
				'existing_id' => $existing_id,
				'menu_order'  => ++$order,
				'photo_id'    => $photo_id,
				'meta'        => array(
					'_bp_category'    => $category,
					'_bp_floor'       => $floor,
					'_bp_description' => $description,
					'_bp_hours'       => sanitize_text_field( $row['hours'] ),
					'_bp_website'     => sanitize_text_field( $row['website'] ),
				),
				// Untranslated KG cells fall back to the RU text, so the KY
				// page never shows a blank where the RU page has content.
				'ky_title'    => '' !== $ky_title ? $ky_title : $title,
				'ky_meta'     => array(
					'_bp_category'    => '' !== $ky_category ? $ky_category : $category,
					'_bp_description' => '' !== $description_ky ? $description_ky : $description,
				),
			)
		);

		$plan[] = $item;
	}

	return $plan;
}

/**
 * Creates or updates one post (title, order, meta, featured image).
 * Returns the post ID or WP_Error.
 */
function bishkek_park_import_save_post( $post_type, $post_id, $title, $menu_order, $meta, $photo_id ) {
	$postarr = array(
		'post_type'   => $post_type,
		'post_title'  => $title,
		'post_status' => 'publish',
		'menu_order'  => $menu_order,
	);

	if ( $post_id ) {
		$postarr['ID'] = $post_id;
		$result        = wp_update_post( wp_slash( $postarr ), true );
	} else {
		$result = wp_insert_post( wp_slash( $postarr ), true );
	}

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	foreach ( $meta as $key => $value ) {
		update_post_meta( $result, $key, wp_slash( $value ) );
	}

	if ( $photo_id ) {
		set_post_thumbnail( $result, $photo_id );
	}

	return (int) $result;
}

/**
 * Applies a plan: RU post, then its KY translation, linked via Polylang.
 * Returns the plan with 'ru_id'/'ky_id'/'error' filled in per item.
 */
function bishkek_park_import_run( $post_type, $plan ) {
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 );
	}

	$default_lang = function_exists( 'pll_default_language' ) ? pll_default_language() : '';
	$ky_lang      = bishkek_park_import_secondary_language();

	// If Polylang's "synchronize custom fields" is on, saving one translation
	// copies its meta onto the other — which would overwrite the RU
	// category/description with the KG ones (or vice versa). Those two are
	// per-language, so keep them out of syncing while importing.
	$keep_per_language = function ( $keys ) {
		return array_values( array_diff( (array) $keys, array( '_bp_category', '_bp_description' ) ) );
	};
	add_filter( 'pll_copy_post_metas', $keep_per_language, 99 );

	foreach ( $plan as &$item ) {
		if ( 'skip' === $item['action'] ) {
			continue;
		}

		$ru_id = bishkek_park_import_save_post( $post_type, $item['existing_id'], $item['title'], $item['menu_order'], $item['meta'], $item['photo_id'] );
		if ( is_wp_error( $ru_id ) ) {
			$item['error'] = $ru_id->get_error_message();
			continue;
		}
		$item['ru_id'] = $ru_id;

		if ( ! $default_lang || ! $ky_lang ) {
			continue;
		}

		pll_set_post_language( $ru_id, $default_lang );

		$ky_id = bishkek_park_import_save_post(
			$post_type,
			(int) pll_get_post( $ru_id, $ky_lang ),
			$item['ky_title'],
			$item['menu_order'],
			array_merge( $item['meta'], $item['ky_meta'] ),
			$item['photo_id']
		);
		if ( is_wp_error( $ky_id ) ) {
			$item['error'] = $ky_id->get_error_message();
			continue;
		}
		$item['ky_id'] = $ky_id;

		pll_set_post_language( $ky_id, $ky_lang );
		pll_save_post_translations(
			array(
				$default_lang => $ru_id,
				$ky_lang      => $ky_id,
			)
		);
	}
	unset( $item );

	remove_filter( 'pll_copy_post_metas', $keep_per_language, 99 );

	return $plan;
}

/**
 * Handles the two form submits; returns [ 'notice' => [type, text],
 * 'plan' => ..., 'post_type' => ..., 'done' => bool ] for the renderer.
 */
function bishkek_park_import_handle_request() {
	$state = array(
		'notice'    => null,
		'plan'      => null,
		'post_type' => '',
		'done'      => false,
	);

	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || empty( $_POST['bp_import_action'] ) ) {
		return $state;
	}

	check_admin_referer( 'bishkek_park_import' );

	$types  = bishkek_park_import_post_types();
	$action = sanitize_key( wp_unslash( $_POST['bp_import_action'] ) );

	if ( 'check' === $action ) {
		$post_type = isset( $_POST['bp_import_post_type'] ) ? sanitize_key( wp_unslash( $_POST['bp_import_post_type'] ) ) : '';
		if ( ! isset( $types[ $post_type ] ) ) {
			$state['notice'] = array( 'error', __( 'Выберите, что импортировать.', 'bishkek-park' ) );
			return $state;
		}

		$file = isset( $_FILES['bp_import_csv'] ) ? $_FILES['bp_import_csv'] : null;
		if ( ! $file || UPLOAD_ERR_OK !== $file['error'] || ! is_uploaded_file( $file['tmp_name'] ) ) {
			$state['notice'] = array( 'error', __( 'Выберите CSV-файл.', 'bishkek-park' ) );
			return $state;
		}
		if ( $file['size'] > 2 * MB_IN_BYTES ) {
			$state['notice'] = array( 'error', __( 'Файл больше 2 МБ — это точно CSV из таблицы?', 'bishkek-park' ) );
			return $state;
		}

		$rows = bishkek_park_import_parse_csv( $file['tmp_name'] );
		if ( is_wp_error( $rows ) ) {
			$state['notice'] = array( 'error', $rows->get_error_message() );
			return $state;
		}

		set_transient(
			bishkek_park_import_transient_key(),
			array(
				'post_type' => $post_type,
				'rows'      => $rows,
			),
			HOUR_IN_SECONDS
		);

		$state['post_type'] = $post_type;
		$state['plan']      = bishkek_park_import_plan( $post_type, $rows );
		return $state;
	}

	if ( 'run' === $action ) {
		$stored = get_transient( bishkek_park_import_transient_key() );
		if ( ! $stored || ! isset( $types[ $stored['post_type'] ] ) ) {
			$state['notice'] = array( 'error', __( 'Проверка устарела — загрузите файл ещё раз.', 'bishkek-park' ) );
			return $state;
		}

		delete_transient( bishkek_park_import_transient_key() );

		$state['post_type'] = $stored['post_type'];
		$state['plan']      = bishkek_park_import_run( $stored['post_type'], bishkek_park_import_plan( $stored['post_type'], $stored['rows'] ) );
		$state['done']      = true;
		$state['notice']    = array( 'success', __( 'Импорт выполнен.', 'bishkek-park' ) );
	}

	return $state;
}

function bishkek_park_render_import_page() {
	if ( ! current_user_can( BISHKEK_PARK_IMPORT_CAPABILITY ) ) {
		wp_die( esc_html__( 'Недостаточно прав.', 'bishkek-park' ) );
	}

	$state = bishkek_park_import_handle_request();
	$types = bishkek_park_import_post_types();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Импорт магазинов и кафе', 'bishkek-park' ); ?></h1>

		<?php if ( $state['notice'] ) : ?>
			<div class="notice notice-<?php echo esc_attr( $state['notice'][0] ); ?>"><p><?php echo esc_html( $state['notice'][1] ); ?></p></div>
		<?php endif; ?>

		<?php if ( ! $state['plan'] || $state['done'] ) : ?>
			<ol>
				<li><?php esc_html_e( 'Загрузите все фото в Медиафайлы → Добавить новый (можно перетащить сразу все файлы).', 'bishkek-park' ); ?></li>
				<li><?php esc_html_e( 'В Google Таблице откройте нужный лист → Файл → Скачать → CSV.', 'bishkek-park' ); ?></li>
				<li><?php esc_html_e( 'Выберите файл ниже и нажмите «Проверить» — ничего не будет записано, пока вы не подтвердите импорт.', 'bishkek-park' ); ?></li>
			</ol>
			<p class="description"><?php esc_html_e( 'Если запись с таким названием уже есть, она обновляется (поля перезаписываются значениями из таблицы), а не создаётся заново. Порядок строк в таблице становится порядком на сайте.', 'bishkek-park' ); ?></p>

			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'bishkek_park_import' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="bp_import_post_type"><?php esc_html_e( 'Что импортировать', 'bishkek-park' ); ?></label></th>
						<td>
							<select id="bp_import_post_type" name="bp_import_post_type">
								<?php foreach ( $types as $type => $label ) : ?>
									<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="bp_import_csv"><?php esc_html_e( 'CSV-файл', 'bishkek-park' ); ?></label></th>
						<td><input type="file" id="bp_import_csv" name="bp_import_csv" accept=".csv,text/csv"></td>
					</tr>
				</table>
				<p><button type="submit" class="button button-primary" name="bp_import_action" value="check"><?php esc_html_e( 'Проверить', 'bishkek-park' ); ?></button></p>
			</form>
		<?php endif; ?>

		<?php if ( $state['plan'] ) : ?>
			<?php
			$counts = array_count_values( wp_list_pluck( $state['plan'], 'action' ) );
			$labels = array(
				'create' => $state['done'] ? __( 'Создано', 'bishkek-park' ) : __( 'Будет создано', 'bishkek-park' ),
				'update' => $state['done'] ? __( 'Обновлено', 'bishkek-park' ) : __( 'Будет обновлено', 'bishkek-park' ),
				'skip'   => __( 'Пропущено', 'bishkek-park' ),
			);
			?>
			<h2><?php echo esc_html( $types[ $state['post_type'] ] ); ?></h2>
			<p>
				<?php foreach ( $labels as $action => $label ) : ?>
					<strong><?php echo esc_html( $label ); ?>:</strong> <?php echo (int) ( isset( $counts[ $action ] ) ? $counts[ $action ] : 0 ); ?>&nbsp;&nbsp;
				<?php endforeach; ?>
			</p>

			<?php if ( ! $state['done'] ) : ?>
				<form method="post">
					<?php wp_nonce_field( 'bishkek_park_import' ); ?>
					<p>
						<button type="submit" class="button button-primary" name="bp_import_action" value="run"><?php esc_html_e( 'Импортировать', 'bishkek-park' ); ?></button>
						<a class="button" href="<?php echo esc_url( menu_page_url( 'bishkek-park-import', false ) ); ?>"><?php esc_html_e( 'Отмена', 'bishkek-park' ); ?></a>
					</p>
				</form>
			<?php endif; ?>

			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Строка', 'bishkek-park' ); ?></th>
						<th><?php esc_html_e( 'Название', 'bishkek-park' ); ?></th>
						<th><?php esc_html_e( 'Действие', 'bishkek-park' ); ?></th>
						<th><?php esc_html_e( 'Этаж', 'bishkek-park' ); ?></th>
						<th><?php esc_html_e( 'Сайт', 'bishkek-park' ); ?></th>
						<th><?php esc_html_e( 'Фото', 'bishkek-park' ); ?></th>
						<th><?php esc_html_e( 'Замечания', 'bishkek-park' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $state['plan'] as $item ) : ?>
						<tr>
							<td><?php echo (int) $item['row']; ?></td>
							<td>
								<?php if ( ! empty( $item['ru_id'] ) ) : ?>
									<a href="<?php echo esc_url( get_edit_post_link( $item['ru_id'] ) ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
									<?php if ( ! empty( $item['ky_id'] ) ) : ?>
										/ <a href="<?php echo esc_url( get_edit_post_link( $item['ky_id'] ) ); ?>">KG</a>
									<?php endif; ?>
								<?php else : ?>
									<?php echo esc_html( $item['title'] ); ?>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( $labels[ $item['action'] ] ); ?></td>
							<td><?php echo isset( $item['meta'] ) ? esc_html( bishkek_park_get_floor_number_label( $item['meta']['_bp_floor'] ) ) : ''; ?></td>
							<td><?php echo isset( $item['meta'] ) ? esc_html( $item['meta']['_bp_website'] ) : ''; ?></td>
							<td>
								<?php if ( ! empty( $item['photo_id'] ) ) : ?>
									<?php echo wp_get_attachment_image( $item['photo_id'], array( 48, 48 ) ); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( ! empty( $item['error'] ) ) : ?>
									<strong style="color:#b32d2e;"><?php echo esc_html( $item['error'] ); ?></strong><br>
								<?php endif; ?>
								<?php echo esc_html( implode( ' ', $item['warnings'] ) ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}
