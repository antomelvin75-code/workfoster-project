<?php
/**
 * Job listing card.
 *
 * Overrides wp-job-manager/templates/content-job_listing.php. WP Job Manager
 * renders listings as <li> inside <ul class="job_listings">, so the outer
 * element is kept and the card design lives inside it.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$wf_id       = get_the_ID();
$wf_company  = workfoster_job_meta( 'company_name', $wf_id, __( 'Workfoster Client', 'workfoster' ) );
$wf_location = workfoster_job_meta( 'job_location', $wf_id );
$wf_rate     = workfoster_job_rate( $wf_id );
$wf_skills   = workfoster_job_skills( $wf_id );
$wf_type     = workfoster_job_type( $wf_id );
$wf_featured = get_post_meta( $wf_id, '_featured', true );
?>
<li <?php job_listing_class( 'wf-job-item' ); ?>>
	<article class="wf-job<?php echo $wf_featured ? ' wf-job--featured' : ''; ?>">

		<header class="wf-job__head">
			<span class="wf-job__logo" aria-hidden="true"><?php echo esc_html( workfoster_company_monogram( $wf_company ) ); ?></span>
			<div>
				<h3 class="wf-job__title">
					<a href="<?php echo esc_url( get_permalink( $wf_id ) ); ?>"><?php the_title(); ?></a>
				</h3>
				<p class="wf-job__company"><?php echo esc_html( $wf_company ); ?></p>
			</div>
			<?php if ( $wf_featured ) : ?>
				<span class="wf-job__badge"><?php esc_html_e( 'Featured', 'workfoster' ); ?></span>
			<?php endif; ?>
		</header>

		<div class="wf-job__meta">
			<?php if ( $wf_location ) : ?>
				<span><?php WF_Icons::render( 'map-pin' ); ?><?php echo esc_html( $wf_location ); ?></span>
			<?php endif; ?>
			<?php if ( $wf_type ) : ?>
				<span><?php WF_Icons::render( 'briefcase' ); ?><?php echo esc_html( $wf_type->name ); ?></span>
			<?php endif; ?>
		</div>

		<p class="wf-job__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>

		<?php if ( $wf_skills ) : ?>
			<div class="wf-job__skills">
				<?php foreach ( array_slice( $wf_skills, 0, 4 ) as $wf_skill ) : ?>
					<span class="wf-tag"><?php echo esc_html( $wf_skill ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<footer class="wf-job__foot">
			<?php if ( $wf_rate ) : ?>
				<span class="wf-job__rate"><?php echo esc_html( $wf_rate ); ?></span>
			<?php else : ?>
				<span class="wf-job__rate"><small><?php esc_html_e( 'Budget on request', 'workfoster' ); ?></small></span>
			<?php endif; ?>

			<a class="wf-btn wf-btn--sm" href="<?php echo esc_url( get_permalink( $wf_id ) ); ?>">
				<?php esc_html_e( 'View &amp; Apply', 'workfoster' ); ?>
			</a>
		</footer>
	</article>
</li>
