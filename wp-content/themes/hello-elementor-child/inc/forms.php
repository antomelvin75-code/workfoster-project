<?php
/**
 * Contact Form 7 integration.
 *
 * Carries the job context into the application email so the applicant does not
 * have to retype which role they applied for.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the current listing to every form rendered on a job listing page.
 *
 * @param array $fields Existing hidden fields.
 * @return array
 */
function workfoster_cf7_hidden_fields( $fields ) {
	$post_id = get_the_ID();

	$fields['wf-source-url'] = is_singular() ? get_permalink( $post_id ) : home_url( add_query_arg( array() ) );

	if ( is_singular( 'job_listing' ) ) {
		$fields['wf-career-id']    = (string) $post_id;
		$fields['wf-career-title'] = get_the_title( $post_id );
	}

	return $fields;
}
add_filter( 'wpcf7_form_hidden_fields', 'workfoster_cf7_hidden_fields' );

/**
 * Make [wf_career_title], [wf_career_url] and [wf_source_url] work in the mail
 * template.
 *
 * @param string $output Current output.
 * @param string $name   Tag name.
 * @param bool   $html   Whether the mail is HTML.
 * @return string
 */
function workfoster_cf7_mail_tags( $output, $name, $html ) {
	unset( $html );

	$submission = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;

	if ( ! $submission ) {
		return $output;
	}

	$posted = $submission->get_posted_data();

	switch ( $name ) {
		case 'wf_career_title':
			return isset( $posted['wf-career-title'] )
				? sanitize_text_field( $posted['wf-career-title'] )
				: __( 'General application', 'workfoster' );

		case 'wf_career_url':
			$career_id = isset( $posted['wf-career-id'] ) ? absint( $posted['wf-career-id'] ) : 0;

			return $career_id ? get_permalink( $career_id ) : '';

		case 'wf_source_url':
			return isset( $posted['wf-source-url'] ) ? esc_url_raw( $posted['wf-source-url'] ) : '';
	}

	return $output;
}
add_filter( 'wpcf7_special_mail_tags', 'workfoster_cf7_mail_tags', 10, 3 );

/**
 * Keep a lightweight application count per listing.
 *
 * Full applicant tracking belongs in a CRM or a dedicated plugin; this is just
 * enough for the admin list to show which roles are getting traction.
 *
 * @param WPCF7_ContactForm $form Submitted form.
 */
function workfoster_cf7_log_application( $form ) {
	unset( $form );

	$submission = class_exists( 'WPCF7_Submission' ) ? WPCF7_Submission::get_instance() : null;

	if ( ! $submission ) {
		return;
	}

	$posted    = $submission->get_posted_data();
	$career_id = isset( $posted['wf-career-id'] ) ? absint( $posted['wf-career-id'] ) : 0;

	if ( ! $career_id || 'job_listing' !== get_post_type( $career_id ) ) {
		return;
	}

	$count = (int) get_post_meta( $career_id, '_job_application_count', true );

	update_post_meta( $career_id, '_job_application_count', $count + 1 );
}
add_action( 'wpcf7_mail_sent', 'workfoster_cf7_log_application' );

/**
 * Contact Form 7 wraps every field in a <p> by default, which fights the grid
 * layout used in the form markup.
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );
