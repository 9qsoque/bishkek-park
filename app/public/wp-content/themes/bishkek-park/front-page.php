<?php
/**
 * Static front page: assembles the homepage from template-parts/homepage/*.
 * Shops, cinema listings, and events are editable in WP Admin; the rest
 * of the sections are structural and live in code.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="bp-main">
	<?php
	get_template_part( 'template-parts/homepage/hero' );
	get_template_part( 'template-parts/homepage/categories' );
	get_template_part( 'template-parts/homepage/parking' );
	get_template_part( 'template-parts/homepage/shops' );
	get_template_part( 'template-parts/homepage/cinema' );
	get_template_part( 'template-parts/homepage/events' );
	get_template_part( 'template-parts/homepage/leasing' );
	?>
</main>

<?php
get_footer();
