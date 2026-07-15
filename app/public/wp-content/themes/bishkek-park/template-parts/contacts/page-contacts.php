<?php
/**
 * Template Name: Контакты
 *
 * Contacts page: circular map image, address/hours/phone, a tenants
 * ("Арендаторам") block with a rental-application download link, and an FAQ
 * accordion (native <details>/<summary>, no JS needed). Lives here (instead
 * of the theme root) per the template-parts/{something}/ convention; the
 * template is registered explicitly via the `theme_page_templates` filter in
 * functions.php (bishkek_park_register_page_templates()) the same way the
 * Funcity template is.
 *
 * All the text below is editable from wp-admin (the "Данные для сайта" meta
 * box on this page — fields defined in bishkek_park_contacts_meta_fields()
 * in inc/meta-boxes.php) and falls back to the strings below, matching the
 * _bp_hours/_bp_website fallback pattern on the single shop page, when a
 * field is left empty.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bp_contacts_page_id = get_queried_object_id();

$bp_contacts_address              = get_post_meta( $bp_contacts_page_id, '_bp_contacts_address', true );
$bp_contacts_hours                = get_post_meta( $bp_contacts_page_id, '_bp_contacts_hours', true );
$bp_contacts_phone                = get_post_meta( $bp_contacts_page_id, '_bp_contacts_phone', true );
$bp_contacts_tenants_text         = get_post_meta( $bp_contacts_page_id, '_bp_contacts_tenants_text', true );
$bp_contacts_tenants_download_lbl = get_post_meta( $bp_contacts_page_id, '_bp_contacts_tenants_download_label', true );
$bp_contacts_tenants_download_url = get_post_meta( $bp_contacts_page_id, '_bp_contacts_tenants_download_url', true );
$bp_contacts_tenants_hours        = get_post_meta( $bp_contacts_page_id, '_bp_contacts_tenants_hours', true );

if ( ! $bp_contacts_phone ) {
	$bp_contacts_phone = '+996 (312) 312 031';
}

// Default Q/A text, used whenever the matching wp-admin field is empty —
// registered with Polylang in inc/translatable-strings.php
// (contacts_faq_q1/a1 ... q6/a6) and translated at output time via pll_*(),
// same pattern as _bp_hours/_bp_website's fallback on the single shop page.
$bp_contacts_faq_defaults = array(
	array(
		'q' => 'Каков режим работы торгового центра?',
		'a' => 'Бишкек Парк работает ежедневно с 10:00 до 22:00, без выходных и праздников.',
	),
	array(
		'q' => 'Где находится парковка?',
		'a' => 'Паркинг расположен на подземном и надземном уровнях торгового центра. Первые 2 часа бесплатно для посетителей ТЦ при подтверждении покупки.',
	),
	array(
		'q' => 'Как арендовать площадь в торговом центре?',
		'a' => 'Для аренды помещения заполните заявку, доступную для скачивания на странице «Контакты», и отправьте её на адрес info@bishkekpark.kg. Наши менеджеры свяжутся с вами в рабочее время.',
	),
	array(
		'q' => 'Есть ли в ТЦ зона питания?',
		'a' => 'Да, на территории торгового центра работает большой фудкорт с широким выбором ресторанов и кафе на любой вкус.',
	),
	array(
		'q' => 'Как добраться до Бишкек Парка?',
		'a' => 'Мы находимся по адресу: 148Б, ул. Киевская, 720001, Бишкек, Кыргызстан. Добраться можно на общественном транспорте, такси или личном автомобиле.',
	),
	array(
		'q' => 'Есть ли детские развлечения?',
		'a' => 'В торговом центре расположены детские игровые зоны, кинотеатр и различные развлекательные заведения для посетителей всех возрастов.',
	),
);

$bp_contacts_faq = array();
foreach ( $bp_contacts_faq_defaults as $bp_faq_i => $bp_faq_default ) {
	$bp_faq_n           = $bp_faq_i + 1;
	$bp_contacts_faq[] = array(
		'q' => get_post_meta( $bp_contacts_page_id, "_bp_contacts_faq_q{$bp_faq_n}", true ),
		'a' => get_post_meta( $bp_contacts_page_id, "_bp_contacts_faq_a{$bp_faq_n}", true ),
	);
}

get_header();
?>
<main class="bp-main bp-contacts">
	<div class="bp-container bp-contacts-heading">
		<h1 class="bp-contacts-heading__title"><?php pll_esc_html_e( 'Контакты' ); ?></h1>
		<nav class="bp-breadcrumb" aria-label="breadcrumb">
			<a class="bp-breadcrumb__link" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php pll_esc_html_e( 'Главная' ); ?></a>
			<span class="bp-breadcrumb__sep" aria-hidden="true">/</span>
			<span class="bp-breadcrumb__current" aria-current="page"><?php pll_esc_html_e( 'Контакты' ); ?></span>
		</nav>
	</div>

	<div class="bp-container bp-contacts-info">
		<div class="bp-contacts-map">
			<img src="<?php echo esc_url( bishkek_park_image_url( 'map.png' ) ); ?>" alt="<?php pll_esc_attr_e( 'Карта расположения Бишкек Парк' ); ?>">
		</div>

		<div class="bp-contacts-details">
			<ul class="bp-contacts-details__list">
				<li class="bp-contacts-details__item">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-map-pin-outline.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					<span>
						<?php if ( $bp_contacts_address ) : ?>
							<?php echo esc_html( $bp_contacts_address ); ?>
						<?php else : ?>
							<?php pll_esc_html_e( '148В, ул. Киевская' ); ?>, <?php pll_esc_html_e( '720001 Бишкек / Кыргызстан' ); ?>
						<?php endif; ?>
					</span>
				</li>
				<li class="bp-contacts-details__item">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-clock-outline.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					<span>
						<?php if ( $bp_contacts_hours ) : ?>
							<?php echo esc_html( $bp_contacts_hours ); ?>
						<?php else : ?>
							<?php pll_esc_html_e( 'Ежедневно: 10:00 — 22:00' ); ?>
						<?php endif; ?>
					</span>
				</li>
				<li class="bp-contacts-details__item">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-phone-outline.svg' ) ); ?>" width="16" height="16" alt="" aria-hidden="true">
					<a href="<?php echo esc_attr( bishkek_park_get_tel_href( $bp_contacts_phone ) ); ?>"><?php echo esc_html( $bp_contacts_phone ); ?></a>
				</li>
			</ul>

			<div class="bp-contacts-tenants">
				<h2 class="bp-contacts-tenants__title"><?php pll_esc_html_e( 'Арендаторам' ); ?></h2>

				<div class="bp-contacts-tenants__card">
					<p class="bp-contacts-tenants__text">
						<?php if ( $bp_contacts_tenants_text ) : ?>
							<?php echo esc_html( $bp_contacts_tenants_text ); ?>
						<?php else : ?>
							<?php pll_esc_html_e( 'Заполненную анкету просим выслать на адрес info@bishkekpark.kg' ); ?>
						<?php endif; ?>
					</p>
					<a class="bp-contacts-tenants__download" href="<?php echo esc_url( $bp_contacts_tenants_download_url ? $bp_contacts_tenants_download_url : '#' ); ?>">
						<?php if ( $bp_contacts_tenants_download_lbl ) : ?>
							<?php echo esc_html( $bp_contacts_tenants_download_lbl ); ?>
						<?php else : ?>
							<?php pll_esc_html_e( 'Скачать заявку на аренду помещения' ); ?>
						<?php endif; ?>
					</a>
				</div>

				<p class="bp-contacts-tenants__hours">
					<?php if ( $bp_contacts_tenants_hours ) : ?>
						<?php echo nl2br( esc_html( $bp_contacts_tenants_hours ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php else : ?>
						<?php pll_esc_html_e( 'Пн - Пт — 10:00-18:00' ); ?><br>
						<?php pll_esc_html_e( 'Сб — 10:00-13:00, без обеда/перерыва' ); ?><br>
						<?php pll_esc_html_e( 'Вс – выходной' ); ?><br>
						<?php pll_esc_html_e( 'Обеденный перерыв: 12:30 — 14:00' ); ?>
					<?php endif; ?>
				</p>
			</div>
		</div>
	</div>

	<section class="bp-container bp-section bp-contacts-faq">
		<h2 class="bp-contacts-faq__title"><?php pll_esc_html_e( 'Часто задаваемые вопросы' ); ?></h2>

		<div class="bp-faq">
			<?php foreach ( $bp_contacts_faq as $bp_faq_index => $bp_faq_item ) : ?>
				<?php $bp_faq_default = $bp_contacts_faq_defaults[ $bp_faq_index ]; ?>
				<details class="bp-faq__item">
					<summary class="bp-faq__question">
						<span class="bp-faq__index"><?php echo esc_html( sprintf( '%02d', $bp_faq_index + 1 ) ); ?></span>
						<span class="bp-faq__question-text">
							<?php if ( $bp_faq_item['q'] ) : ?>
								<?php echo esc_html( $bp_faq_item['q'] ); ?>
							<?php else : ?>
								<?php pll_esc_html_e( $bp_faq_default['q'] ); ?>
							<?php endif; ?>
						</span>
						<span class="bp-faq__icon" aria-hidden="true"></span>
					</summary>
					<div class="bp-faq__answer">
						<p>
							<?php if ( $bp_faq_item['a'] ) : ?>
								<?php echo esc_html( $bp_faq_item['a'] ); ?>
							<?php else : ?>
								<?php pll_esc_html_e( $bp_faq_default['a'] ); ?>
							<?php endif; ?>
						</p>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</section>
</main>
<?php
get_footer();
