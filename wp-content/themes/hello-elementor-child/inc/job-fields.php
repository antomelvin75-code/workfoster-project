<?php
/**
 * Extra job listing fields.
 *
 * WP Job Manager covers company, location, type, salary, featured and expiry
 * natively. These four have no native equivalent, so they are added to WPJM's
 * own meta box through its documented filter rather than with a separate
 * custom-fields plugin.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the additional fields on the Job Listing edit screen.
 *
 * @param array $fields Existing WPJM fields.
 * @return array
 */
function workfoster_job_listing_fields( $fields ) {
	$fields['_job_experience'] = array(
		'label'       => __( 'Experience required', 'workfoster' ),
		'type'        => 'text',
		'placeholder' => '3–5 years',
		'priority'    => 12,
	);

	$fields['_job_duration'] = array(
		'label'       => __( 'Engagement duration', 'workfoster' ),
		'type'        => 'text',
		'placeholder' => '4 months',
		'priority'    => 13,
	);

	$fields['_job_openings'] = array(
		'label'       => __( 'Openings', 'workfoster' ),
		'type'        => 'text',
		'placeholder' => '2',
		'priority'    => 14,
	);

	$fields['_job_skills'] = array(
		'label'       => __( 'Skills', 'workfoster' ),
		'type'        => 'text',
		'placeholder' => 'Python, Pandas, SQL',
		'description' => __( 'Comma separated. Shown as tags on the card and the detail page.', 'workfoster' ),
		'priority'    => 15,
	);

	$fields['_job_rate_suffix'] = array(
		'label'       => __( 'Rate suffix (display)', 'workfoster' ),
		'type'        => 'text',
		'placeholder' => '/ hour',
		'description' => __( 'Only needed for units WP Job Manager does not offer, such as "/ article".', 'workfoster' ),
		'priority'    => 16,
	);

	return $fields;
}
add_filter( 'job_manager_job_listing_data_fields', 'workfoster_job_listing_fields' );

/**
 * Read a job meta value.
 *
 * @param string $key     Meta key without the leading underscore.
 * @param int    $post_id Optional post ID.
 * @param string $default Fallback.
 * @return string
 */
function workfoster_job_meta( $key, $post_id = 0, $default = '' ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$value   = get_post_meta( $post_id, '_' . $key, true );

	return ( '' !== $value && null !== $value ) ? $value : $default;
}

/**
 * Skills as a clean array.
 *
 * @param int $post_id Optional post ID.
 * @return string[]
 */
function workfoster_job_skills( $post_id = 0 ) {
	$raw = workfoster_job_meta( 'job_skills', $post_id );

	if ( '' === $raw ) {
		return array();
	}

	return array_values( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) );
}

/**
 * Rate plus its unit, as a display string.
 *
 * Prefers WP Job Manager's own salary unit; falls back to the free-text suffix
 * for units WPJM does not offer (for example "/ article").
 *
 * @param int $post_id Optional post ID.
 * @return string
 */
function workfoster_job_rate( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$salary  = workfoster_job_meta( 'job_salary', $post_id );

	if ( ! $salary ) {
		return '';
	}

	$unit   = workfoster_job_meta( 'job_salary_unit', $post_id );
	$suffix = workfoster_job_meta( 'job_rate_suffix', $post_id );

	if ( $unit ) {
		$labels = array(
			'YEAR'  => __( '/ year', 'workfoster' ),
			'MONTH' => __( '/ month', 'workfoster' ),
			'WEEK'  => __( '/ week', 'workfoster' ),
			'DAY'   => __( '/ day', 'workfoster' ),
			'HOUR'  => __( '/ hour', 'workfoster' ),
		);

		$suffix = isset( $labels[ $unit ] ) ? $labels[ $unit ] : $suffix;
	}

	return trim( $salary . ' ' . $suffix );
}

/**
 * Two-letter monogram for a company, used in place of a client logo.
 *
 * @param string $name Company name.
 * @return string
 */
function workfoster_company_monogram( $name ) {
	$words = preg_split( '/[\s&\-]+/', trim( wp_strip_all_tags( (string) $name ) ), -1, PREG_SPLIT_NO_EMPTY );

	if ( ! $words ) {
		return 'WF';
	}

	if ( 1 === count( $words ) ) {
		return strtoupper( mb_substr( $words[0], 0, 2 ) );
	}

	return strtoupper( mb_substr( $words[0], 0, 1 ) . mb_substr( $words[1], 0, 1 ) );
}

/**
 * The primary category term for a listing.
 *
 * @param int $post_id Optional post ID.
 * @return WP_Term|null
 */
function workfoster_job_category( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$terms   = get_the_terms( $post_id, 'job_listing_category' );

	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

/**
 * The job type term for a listing.
 *
 * @param int $post_id Optional post ID.
 * @return WP_Term|null
 */
function workfoster_job_type( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$terms   = get_the_terms( $post_id, 'job_listing_type' );

	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}
