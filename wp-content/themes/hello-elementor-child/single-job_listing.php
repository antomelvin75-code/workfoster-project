<?php
/**
 * Single job listing — the Career Details page.
 *
 * Provides the page shell (banner, two-column layout, sidebar, related roles).
 * The middle column comes from `the_content()`, which WP Job Manager filters
 * through `job_manager/content-single-job_listing.php` — overridden in this
 * theme to render the description, skills and application form.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$job_id = get_the_ID();

	$company  = workfoster_job_meta( 'company_name', $job_id, __( 'Workfoster Client', 'workfoster' ) );
	$location = workfoster_job_meta( 'job_location', $job_id );
	$rate     = workfoster_job_rate( $job_id );
	$expires  = workfoster_job_meta( 'job_expires', $job_id );
	$category = workfoster_job_category( $job_id );
	$type     = workfoster_job_type( $job_id );

	$careers_url = workfoster_careers_url();
	?>

	<main id="content" class="wf-single">

		<section class="wf-single__hero">
			<div class="wf-wrap">
				<nav class="wf-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'workfoster' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'workfoster' ); ?></a>
					<span>/</span>
					<a href="<?php echo esc_url( $careers_url ); ?>"><?php esc_html_e( 'Careers', 'workfoster' ); ?></a>
					<span>/</span>
					<?php echo esc_html( get_the_title() ); ?>
				</nav>

				<?php if ( $category ) : ?>
					<p style="margin:0 0 14px;"><span class="wf-eyebrow"><?php echo esc_html( $category->name ); ?></span></p>
				<?php endif; ?>

				<h1 class="wf-single__title"><?php the_title(); ?></h1>

				<div class="wf-single__sub">
					<span><?php WF_Icons::render( 'briefcase' ); ?><?php echo esc_html( $company ); ?></span>
					<?php if ( $location ) : ?>
						<span><?php WF_Icons::render( 'map-pin' ); ?><?php echo esc_html( $location ); ?></span>
					<?php endif; ?>
					<?php if ( $type ) : ?>
						<span><?php WF_Icons::render( 'clock' ); ?><?php echo esc_html( $type->name ); ?></span>
					<?php endif; ?>
					<?php if ( $rate ) : ?>
						<span><?php WF_Icons::render( 'wallet' ); ?><?php echo esc_html( $rate ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</section>

		<div class="wf-wrap">
			<div class="wf-single__layout">

				<div class="wf-single__content">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure style="margin:0 0 32px;">
							<?php the_post_thumbnail( 'large', array( 'style' => 'border-radius:22px;width:100%;height:auto;' ) ); ?>
						</figure>
					<?php endif; ?>

					<?php the_content(); ?>
				</div>

				<aside class="wf-aside">
					<div class="wf-panel">
						<h3><?php esc_html_e( 'Role snapshot', 'workfoster' ); ?></h3>
						<dl class="wf-facts">
							<?php $experience = workfoster_job_meta( 'job_experience', $job_id ); ?>
							<?php if ( $experience ) : ?>
								<div><dt><?php esc_html_e( 'Experience', 'workfoster' ); ?></dt><dd><?php echo esc_html( $experience ); ?></dd></div>
							<?php endif; ?>

							<?php $duration = workfoster_job_meta( 'job_duration', $job_id ); ?>
							<?php if ( $duration ) : ?>
								<div><dt><?php esc_html_e( 'Duration', 'workfoster' ); ?></dt><dd><?php echo esc_html( $duration ); ?></dd></div>
							<?php endif; ?>

							<?php if ( $rate ) : ?>
								<div><dt><?php esc_html_e( 'Budget', 'workfoster' ); ?></dt><dd><?php echo esc_html( $rate ); ?></dd></div>
							<?php endif; ?>

							<?php $openings = workfoster_job_meta( 'job_openings', $job_id ); ?>
							<?php if ( $openings ) : ?>
								<div><dt><?php esc_html_e( 'Openings', 'workfoster' ); ?></dt><dd><?php echo esc_html( $openings ); ?></dd></div>
							<?php endif; ?>

							<?php if ( $expires ) : ?>
								<div>
									<dt><?php esc_html_e( 'Apply before', 'workfoster' ); ?></dt>
									<dd><?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $expires ) ) ); ?></dd>
								</div>
							<?php endif; ?>

							<div>
								<dt><?php esc_html_e( 'Posted', 'workfoster' ); ?></dt>
								<dd><?php echo esc_html( get_the_date() ); ?></dd>
							</div>
						</dl>
					</div>

					<div class="wf-panel wf-panel--cta">
						<h3><?php esc_html_e( 'Ready to apply?', 'workfoster' ); ?></h3>
						<p><?php esc_html_e( 'It takes about three minutes. No account required.', 'workfoster' ); ?></p>
						<a class="wf-btn" href="#apply"><?php esc_html_e( 'Apply now', 'workfoster' ); ?></a>
					</div>

					<div class="wf-panel">
						<h3><?php esc_html_e( 'Share this role', 'workfoster' ); ?></h3>
						<?php
						$permalink = rawurlencode( get_permalink() );
						$title     = rawurlencode( get_the_title() );
						?>
						<div class="wf-share">
							<a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo esc_attr( $permalink ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'workfoster' ); ?>"><?php WF_Icons::render( 'linkedin' ); ?></a>
							<a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $permalink ); ?>&amp;text=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on X', 'workfoster' ); ?>"><?php WF_Icons::render( 'twitter' ); ?></a>
							<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $permalink ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on Facebook', 'workfoster' ); ?>"><?php WF_Icons::render( 'facebook' ); ?></a>
							<a href="mailto:?subject=<?php echo esc_attr( $title ); ?>&amp;body=<?php echo esc_attr( $permalink ); ?>" aria-label="<?php esc_attr_e( 'Share by email', 'workfoster' ); ?>"><?php WF_Icons::render( 'mail' ); ?></a>
						</div>
					</div>
				</aside>
			</div>

			<?php
			$related_args = array(
				'post_type'           => 'job_listing',
				'post_status'         => 'publish',
				'posts_per_page'      => 3,
				'post__not_in'        => array( $job_id ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			);

			if ( $category ) {
				$related_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'job_listing_category',
						'field'    => 'term_id',
						'terms'    => array( $category->term_id ),
					),
				);
			}

			$related = new WP_Query( $related_args );

			if ( $related->have_posts() ) :
				?>
				<section style="margin-top:72px;">
					<h2 style="font-size:28px;margin:0 0 6px;"><?php esc_html_e( 'Similar opportunities', 'workfoster' ); ?></h2>
					<p style="margin:0 0 26px;color:var(--wf-muted);"><?php esc_html_e( 'Other roles our clients are hiring for right now.', 'workfoster' ); ?></p>

					<ul class="job_listings wf-careers__grid">
						<?php
						while ( $related->have_posts() ) :
							$related->the_post();
							get_job_manager_template_part( 'content', 'job_listing' );
						endwhile;
						wp_reset_postdata();
						?>
					</ul>
				</section>
			<?php endif; ?>
		</div>
	</main>

	<?php
endwhile;

get_footer();
