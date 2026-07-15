<?php
/**
 * Homepage "Синематика" section — today's showtimes, fetched live from the
 * Cinematica.kg API (see inc/cinematica-api.php). Grouped one card per
 * movie with its showtimes as a row of pills; replaces the previous
 * bp_movie CPT listing since sessions now come straight from the cinema's
 * own booking system instead of being hand-entered in wp-admin. Cards link
 * out to the cinema's own page (bishkek_park_get_cinematica_link_url(), see
 * inc/cinematica-settings.php) since there's no single-movie page on this
 * site to send visitors to.
 *
 * Shows one row (BISHKEK_PARK_MOVIES_PER_ROW cards) as a static grid.
 * If more movies are showing today than that, it becomes a horizontally
 * scrollable slider with prev/next controls instead of wrapping rows.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_MOVIES_PER_ROW', 3 );
define( 'BISHKEK_PARK_MOVIES_PER_MOBILE_PAGE', 2 );

$bp_movies = bishkek_park_get_cinematica_sessions();

if ( ! $bp_movies ) {
	return;
}

$bp_movies_count     = count( $bp_movies );
$bp_movies_is_slider = $bp_movies_count > BISHKEK_PARK_MOVIES_PER_ROW
	|| $bp_movies_count > BISHKEK_PARK_MOVIES_PER_MOBILE_PAGE;
$bp_cinema_link_url  = bishkek_park_get_cinematica_link_url();

// Sessions come back as "HH:MM" strings already zero-padded, so a plain
// string comparison against "now" (same format, Bishkek's GMT+6) is enough
// to tell past showtimes from the next upcoming one — no DateTime parsing
// needed per pill.
$bp_now = ( new DateTime( 'now', new DateTimeZone( '+06:00' ) ) )->format( 'H:i' );
?>
<section class="bp-container bp-section bp-cinema-section">
	<div class="bp-section__header">
		<h2 class="bp-section__title"><?php pll_esc_html_e( 'Синематика' ); ?></h2>
		<?php if ( $bp_movies_is_slider ) : ?>
			<div class="bp-slider-nav" data-bp-slider-nav="bp-movies-track">
				<button type="button" class="bp-slider-nav__btn" data-bp-slider-prev aria-label="<?php pll_esc_attr_e( 'Предыдущие сеансы' ); ?>">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
				</button>
				<button type="button" class="bp-slider-nav__btn" data-bp-slider-next aria-label="<?php pll_esc_attr_e( 'Следующие сеансы' ); ?>">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-right.svg' ) ); ?>" width="7" height="12" alt="">
				</button>
			</div>
		<?php endif; ?>
	</div>

	<div
		class="bp-movies-grid<?php echo $bp_movies_is_slider ? ' bp-movies-grid--slider' : ''; ?>"
		<?php echo $bp_movies_is_slider ? 'id="bp-movies-track"' : ''; ?>
	>
		<?php
		$bp_movie_index = 0;
		foreach ( $bp_movies as $bp_movie ) :
			if ( 0 === $bp_movie_index % BISHKEK_PARK_MOVIES_PER_MOBILE_PAGE ) :
				?>
				<div class="bp-movie-slide">
				<?php
			endif;
			?>
			<a href="<?php echo esc_url( $bp_cinema_link_url ); ?>" class="bp-movie-card" target="_blank" rel="noopener noreferrer">
				<?php if ( $bp_movie['poster'] ) : ?>
					<div class="bp-movie-card__poster bp-movie-card__poster--photo">
						<img src="<?php echo esc_url( $bp_movie['poster'] ); ?>" alt="<?php echo esc_attr( $bp_movie['title'] ); ?>" loading="lazy">
					</div>
				<?php else : ?>
					<div class="bp-movie-card__poster" aria-hidden="true">
						<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-movie-placeholder.svg' ) ); ?>" width="26" height="26" alt="">
					</div>
				<?php endif; ?>
				<div class="bp-movie-card__body">
					<div class="bp-movie-card__meta">
						<span class="bp-movie-card__label"><?php pll_esc_html_e( 'Сегодня в кино' ); ?></span>
					</div>
					<h3 class="bp-movie-card__title"><?php echo esc_html( $bp_movie['title'] ); ?></h3>
					<?php if ( $bp_movie['times'] ) : ?>
						<?php
						// Times are sorted chronologically, so the first one that
						// hasn't passed yet is the next upcoming session.
						$bp_next_time = null;
						foreach ( $bp_movie['times'] as $bp_time ) {
							if ( $bp_time >= $bp_now ) {
								$bp_next_time = $bp_time;
								break;
							}
						}
						?>
						<div class="bp-movie-card__times">
							<?php foreach ( $bp_movie['times'] as $bp_time ) : ?>
								<?php
								$bp_time_class = '';
								if ( $bp_time < $bp_now ) {
									$bp_time_class = ' is-past';
								} elseif ( $bp_time === $bp_next_time ) {
									$bp_time_class = ' is-active';
								}
								?>
								<span class="bp-time-pill<?php echo esc_attr( $bp_time_class ); ?>"><?php echo esc_html( $bp_time ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</a>
			<?php
			++$bp_movie_index;
			if ( 0 === $bp_movie_index % BISHKEK_PARK_MOVIES_PER_MOBILE_PAGE || $bp_movie_index === $bp_movies_count ) :
				?>
				</div>
				<?php
			endif;
		endforeach;
		?>
	</div>
</section>
