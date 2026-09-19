<?php
/**
 * Plugin Name: Workfoster — Local Mail Catcher
 * Description: Local-only safety net. XAMPP has no MTA, so wp_mail() would fail and Contact Form 7 would report a send error instead of showing the thank-you panel. This intercepts outgoing mail, writes it to wp-content/uploads/wf-mail-log/, and reports success. It is a no-op on any environment other than `local`.
 * Version:     1.0.0
 * Author:      Workfoster Web Team
 * License:     GPL-2.0-or-later
 *
 * @package Workfoster\Dev
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Short-circuit wp_mail() and write the message to disk.
 *
 * `pre_wp_mail` returning a non-null value stops WordPress from ever reaching
 * PHPMailer, which is exactly what we want with no MTA installed.
 *
 * @param null|bool $short_circuit Null by default.
 * @param array     $atts          to/subject/message/headers/attachments.
 * @return null|bool
 */
function wf_local_mail_catcher( $short_circuit, $atts ) {
	if ( 'local' !== wp_get_environment_type() ) {
		return $short_circuit;
	}

	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'wf-mail-log';

	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );

		// Keep the log out of the web root's reach.
		file_put_contents( $dir . '/.htaccess', "Require all denied\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		file_put_contents( $dir . '/index.php', "<?php // Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}

	$to = is_array( $atts['to'] ) ? implode( ', ', $atts['to'] ) : $atts['to'];

	$headers = $atts['headers'];
	if ( is_array( $headers ) ) {
		$headers = implode( "\n", $headers );
	}

	$record = sprintf(
		"=== %s ===\nTo: %s\nSubject: %s\nHeaders:\n%s\n\n%s\n\n",
		wp_date( 'Y-m-d H:i:s' ),
		$to,
		$atts['subject'],
		$headers,
		$atts['message']
	);

	file_put_contents( // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		$dir . '/mail-' . wp_date( 'Y-m-d' ) . '.log',
		$record,
		FILE_APPEND
	);

	return true;
}
add_filter( 'pre_wp_mail', 'wf_local_mail_catcher', 10, 2 );

/**
 * Make it obvious in wp-admin that mail is being swallowed, so nobody spends an
 * afternoon wondering why the inbox is empty.
 */
function wf_local_mail_notice() {
	if ( 'local' !== wp_get_environment_type() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'toplevel_page_wpcf7' ), true ) ) {
		return;
	}

	$uploads = wp_upload_dir();

	printf(
		'<div class="notice notice-info"><p><strong>%s</strong> %s <code>%s</code></p></div>',
		esc_html__( 'Local environment:', 'workfoster' ),
		esc_html__( 'outgoing email is captured to disk instead of being sent. Form submissions still succeed. Log location:', 'workfoster' ),
		esc_html( trailingslashit( $uploads['basedir'] ) . 'wf-mail-log/' )
	);
}
add_action( 'admin_notices', 'wf_local_mail_notice' );
