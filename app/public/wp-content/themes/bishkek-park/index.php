<?php
/**
 * Fallback template: used for any request that doesn't match a more
 * specific template (single posts, archives, search, 404, etc.).
 * The homepage itself is handled by front-page.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="bp-main">
	<div class="bp-container" style="padding-block: 48px;">
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h1><?php the_title(); ?></h1>
					<div><?php the_content(); ?></div>
				</article>
				<?php
			endwhile;
			?>
		<?php else : ?>
			<h1><?php esc_html_e( 'Ничего не найдено', 'bishkek-park' ); ?></h1>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
