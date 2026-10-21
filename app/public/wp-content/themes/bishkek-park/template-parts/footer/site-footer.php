<?php
/**
 * Site footer component. Desktop: logo + Instagram, then address/phones,
 * opening hours and links side by side; below 783px the separate
 * .bp-site-footer__mobile layout is shown instead.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The address opens Bishkek Park in 2GIS; the leasing/sales numbers (see
// bishkek_park_get_whatsapp_contacts()) open a WhatsApp chat.
$bp_footer_2gis_url  = 'https://2gis.kg/bishkek/firm/70000001019343641';
$bp_footer_whatsapps = bishkek_park_get_whatsapp_contacts();
$bp_footer_instagram = 'https://www.instagram.com/bishkekpark/';
?>
<footer class="bp-site-footer bp-pattern bp-pattern--light">
	<div class="bp-container bp-site-footer__top">
		<a class="bp-logo bp-logo--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Bishkek Park">
			<?php echo bishkek_park_get_logo_svg(); ?>
		</a>

		<a class="bp-site-footer__instagram" href="<?php echo esc_url( $bp_footer_instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram @bishkekpark">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-instagram.svg' ) ); ?>" width="18" height="18" alt="" aria-hidden="true">
			@bishkekpark
		</a>

		<address class="bp-site-footer__address">
			<a class="bp-site-footer__address-item" href="<?php echo esc_url( $bp_footer_2gis_url ); ?>" target="_blank" rel="noopener noreferrer">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-map-pin.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
				<span><?php pll_esc_html_e( '148В, ул. Киевская' ); ?><br><?php pll_esc_html_e( '720001 Бишкек / Кыргызстан' ); ?></span>
			</a>
			<div class="bp-site-footer__phones">
				<a class="bp-site-footer__phone" href="tel:+996312312031">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-phone.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
					<span class="bp-site-footer__phone-label"><?php pll_esc_html_e( 'Связь' ); ?>:</span>
					+996 (312) 312 031
				</a>
				<?php foreach ( $bp_footer_whatsapps as $bp_footer_whatsapp ) : ?>
					<a class="bp-site-footer__phone" href="<?php echo esc_url( $bp_footer_whatsapp['url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-whatsapp.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
						<span class="bp-site-footer__phone-label"><?php pll_esc_html_e( $bp_footer_whatsapp['label'] ); ?>:</span>
						<?php echo esc_html( $bp_footer_whatsapp['number'] ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</address>

		<div class="bp-footer-hours">
			<span class="bp-footer-hours__label"><?php pll_esc_html_e( 'Время работы' ); ?></span>
			<table>
				<tbody>
					<tr>
						<th scope="col"><?php pll_esc_html_e( 'Пн' ); ?></th><th scope="col"><?php pll_esc_html_e( 'Вт' ); ?></th><th scope="col"><?php pll_esc_html_e( 'Ср' ); ?></th><th scope="col"><?php pll_esc_html_e( 'Чт' ); ?></th><th scope="col"><?php pll_esc_html_e( 'Пт' ); ?></th><th scope="col"><?php pll_esc_html_e( 'Сб' ); ?></th><th scope="col"><?php pll_esc_html_e( 'Вс' ); ?></th>
					</tr>
					<tr>
						<td>10:00<br>22:00</td>
						<td>10:00<br>22:00</td>
						<td>10:00<br>22:00</td>
						<td>10:00<br>22:00</td>
						<td>10:00<br>22:00</td>
						<td>10:00<br>22:00</td>
						<td>10:00<br>22:00</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="bp-footer-links">
			<ul>
				<li><a href="<?php echo esc_url( bishkek_park_get_contacts_page_url() ); ?>"><?php pll_esc_html_e( 'Контакты' ); ?></a></li>
				<li><a href="#"><?php pll_esc_html_e( 'Обратная связь' ); ?></a></li>
				<li><a href="#"><?php pll_esc_html_e( 'Арендаторам' ); ?></a></li>
			</ul>
			<ul>
				<li><a href="#"><?php pll_esc_html_e( 'Рекламные услуги' ); ?></a></li>
				<li><a href="#"><?php pll_esc_html_e( 'Вакансии' ); ?></a></li>
				<li><a href="#"><?php pll_esc_html_e( 'Политика конфиденциальности' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="bp-container bp-site-footer__mobile">
		<div class="bp-site-footer__mobile-head">
			<a class="bp-logo bp-logo--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Bishkek Park">
				<?php echo bishkek_park_get_logo_svg(); ?>
			</a>
			<a class="bp-site-footer__instagram" href="<?php echo esc_url( $bp_footer_instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram @bishkekpark">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-instagram.svg' ) ); ?>" width="18" height="18" alt="" aria-hidden="true">
				@bishkekpark
			</a>
		</div>

		<div class="bp-site-footer__mobile-group">
			<span class="bp-site-footer__mobile-title"><?php pll_esc_html_e( 'Наш адрес' ); ?></span>
			<address class="bp-site-footer__mobile-text">
				<a href="<?php echo esc_url( $bp_footer_2gis_url ); ?>" target="_blank" rel="noopener noreferrer">148В, Kievskaya Str. 720001 Bishkek<br>/ Kyrgyzstan</a>
			</address>
		</div>

		<div class="bp-site-footer__mobile-group">
			<span class="bp-site-footer__mobile-title "><?php pll_esc_html_e( 'Связаться с нами' ); ?></span>
			<a class="bp-site-footer__mobile-text bp-site-footer__mobile-phone" href="tel:+996312312031">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-phone.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
				<?php pll_esc_html_e( 'Связь' ); ?>: +996 (312) 312 031
			</a>
			<?php foreach ( $bp_footer_whatsapps as $bp_footer_whatsapp ) : ?>
				<a class="bp-site-footer__mobile-text bp-site-footer__mobile-phone" href="<?php echo esc_url( $bp_footer_whatsapp['url'] ); ?>" target="_blank" rel="noopener noreferrer">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-whatsapp.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
					<?php pll_esc_html_e( $bp_footer_whatsapp['label'] ); ?>: <?php echo esc_html( $bp_footer_whatsapp['number'] ); ?>
				</a>
			<?php endforeach; ?>
		</div>

		<hr class="bp-site-footer__divider">

		<div class="bp-footer-links bp-site-footer__mobile-links">
			<ul>
				<li><a href="#"><?php pll_esc_html_e( 'Шопинг' ); ?></a></li>
				<li><a href="#"><?php pll_esc_html_e( 'Синематика' ); ?></a></li>
				<li><a href="#">Funcity</a></li>
			</ul>
			<ul>
				<li><a href="#"><?php pll_esc_html_e( 'Кафе и рестораны' ); ?></a></li>
				<li><a href="#"><?php pll_esc_html_e( 'Мероприятия' ); ?></a></li>
				<li><a href="<?php echo esc_url( bishkek_park_get_contacts_page_url() ); ?>"><?php pll_esc_html_e( 'Контакты' ); ?></a></li>
			</ul>
		</div>

		<div class="bp-site-footer__mobile-utility">
			<a href="<?php echo esc_url( bishkek_park_get_mall_map_page_url() ); ?>"><?php pll_esc_html_e( 'План ТЦ' ); ?></a>
			<a href="#"><?php pll_esc_html_e( 'Парковка' ); ?></a>
		</div>
	</div>

	<?php
	$bp_structured_data = array(
		'@context'                  => 'https://schema.org',
		'@type'                     => 'ShoppingCenter',
		'name'                      => get_bloginfo( 'name' ),
		'url'                       => home_url( '/' ),
		'telephone'                 => '+996312312031',
		'address'                   => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => '148В, ул. Киевская',
			'addressLocality' => 'Бишкек',
			'postalCode'      => '720001',
			'addressCountry'  => 'KG',
		),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Sunday' ),
				'opens'     => '10:00',
				'closes'    => '22:00',
			),
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Friday', 'Saturday' ),
				'opens'     => '10:00',
				'closes'    => '22:00',
			),
		),
	);
	?>
	<script type="application/ld+json"><?php echo wp_json_encode( $bp_structured_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
</footer>
