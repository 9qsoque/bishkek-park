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

		<div class="bp-site-footer__address">
			<span>г. Москва,<br>Кутузовский проспект, д. А</span>
			<span class="bp-site-footer__phone">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-phone.svg' ) ); ?>" width="14" height="14" alt="" aria-hidden="true">
				+7 (495) 644-45-44
			</span>
		</div>

		<div class="bp-site-footer__social">
			<a href="#" aria-label="Instagram">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-instagram.svg' ) ); ?>" width="18" height="18" alt="">
			</a>
			<a href="#" aria-label="VK">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-vk.svg' ) ); ?>" width="18" height="18" alt="">
			</a>
			<a href="#" aria-label="Telegram">
				<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-telegram.svg' ) ); ?>" width="18" height="18" alt="">
			</a>
		</div>
	</div>

	<div class="bp-site-footer__bottom">
		<div class="bp-container bp-site-footer__bottom-inner">
			<div class="bp-footer-hours">
				<span class="bp-footer-hours__label">Время работы</span>
				<table>
					<tbody>
						<tr>
							<th>Пн</th><th>Вт</th><th>Ср</th><th>Чт</th><th>Пт</th><th>Сб</th><th>Вс</th>
						</tr>
						<tr>
							<td>10:00<br>22:00</td>
							<td>10:00<br>22:00</td>
							<td>10:00<br>22:00</td>
							<td>10:00<br>22:00</td>
							<td>10:00<br>23:00</td>
							<td>10:00<br>23:00</td>
							<td>10:00<br>22:00</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="bp-footer-links">
				<ul>
					<li><a href="#">Контакты</a></li>
					<li><a href="#">Обратная связь</a></li>
					<li><a href="#">Арендаторам</a></li>
				</ul>
				<ul>
					<li><a href="#">Рекламные услуги</a></li>
					<li><a href="#">Вакансии</a></li>
					<li><a href="#">Политика конфиденциальности</a></li>
				</ul>
			</div>
		</div>
	</div>
</footer>
