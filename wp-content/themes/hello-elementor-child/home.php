<?php
/**
 * Blog index (the page assigned as "Posts page").
 *
 * Elementor free has no theme builder, so the blog archive is a normal template
 * rather than an Elementor document. It reuses the same banner and card
 * treatment as the rest of the site.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$blog_page_id = (int) get_option( 'page_for_posts' );
$heading      = $blog_page_id ? get_the_title( $blog_page_id ) : __( 'Blog &amp; Resources', 'workfoster' );
?>

<main id="content" class="wf-single">

	<section class="wf-banner wf-blog-banner">
		<div class="wf-wrap">
			<p class="wf-crumbs">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'workfoster' ); ?></a>
				<span>/</span><?php echo esc_html( wp_strip_all_tags( $heading ) ); ?>
			</p>

			<h1 class="wf-banner__title"><?php esc_html_e( 'Notes on hiring, freelancing and how we work', 'workfoster' ); ?></h1>

			<p class="wf-banner__lede">
				<?php esc_html_e( 'Practical writing from the team that reads the briefs — what makes a good one, what a fair rate looks like, and what we get wrong.', 'workfoster' ); ?>
			</p>
		</div>
	</section>

	<div class="wf-wrap" style="padding:56px 0 88px;">
		<?php if ( have_posts() ) : ?>

			<div class="wf-posts">
				<?php
				while ( have_posts() ) :
					the_post();

					$categories = get_the_category();
					$category   = $categories ? $categories[0] : null;
					?>
					<article <?php post_class( 'wf-post-card' ); ?>>
						<a class="wf-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'workfoster-card' ); ?>
							<?php else : ?>
								<span class="wf-post-card__mark"><?php echo esc_html( workfoster_post_monogram( get_the_title() ) ); ?></span>
							<?php endif; ?>
						</a>

						<div class="wf-post-card__body">
							<p class="wf-post-card__meta">
								<?php if ( $category ) : ?>
									<a class="wf-post-card__cat" href="<?php echo esc_url( get_category_link( $category ) ); ?>">
										<?php echo esc_html( $category->name ); ?>
									</a>
								<?php endif; ?>
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								<span><?php echo esc_html( workfoster_reading_time( get_the_content() ) ); ?></span>
							</p>

							<h2 class="wf-post-card__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>

							<p class="wf-post-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>

							<a class="wf-post-card__more" href="<?php the_permalink(); ?>">
								<?php esc_html_e( 'Read article', 'workfoster' ); ?>
								<span aria-hidden="true">&rarr;</span>
							</a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<div class="wf-pagination">
				<?php
				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => esc_html__( '← Previous', 'workfoster' ),
						'next_text' => esc_html__( 'Next →', 'workfoster' ),
					)
				);
				?>
			</div>

		<?php else : ?>
			<p class="wf-careers__empty"><?php esc_html_e( 'Nothing published yet — the first article is being written.', 'workfoster' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
