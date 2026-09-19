<?php
/**
 * Single job listing body.
 *
 * Overrides wp-job-manager/templates/content-single-job_listing.php.
 * Renders the description, the skill tags and the application form; the page
 * shell around it lives in single-job_listing.php.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

if ( post_password_required( $post ) && ! is_super_admin() ) {
	get_job_manager_template_part( 'access-denied', 'single-job_listing' );
	return;
}

$wf_job_id      = $post->ID;
$wf_skills      = workfoster_job_skills( $wf_job_id );
$wf_form_id     = (int) get_option( 'wf_application_form_id', 0 );
$wf_careers_url = workfoster_careers_url();
?>

<div class="single_job_listing">

	<?php if ( 'expired' === $post->post_status ) : ?>

		<div class="wf-callout">
			<strong><?php esc_html_e( 'This listing has closed', 'workfoster' ); ?></strong>
			<p><?php esc_html_e( 'Applications are no longer being accepted for this role.', 'workfoster' ); ?></p>
			<a class="wf-btn wf-btn--sm" href="<?php echo esc_url( $wf_careers_url ); ?>">
				<?php esc_html_e( 'Browse open roles', 'workfoster' ); ?>
			</a>
		</div>

	<?php else : ?>

		<div class="job_description">
			<?php wpjm_the_job_description(); ?>
		</div>

		<?php if ( $wf_skills ) : ?>
			<h2><?php esc_html_e( 'Skills &amp; tools', 'workfoster' ); ?></h2>
			<div class="wf-job__skills" style="margin-bottom:8px;">
				<?php foreach ( $wf_skills as $wf_skill ) : ?>
					<span class="wf-tag"><?php echo esc_html( $wf_skill ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<h2 id="apply"><?php esc_html_e( 'Apply for this role', 'workfoster' ); ?></h2>
		<p><?php esc_html_e( 'Tell us a little about your experience and share your portfolio. Applications are reviewed by a real person, and every applicant hears back.', 'workfoster' ); ?></p>

		<div class="wf-panel" style="margin-top:22px;">
			<?php
			if ( $wf_form_id ) {
				echo do_shortcode(
					sprintf(
						'[wf_form id="%d" title="%s" text="%s" button="%s" button_url="%s"]',
						$wf_form_id,
						esc_attr__( 'Application received', 'workfoster' ),
						esc_attr__( 'Thanks for applying through Workfoster. Our talent team reviews every submission within two working days and will email you the next step. Keep an eye on your inbox — and your spam folder, just in case.', 'workfoster' ),
						esc_attr__( 'Browse more opportunities', 'workfoster' ),
						esc_url( $wf_careers_url )
					)
				);
			} else {
				echo '<p><em>' . esc_html__( 'The application form has not been configured yet.', 'workfoster' ) . '</em></p>';
			}
			?>
		</div>

	<?php endif; ?>
</div>
