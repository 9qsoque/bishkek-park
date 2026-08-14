<?php
/**
 * Site footer component: address/social bar and hours/links bar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<footer class="bp-site-footer">
	<div class="bp-container bp-site-footer__top">
		<a class="bp-logo bp-logo--footer" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Bishkek Park">
			<?php echo bishkek_park_get_logo_svg(); ?>
		</a>

		<address class="bp-site-footer__address">
			<span class="bp-site-footer__address-item">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-map-pin.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
				<?php pll_esc_html_e( '148В, ул. Киевская' ); ?><br><?php pll_esc_html_e( '720001 Бишкек / Кыргызстан' ); ?>
			</span>
			<a class="bp-site-footer__phone" href="tel:+996312312031">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-phone.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
				+996 (312) 312 031
			</a>
		</address>

		<div class="bp-site-footer__social">
			<a href="#" aria-label="Instagram">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-instagram.svg' ) ); ?>" width="18" height="18" alt="">
			</a>
			<a href="#" aria-label="YouTube">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-youtube.svg' ) ); ?>" width="18" height="18" alt="">
			</a>
			<a href="#" aria-label="Telegram">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-telegram.svg' ) ); ?>" width="18" height="18" alt="">
			</a>
		</div>
	</div>

	<div class="bp-container bp-site-footer__mobile">
		<a class="bp-logo bp-logo--footer bp-site-footer__mobile-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="Bishkek Park">
			<?php echo bishkek_park_get_logo_svg(); ?>
		</a>

		<div class="bp-site-footer__mobile-group">
			<span class="bp-site-footer__mobile-title"><?php pll_esc_html_e( 'Наш адрес' ); ?></span>
			<address class="bp-site-footer__mobile-text">148В, Kievskaya Str. 720001 Bishkek<br>/ Kyrgyzstan</address>
		</div>

		<div class="bp-site-footer__mobile-group">
			<span class="bp-site-footer__mobile-title "><?php pll_esc_html_e( 'Связаться с нами' ); ?></span>
			<a class="bp-site-footer__mobile-text" href="tel:+996312312031">+996 (312) 312 031</a>
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

		<div class="bp-site-footer__social bp-site-footer__social--mobile">
			<a href="#" aria-label="Instagram">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-instagram.svg' ) ); ?>" width="18" height="18" alt="">
			</a>
			<a href="#" aria-label="YouTube">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-youtube.svg' ) ); ?>" width="18" height="18" alt="">
			</a>
			<a href="#" aria-label="Telegram">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-telegram.svg' ) ); ?>" width="18" height="18" alt="">
			</a>
		</div>
	</div>

	<div class="bp-site-footer__bottom">
		<div class="bp-container bp-site-footer__bottom-inner">
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
