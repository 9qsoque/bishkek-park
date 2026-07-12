<?php
/**
 * Homepage "Синематика" section, pulled from the bp_movie post type.
 * Edit/add sessions under WP Admin -> Синематика.
 *
 * Shows one row (BISHKEK_PARK_MOVIES_PER_ROW cards) as a static grid.
 * If more sessions are published than that, it becomes a horizontally
 * scrollable slider with prev/next controls instead of wrapping rows.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_MOVIES_PER_ROW', 3 );
define( 'BISHKEK_PARK_MOVIES_PER_MOBILE_PAGE', 2 );

$bp_movies_query = new WP_Query(
	array(
		'post_type'      => 'bp_movie',
		'posts_per_page' => 24,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( ! $bp_movies_query->have_posts() ) {
	return;
}

$bp_movies_is_slider = $bp_movies_query->post_count > BISHKEK_PARK_MOVIES_PER_ROW
	|| $bp_movies_query->post_count > BISHKEK_PARK_MOVIES_PER_MOBILE_PAGE;
?>
<section class="bp-container bp-section bp-cinema-section">
	<div class="bp-section__header">
		<h2 class="bp-section__title">Синематика</h2>
		<?php if ( $bp_movies_is_slider ) : ?>
			<div class="bp-slider-nav" data-bp-slider-nav="bp-movies-track">
				<button type="button" class="bp-slider-nav__btn" data-bp-slider-prev aria-label="Предыдущие сеансы">
					<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-arrow-left.svg' ) ); ?>" width="7" height="12" alt="">
				</button>
				<button type="button" class="bp-slider-nav__btn" data-bp-slider-next aria-label="Следующие сеансы">
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
		while ( $bp_movies_query->have_posts() ) :
			$bp_movies_query->the_post();

			$label        = get_post_meta( get_the_ID(), '_bp_label', true );
			$subtitle     = get_post_meta( get_the_ID(), '_bp_subtitle', true );
			$times_raw    = get_post_meta( get_the_ID(), '_bp_times', true );
			$active_time  = trim( get_post_meta( get_the_ID(), '_bp_active_time', true ) );
			$times        = $times_raw ? array_map( 'trim', explode( ',', $times_raw ) ) : array();
			$primary_time = $active_time ? $active_time : ( $times ? $times[0] : '' );

			if ( 0 === $bp_movie_index % BISHKEK_PARK_MOVIES_PER_MOBILE_PAGE ) :
				?>
				<div class="bp-movie-slide">
				<?php
			endif;
			?>
			<a href="<?php the_permalink(); ?>" class="bp-movie-card">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="bp-movie-card__poster bp-movie-card__poster--photo">
						<?php the_post_thumbnail( 'thumbnail' ); ?>
					</div>
				<?php else : ?>
					<div class="bp-movie-card__poster" aria-hidden="true">
						<img src="<?php echo esc_url( bishkek_park_icon_url( 'icon-movie-placeholder.svg' ) ); ?>" width="26" height="26" alt="">
					</div>
				<?php endif; ?>
				<div class="bp-movie-card__body">
					<?php if ( $label || $primary_time ) : ?>
						<div class="bp-movie-card__meta">
							<?php if ( $label ) : ?>
								<span class="bp-movie-card__label"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
							<?php if ( $primary_time ) : ?>
								<span class="bp-movie-card__time"><?php echo esc_html( $primary_time ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<h3 class="bp-movie-card__title"><?php the_title(); ?></h3>
					<?php if ( $subtitle ) : ?>
						<p class="bp-movie-card__subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
					<?php if ( $times ) : ?>
						<div class="bp-movie-card__times">
							<?php foreach ( $times as $time ) : ?>
								<span class="bp-time-pill<?php echo ( $time === $active_time ) ? ' is-active' : ''; ?>"><?php echo esc_html( $time ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</a>
			<?php
			++$bp_movie_index;
			if ( 0 === $bp_movie_index % BISHKEK_PARK_MOVIES_PER_MOBILE_PAGE || $bp_movie_index === $bp_movies_query->post_count ) :
				?>
				</div>
				<?php
			endif;
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
