<?php
/**
 * Fetches today's cinema showtimes from the external Cinematica.kg API for
 * the homepage "Синематика" section (see template-parts/homepage/cinema.php).
 * Sessions come live from the cinema's own booking system for Bishkek Park's
 * hall (cinema_id=3 by default) instead of being hand-entered as posts — the
 * API endpoint and the public cinema link are editable from wp-admin (see
 * bishkek_park_get_cinematica_api_url()/bishkek_park_get_cinematica_link_url()
 * in inc/cinematica-settings.php) in case that ID ever changes.
 *
 * A WP-Cron job refreshes the cache in the background every 10 minutes so a
 * real visitor's page load never pays the external API's latency; the
 * transient itself is kept for an hour as a safety net in case cron doesn't
 * fire (e.g. very low traffic), and a failed refresh — cron or on-demand —
 * always leaves the previous same-day cache in place rather than clearing it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BISHKEK_PARK_CINEMATICA_BASE_URL', 'https://cinematica.kg' );
define( 'BISHKEK_PARK_CINEMATICA_CACHE_KEY', 'bishkek_park_cinematica_sessions' );
define( 'BISHKEK_PARK_CINEMATICA_CRON_HOOK', 'bishkek_park_refresh_cinematica_sessions' );

/**
 * Returns today's cinema sessions grouped by movie, sorted by earliest
 * showtime. Each entry: array( 'title', 'poster' (absolute URL or ''),
 * 'times' (array of "HH:MM" strings, chronological, deduped) ). Sessions
 * that are disabled or not currently on sale (`is_disabled`/`disable_sales`
 * from the API) are excluded.
 *
 * Normally this just reads the transient the cron job keeps warm. It only
 * calls the API directly on a cache miss (first run, or cron having failed
 * to fire for over an hour) — that live call still falls back to a same-day
 * stale cache on failure, or an empty array if there's no cache at all.
 */
function bishkek_park_get_cinematica_sessions() {
	$today  = current_time( 'd.m.y' );
	$cached = get_transient( BISHKEK_PARK_CINEMATICA_CACHE_KEY );

	if ( is_array( $cached ) && isset( $cached['date'] ) && $today === $cached['date'] ) {
		return $cached['sessions'];
	}

	$sessions = bishkek_park_fetch_cinematica_sessions( $today );

	if ( false === $sessions ) {
		return is_array( $cached ) && isset( $cached['sessions'] ) ? $cached['sessions'] : array();
	}

	bishkek_park_store_cinematica_sessions_cache( $today, $sessions );

	return $sessions;
}

/**
 * Cron callback (see BISHKEK_PARK_CINEMATICA_CRON_HOOK below): re-fetches
 * and re-caches today's sessions in the background. Leaves the existing
 * cache untouched on failure so a transient API hiccup doesn't blank the
 * homepage section for site visitors.
 */
function bishkek_park_refresh_cinematica_sessions_cache() {
	$today    = current_time( 'd.m.y' );
	$sessions = bishkek_park_fetch_cinematica_sessions( $today );

	if ( false !== $sessions ) {
		bishkek_park_store_cinematica_sessions_cache( $today, $sessions );
	}
}
add_action( BISHKEK_PARK_CINEMATICA_CRON_HOOK, 'bishkek_park_refresh_cinematica_sessions_cache' );

/**
 * Calls the Cinematica.kg API and returns today's sessions grouped by movie,
 * or false on a network/response failure. Pure fetch — doesn't touch the
 * transient, so it's shared by both the on-demand getter and the cron job.
 */
function bishkek_park_fetch_cinematica_sessions( $today ) {
	$response = wp_remote_get(
		bishkek_park_get_cinematica_api_url(),
		array( 'timeout' => 5 )
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	$list = isset( $body['list'] ) && is_array( $body['list'] ) ? $body['list'] : array();

	$movies = array();

	foreach ( $list as $session ) {
		if ( ! isset( $session['date'], $session['movie'], $session['time'] ) || $today !== $session['date'] ) {
			continue;
		}

		if ( ! empty( $session['is_disabled'] ) || ! empty( $session['disable_sales'] ) ) {
			continue;
		}

		$movie_id = isset( $session['movie_id'] ) ? $session['movie_id'] : $session['movie'];

		if ( ! isset( $movies[ $movie_id ] ) ) {
			$poster = isset( $session['poster'] ) ? $session['poster'] : '';
			if ( $poster && 0 !== strpos( $poster, 'http' ) ) {
				$poster = BISHKEK_PARK_CINEMATICA_BASE_URL . $poster;
			}

			$movies[ $movie_id ] = array(
				'title'  => $session['movie'],
				'poster' => $poster,
				'times'  => array(),
			);
		}

		if ( ! in_array( $session['time'], $movies[ $movie_id ]['times'], true ) ) {
			$movies[ $movie_id ]['times'][] = $session['time'];
		}
	}

	foreach ( $movies as $movie_id => $movie ) {
		sort( $movies[ $movie_id ]['times'] );
	}

	usort(
		$movies,
		function ( $a, $b ) {
			$a_time = $a['times'] ? $a['times'][0] : '';
			$b_time = $b['times'] ? $b['times'][0] : '';
			return strcmp( $a_time, $b_time );
		}
	);

	return array_values( $movies );
}

/**
 * Stores a fetched sessions list in the transient, tagged with the date it
 * was fetched for. HOUR_IN_SECONDS is a safety-net expiry, not the normal
 * refresh cadence — the cron job (every 10 minutes) is what actually keeps
 * this warm; the hour just bounds how stale it can get if cron stalls.
 */
function bishkek_park_store_cinematica_sessions_cache( $date, $sessions ) {
	set_transient(
		BISHKEK_PARK_CINEMATICA_CACHE_KEY,
		array(
			'date'     => $date,
			'sessions' => $sessions,
		),
		HOUR_IN_SECONDS
	);
}

/**
 * Registers the 10-minute cron schedule WP core doesn't ship by default.
 */
function bishkek_park_add_cinematica_cron_schedule( $schedules ) {
	$schedules['bishkek_park_ten_minutes'] = array(
		'interval' => 10 * MINUTE_IN_SECONDS,
		'display'  => __( 'Every 10 Minutes', 'bishkek-park' ),
	);

	return $schedules;
}
add_filter( 'cron_schedules', 'bishkek_park_add_cinematica_cron_schedule' );

/**
 * Schedules the recurring cache-refresh cron job if it isn't already
 * scheduled. Hooked to `init` rather than a theme (de)activation hook —
 * themes don't get a reliable activation hook the way plugins do, and
 * `wp_next_scheduled()` makes this a cheap no-op on every request after the
 * first, which is the standard theme workaround for that.
 */
function bishkek_park_schedule_cinematica_cron() {
	if ( ! wp_next_scheduled( BISHKEK_PARK_CINEMATICA_CRON_HOOK ) ) {
		wp_schedule_event( time(), 'bishkek_park_ten_minutes', BISHKEK_PARK_CINEMATICA_CRON_HOOK );
	}
}
add_action( 'init', 'bishkek_park_schedule_cinematica_cron' );

/**
 * Clears the scheduled cron job when switching away from this theme, so it
 * doesn't keep firing (and hitting the external API) for an inactive theme.
 */
function bishkek_park_unschedule_cinematica_cron() {
	wp_clear_scheduled_hook( BISHKEK_PARK_CINEMATICA_CRON_HOOK );
}
add_action( 'switch_theme', 'bishkek_park_unschedule_cinematica_cron' );
