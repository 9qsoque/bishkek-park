<?php
/**
 * Registers the theme's hardcoded UI strings with Polylang so they can be
 * translated from Languages -> String translations, and provides fallback
 * implementations of the pll_e()/pll_esc_html_e()/pll_esc_attr_e() template
 * tags so template-parts/ don't fatal if Polylang is deactivated.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'pll_e' ) ) {
	function pll_e( $string ) {
		echo $string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'pll_esc_html_e' ) ) {
	function pll_esc_html_e( $string ) {
		echo esc_html( $string );
	}
}

if ( ! function_exists( 'pll_esc_attr_e' ) ) {
	function pll_esc_attr_e( $string ) {
		echo esc_attr( $string );
	}
}

if ( ! function_exists( 'pll_esc_html__' ) ) {
	function pll_esc_html__( $string ) {
		return esc_html( $string );
	}
}

/**
 * All translatable theme strings: registration name => string.
 * Grouped only for readability; the group key below becomes the Polylang
 * "String translations" context for that block.
 */
function bishkek_park_translatable_strings() {
	return array(
		'Bishkek Park: Header' => array(
			'header_nav_shopping' => 'Шопинг',
			'header_nav_cinema'   => 'Синематика',
			'header_nav_cafes'    => 'Кафе и рестораны',
			'header_nav_events'   => 'Мероприятия',
			'header_nav_contacts' => 'Контакты',
			'header_menu_open'    => 'Меню',
			'header_menu_close'   => 'Закрыть',
			'header_today_hours'  => 'Сегодня до 22:00',
			'header_mall_map'     => 'Карта Молла',
			'header_parking'      => 'Парковка',
			'header_lang_ru_name' => 'Русский',
		),
		'Bishkek Park: Footer' => array(
			'footer_address_street' => '148В, ул. Киевская',
			'footer_address_city'   => '720001 Бишкек / Кыргызстан',
			'footer_our_address'    => 'Наш адрес',
			'footer_contact_us'     => 'Связаться с нами',
			'footer_mall_plan'      => 'План молла',
			'footer_hours_label'    => 'Время работы',
			'footer_day_mon'        => 'Пн',
			'footer_day_tue'        => 'Вт',
			'footer_day_wed'        => 'Ср',
			'footer_day_thu'        => 'Чт',
			'footer_day_fri'        => 'Пт',
			'footer_day_sat'        => 'Сб',
			'footer_day_sun'        => 'Вс',
			'footer_feedback'       => 'Обратная связь',
			'footer_for_tenants'    => 'Арендаторам',
			'footer_advertising'    => 'Рекламные услуги',
			'footer_vacancies'      => 'Вакансии',
			'footer_privacy_policy' => 'Политика конфиденциальности',
		),
		'Bishkek Park: Homepage' => array(
			'home_section_shops'           => 'Магазины',
			'home_shops_desktop_text'      => 'Шопинг и покупки с кешбэком 10%',
			'home_cashback_text'           => 'Кешбэк 10%',
			'home_section_cafe'            => 'Кафе',
			'home_cafe_desktop_text'       => 'Еда и напитки с кешбэком 10%',
			'home_cinema_desktop_text'     => 'Кино и развлечения с кешбэком 10%',
			'home_hero_badge'              => 'СЕЗОННАЯ РАСПРОДАЖА',
			'home_hero_title'              => 'Скидки до 30% в&nbsp;любимых магазинах',
			'home_hero_text'               => 'Покупайте онлайн и возвращайте деньги за каждую покупку',
			'home_learn_more'              => 'Подробнее',
			'home_shops_prev'              => 'Предыдущие магазины',
			'home_shops_next'              => 'Следующие магазины',
			'home_cinema_prev'             => 'Предыдущие сеансы',
			'home_cinema_next'             => 'Следующие сеансы',
			'home_events_prev'             => 'Предыдущие мероприятия',
			'home_events_next'             => 'Следующие мероприятия',
			'home_leasing_title'           => 'Аренда помещений в торговом центре',
			'home_leasing_text'            => 'Откройте свой магазин в одном из лучших ТЦ города. Мы предлагаем помещения под любой формат бизнеса.',
			'home_leasing_feature1_title'  => 'Площадь от 20 м²',
			'home_leasing_feature1_text'   => 'Помещения для малого бизнеса, островки, корнеры и якорные магазины',
			'home_leasing_feature2_title'  => 'Высокая проходимость',
			'home_leasing_feature2_text'   => 'Более 50 000 посетителей ежедневно, развитая инфраструктура',
			'home_leasing_feature3_title'  => 'Гибкие условия',
			'home_leasing_feature3_text'   => 'Краткосрочная и долгосрочная аренда, помощь с оформлением',
			'home_leasing_cta'             => 'Оставить заявку',
			'home_leasing_media_kit'       => 'Скачать медиакит',
			'home_parking_title'           => 'Паркинг в ТЦ',
			'home_parking_text'            => 'Зарегистрируйтесь и совершите покупку в любом магазине-партнёре.',
			'home_parking_cta'             => 'Смотреть',
			'home_parking_total'           => 'мест всего',
			'home_parking_free'            => 'свободно',
			'home_shops_view_all'          => 'Все магазины',
			'home_events_view_all'        => 'Все мероприятия',
		),
		'Bishkek Park: Shop catalog' => array(
			'shop_catalog_title'        => 'Каталог магазинов',
			'shop_catalog_filter_label' => 'Фильтр по этажам',
			'shop_catalog_filter_all'   => 'Все этажи',
			'shop_catalog_empty'        => 'Магазины пока не добавлены.',
			'shop_floor_label'          => 'Этаж',
			'shop_catalog_back'         => 'Назад на главную',
		),
		'Bishkek Park: Single shop' => array(
			'shop_single_website_label' => 'Сайт',
			'shop_single_hours_label'   => 'Время работы',
			'shop_single_default_hours' => 'Пн - Вс: 10:00 - 22:00',
			'shop_single_default_site'  => 'info@bishkekpark.kg',
			'shop_single_related_title' => 'Магазины на этаже',
			'shop_single_back'          => 'Назад в каталог',
		),
		'Bishkek Park: Cafe catalog' => array(
			'cafe_catalog_title'        => 'Кафе и рестораны',
			'cafe_catalog_filter_label' => 'Фильтр по этажам',
			'cafe_catalog_filter_all'   => 'Все этажи',
			'cafe_catalog_empty'        => 'Кафе и рестораны пока не добавлены.',
			'cafe_catalog_back'         => 'Назад на главную',
			'cafe_default_category'     => 'Ресторан',
		),
		'Bishkek Park: Single cafe' => array(
			'cafe_single_website_label' => 'Сайт',
			'cafe_single_hours_label'   => 'Время работы',
			'cafe_single_default_hours' => 'Пн - Вс: 10:00 - 22:00',
			'cafe_single_default_site'  => 'info@bishkekpark.kg',
			'cafe_single_related_title' => 'Кафе и рестораны на этаже',
			'cafe_single_back'          => 'Назад в каталог',
		),
		'Bishkek Park: Event catalog' => array(
			'events_catalog_title'        => 'Мероприятия',
			'events_catalog_filter_label' => 'Фильтр по категориям',
			'events_catalog_filter_all'   => 'Все',
			'events_catalog_empty'        => 'Мероприятия пока не добавлены.',
			'events_catalog_back'         => 'Назад на главную',
		),
		'Bishkek Park: Single event' => array(
			'events_single_back'          => 'Назад в каталог',
			'events_single_related_title' => 'Мероприятия',
		),
	);
}

/**
 * Registers every string above with Polylang. Safe to call unconditionally:
 * pll_register_string() itself no-ops outside wp-admin.
 */
function bishkek_park_register_translatable_strings() {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}

	foreach ( bishkek_park_translatable_strings() as $context => $strings ) {
		foreach ( $strings as $name => $string ) {
			pll_register_string( $name, $string, $context );
		}
	}
}
add_action( 'init', 'bishkek_park_register_translatable_strings' );
