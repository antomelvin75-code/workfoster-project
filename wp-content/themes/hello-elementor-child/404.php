<?php
/**
 * Custom 404 page.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$careers_page = get_page_by_path( 'careers' );
$contact_page = get_page_by_path( 'contact-us' );
?>

<main id="content" class="wf-404">
	<div class="wf-wrap">
		<p class="wf-404__code">404</p>

		<h1 style="margin:0 0 12px;font-size:clamp(26px,4vw,38px);">
			<?php esc_html_e( 'This page has moved on to its next project', 'workfoster' ); ?>
		</h1>

		<p style="max-width:52ch;margin:0 auto 30px;font-size:17px;line-height:1.7;">
			<?php esc_html_e( 'The link you followed is broken or the page was retired. Let us point you somewhere useful.', 'workfoster' ); ?>
		</p>

		<div style="max-width:440px;margin:0 auto 28px;">
			<?php get_search_form(); ?>
		</div>

		<div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;">
			<a class="wf-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'workfoster' ); ?></a>

			<?php if ( $careers_page ) : ?>
				<a class="wf-btn wf-btn--ghost" href="<?php echo esc_url( get_permalink( $careers_page ) ); ?>">
					<?php esc_html_e( 'Browse opportunities', 'workfoster' ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $contact_page ) : ?>
				<a class="wf-btn wf-btn--ghost" href="<?php echo esc_url( get_permalink( $contact_page ) ); ?>">
					<?php esc_html_e( 'Contact us', 'workfoster' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php
get_footer();
