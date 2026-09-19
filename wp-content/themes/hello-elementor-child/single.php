<?php
/**
 * Single blog post.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$blog_page_id = (int) get_option( 'page_for_posts' );
$blog_url     = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/' );

while ( have_posts() ) :
	the_post();

	$categories = get_the_category();
	$category   = $categories ? $categories[0] : null;
	?>

	<main id="content" class="wf-single">

		<section class="wf-single__hero">
			<div class="wf-wrap">
				<nav class="wf-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'workfoster' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'workfoster' ); ?></a>
					<span>/</span>
					<a href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'Blog', 'workfoster' ); ?></a>
					<span>/</span>
					<?php echo esc_html( get_the_title() ); ?>
				</nav>

				<?php if ( $category ) : ?>
					<p style="margin:0 0 14px;"><span class="wf-eyebrow"><?php echo esc_html( $category->name ); ?></span></p>
				<?php endif; ?>

				<h1 class="wf-single__title" style="max-width:20ch;"><?php the_title(); ?></h1>

				<div class="wf-single__sub">
					<span><?php echo esc_html( get_the_author() ); ?></span>
					<span><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
					<span><?php echo esc_html( workfoster_reading_time( get_the_content() ) ); ?></span>
				</div>
			</div>
		</section>

		<div class="wf-wrap">
			<article class="wf-article">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="wf-article__media"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>

				<div class="wf-single__content wf-article__body">
					<?php the_content(); ?>
				</div>

				<footer class="wf-article__foot">
					<p><?php esc_html_e( 'Share this article', 'workfoster' ); ?></p>
					<?php
					$permalink = rawurlencode( get_permalink() );
					$title     = rawurlencode( get_the_title() );
					?>
					<div class="wf-share">
						<a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo esc_attr( $permalink ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'workfoster' ); ?>"><?php WF_Icons::render( 'linkedin' ); ?></a>
						<a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $permalink ); ?>&amp;text=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on X', 'workfoster' ); ?>"><?php WF_Icons::render( 'twitter' ); ?></a>
						<a href="mailto:?subject=<?php echo esc_attr( $title ); ?>&amp;body=<?php echo esc_attr( $permalink ); ?>" aria-label="<?php esc_attr_e( 'Share by email', 'workfoster' ); ?>"><?php WF_Icons::render( 'mail' ); ?></a>
					</div>
				</footer>
			</article>

			<?php
			$related = new WP_Query(
				array(
					'post_type'           => 'post',
					'posts_per_page'      => 2,
					'post__not_in'        => array( get_the_ID() ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);

			if ( $related->have_posts() ) :
				?>
				<section class="wf-article__related">
					<h2><?php esc_html_e( 'Keep reading', 'workfoster' ); ?></h2>

					<div class="wf-posts wf-posts--two">
						<?php
						while ( $related->have_posts() ) :
							$related->the_post();
							?>
							<article class="wf-post-card">
								<a class="wf-post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
									<span class="wf-post-card__mark"><?php echo esc_html( workfoster_post_monogram( get_the_title() ) ); ?></span>
								</a>
								<div class="wf-post-card__body">
									<p class="wf-post-card__meta"><time><?php echo esc_html( get_the_date() ); ?></time></p>
									<h3 class="wf-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<p class="wf-post-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
				</section>
				<?php
				wp_reset_postdata();
			endif;
			?>
		</div>
	</main>

	<?php
endwhile;

get_footer();
