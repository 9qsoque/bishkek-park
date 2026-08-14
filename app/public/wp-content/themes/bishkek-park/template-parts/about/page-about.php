<?php
/**
 * Template Name: О нас
 *
 * About page: hero (bag photo + title + text) with a static 4-card feature
 * row, a Руководство (leadership) section pulled from the bp_leader post
 * type, an Этапы нашего развития (milestones) section pulled from the
 * bp_milestone post type, and a closing contact CTA that reuses the Funcity
 * page's .bp-funcity-cta block verbatim (see functions.php, which enqueues
 * funcity.css here for that reason). Lives here (instead of the theme root)
 * per the template-parts/{something}/ convention; the template is
 * registered explicitly via the `theme_page_templates` filter in
 * functions.php (bishkek_park_register_page_templates()), the same way the
 * Funcity/Контакты/Карта ТЦ templates are.
 *
 * The feature row is fixed content (no CPT — same reasoning as the Funcity
 * play zones). Leadership and milestones are fully editable from wp-admin;
 * each section hides itself if no posts are published yet, matching the
 * "return early if empty" convention used by the homepage's CPT-backed
 * sections.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_LEADERS_PER_ROW', 3 );

$bp_about_features = array(
	array(
		'icon'  => 'icon-utensils.svg',
		'title' => 'Фудкорт',
		'text'  => 'Обширная зона с вкусами со всего мира - для любого бюджета.',
	),
	array(
		'icon'  => 'icon-shopping-bag.svg',
		'title' => 'Мировые бренды',
		'text'  => 'Флагманские магазины и актуальные коллекции - всё в одном месте.',
	),
	array(
		'icon'  => 'icon-clapperboard.svg',
		'title' => 'Кинотеатр',
		'text'  => 'Ультрасовременный пятизальный кинотеатр «Синематика».',
	),
	array(
		'icon'  => 'icon-gamepad.svg',
		'title' => 'FunCity',
		'text'  => 'Гигантская игровая зона для детей - развлечения, безопасность и комфорт.',
	),
);

$bp_leaders_query = new WP_Query(
	array(
		'post_type'      => 'bp_leader',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
		// Query the default (RU) language as the canonical list, then swap in
		// each post's current-language translation where one exists — see
		// CLAUDE.md "Polylang CPT translation".
		'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
	)
);
$bp_leaders_is_slider = $bp_leaders_query->post_count > BISHKEK_PARK_LEADERS_PER_ROW;

$bp_milestones_query = new WP_Query(
	array(
		'post_type'      => 'bp_milestone',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
		'lang'           => function_exists( 'pll_default_language' ) ? pll_default_language() : '',
	)
);

get_header();
?>
<main class="bp-main bp-about">
	<div class="bp-container bp-back-link-wrap">
		<a class="bp-back-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
			<?php pll_esc_html_e( 'Назад на главную' ); ?>
		</a>
	</div>

	<section class="bp-container bp-about-hero">
		<img class="bp-about-hero__image" src="<?php echo esc_url( bishkek_park_image_url( 'about-hero.png' ) ); ?>" width="220" height="220" alt="">
		<h1 class="bp-about-hero__title"><?php pll_esc_html_e( 'Крупнейший ТРЦ европейского класса в Кыргызстане' ); ?></h1>
		<p class="bp-about-hero__text"><?php pll_esc_html_e( 'ТРЦ «Bishkek Park» распахнул свои двери в 2013 году, навсегда изменив представление жителей столицы о качественном досуге и шоппинге' ); ?></p>

		<div class="bp-about-features">
			<?php foreach ( $bp_about_features as $bp_about_feature ) : ?>
				<div class="bp-about-feature-card">
					<span class="bp-about-feature-card__icon">
						<img src="<?php echo esc_url( bishkek_park_icon_url( $bp_about_feature['icon'] ) ); ?>" width="24" height="24" alt="" aria-hidden="true">
					</span>
					<div class="bp-about-feature-card__body">
						<h3 class="bp-about-feature-card__title"><?php pll_esc_html_e( $bp_about_feature['title'] ); ?></h3>
						<p class="bp-about-feature-card__text"><?php pll_esc_html_e( $bp_about_feature['text'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ( $bp_leaders_query->have_posts() ) : ?>
		<section class="bp-container bp-section bp-about-leadership">
			<h2 class="bp-about-leadership__title"><?php pll_esc_html_e( 'Руководство ТРЦ «Bishkek Park»' ); ?></h2>

			<div class="bp-leaders-grid<?php echo $bp_leaders_is_slider ? ' bp-leaders-grid--slider' : ''; ?>">
				<?php
				while ( $bp_leaders_query->have_posts() ) :
					$bp_leaders_query->the_post();

					global $post;
					$bp_leader_localized_id = bishkek_park_get_localized_post_id( get_the_ID() );
					if ( $bp_leader_localized_id !== get_the_ID() ) {
						$post = get_post( $bp_leader_localized_id );
						setup_postdata( $post );
					}

					$bp_leader_position    = get_post_meta( get_the_ID(), '_bp_position', true );
					$bp_leader_description = get_post_meta( get_the_ID(), '_bp_description', true );
					?>
					<article class="bp-leader-card">
						<span class="bp-leader-card__image">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium', array( 'class' => 'bp-leader-card__photo' ) ); ?>
							<?php endif; ?>
						</span>
						<?php if ( $bp_leader_position ) : ?>
							<span class="bp-leader-card__position"><?php echo esc_html( $bp_leader_position ); ?></span>
						<?php endif; ?>
						<h3 class="bp-leader-card__name"><?php the_title(); ?></h3>
						<?php if ( $bp_leader_description ) : ?>
							<p class="bp-leader-card__text"><?php echo esc_html( $bp_leader_description ); ?></p>
						<?php endif; ?>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $bp_milestones_query->have_posts() ) : ?>
		<section class="bp-container bp-section bp-about-milestones">
			<h2 class="bp-about-leadership__title"><?php pll_esc_html_e( 'Этапы нашего развития' ); ?></h2>
			<p class="bp-about-section-text"><?php pll_esc_html_e( 'С момента закладки фундамента и до сегодняшнего дня мы постоянно совершенствуемся, чтобы предвосхищать ваши самые смелые ожидания.' ); ?></p>

			<div class="bp-milestones-grid">
				<?php
				while ( $bp_milestones_query->have_posts() ) :
					$bp_milestones_query->the_post();

					global $post;
					$bp_milestone_localized_id = bishkek_park_get_localized_post_id( get_the_ID() );
					if ( $bp_milestone_localized_id !== get_the_ID() ) {
						$post = get_post( $bp_milestone_localized_id );
						setup_postdata( $post );
					}

					$bp_milestone_year        = get_post_meta( get_the_ID(), '_bp_year', true );
					$bp_milestone_description = get_post_meta( get_the_ID(), '_bp_description', true );
					?>
					<article class="bp-milestone-card">
						<?php if ( $bp_milestone_year ) : ?>
							<span class="bp-milestone-card__year"><?php echo esc_html( $bp_milestone_year ); ?></span>
						<?php endif; ?>
						<h3 class="bp-milestone-card__title"><?php the_title(); ?></h3>
						<?php if ( $bp_milestone_description ) : ?>
							<p class="bp-milestone-card__text"><?php echo esc_html( $bp_milestone_description ); ?></p>
						<?php endif; ?>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endif; ?>

	<section class="bp-funcity-cta">
		<div class="bp-container bp-funcity-cta__inner">
			<h2 class="bp-funcity-cta__title"><?php pll_esc_html_e( 'Остались вопросы?' ); ?></h2>
			<p class="bp-funcity-cta__subtitle"><?php pll_esc_html_e( 'Свяжитесь с нами' ); ?></p>
			<p class="bp-funcity-cta__text"><?php pll_esc_html_e( 'Мы готовы ответить на любые ваши вопросы' ); ?></p>
			<a class="bp-btn bp-btn--primary" href="tel:+996312312031"><?php pll_esc_html_e( 'Связаться с нами' ); ?></a>
		</div>
	</section>
</main>
<?php
get_footer();
