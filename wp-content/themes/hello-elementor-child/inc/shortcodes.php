<?php
/**
 * Shortcodes used by the Elementor pages.
 *
 * Job listings themselves are rendered by WP Job Manager's [jobs] shortcode.
 * These cover the pieces around it — category tiles, the hero search bar, the
 * form wrapper that adds the thank-you panel, and the FAQ accordion.
 *
 * @package Workfoster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL of the Careers page (the one holding the [jobs] shortcode).
 *
 * @return string
 */
function workfoster_careers_url() {
	$page_id = (int) get_option( 'job_manager_jobs_page_id' );

	if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
		return get_permalink( $page_id );
	}

	$page = get_page_by_path( 'careers' );

	return $page ? get_permalink( $page ) : home_url( '/careers/' );
}

/**
 * Category tiles.
 *
 * Links carry WP Job Manager's own `search_category` parameter, so the Careers
 * page opens with that filter already applied.
 *
 * [wf_categories limit="6" columns="3"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function workfoster_sc_categories( $atts ) {
	$atts = shortcode_atts(
		array(
			'limit'   => 6,
			'columns' => 3,
		),
		$atts,
		'wf_categories'
	);

	if ( ! taxonomy_exists( 'job_listing_category' ) ) {
		return '';
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'job_listing_category',
			'hide_empty' => false,
			'number'     => (int) $atts['limit'],
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return '';
	}

	$icons = array(
		'data-science'    => 'chart',
		'writing'         => 'send',
		'finance'         => 'wallet',
		'design'          => 'star',
		'web-development' => 'link',
		'marketing'       => 'users',
	);

	$careers = workfoster_careers_url();

	ob_start();
	?>
	<div class="wf-cats" style="display:grid;grid-template-columns:repeat(<?php echo absint( $atts['columns'] ); ?>,minmax(0,1fr));gap:18px;">
		<?php foreach ( $terms as $term ) : ?>
			<?php
			$icon = isset( $icons[ $term->slug ] ) ? $icons[ $term->slug ] : 'briefcase';
			$url  = add_query_arg( 'search_category', rawurlencode( $term->slug ), $careers );
			?>
			<a class="wf-card" href="<?php echo esc_url( $url ); ?>"
				style="display:flex;align-items:center;gap:14px;padding:20px;text-decoration:none;">
				<span style="flex:none;display:grid;place-items:center;width:46px;height:46px;border-radius:12px;background:var(--wf-green-50);color:var(--wf-green-600);">
					<?php WF_Icons::render( $icon, 'wf-cat-icon' ); ?>
				</span>
				<span>
					<strong style="display:block;color:var(--wf-ink);font-size:16px;"><?php echo esc_html( $term->name ); ?></strong>
					<small style="color:var(--wf-muted);">
						<?php
						/* translators: %d: number of open roles. */
						printf( esc_html( _n( '%d open role', '%d open roles', (int) $term->count, 'workfoster' ) ), (int) $term->count );
						?>
					</small>
				</span>
			</a>
		<?php endforeach; ?>
	</div>
	<style>
		.wf-cats .wf-cat-icon{width:22px;height:22px}
		@media (max-width:1024px){.wf-cats{grid-template-columns:repeat(2,minmax(0,1fr))!important}}
		@media (max-width:600px){.wf-cats{grid-template-columns:minmax(0,1fr)!important}}
	</style>
	<?php
	return ob_get_clean();
}
add_shortcode( 'wf_categories', 'workfoster_sc_categories' );

/**
 * Hero search bar.
 *
 * A plain GET form pointed at the Careers page using WP Job Manager's own
 * parameter names, so it works with JavaScript disabled.
 *
 * [wf_career_search label="Search jobs"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function workfoster_sc_career_search( $atts ) {
	$atts = shortcode_atts(
		array(
			'target' => '',
			'label'  => __( 'Search jobs', 'workfoster' ),
		),
		$atts,
		'wf_career_search'
	);

	$target = $atts['target'] ? $atts['target'] : workfoster_careers_url();

	$terms = taxonomy_exists( 'job_listing_category' )
		? get_terms(
			array(
				'taxonomy'   => 'job_listing_category',
				'hide_empty' => false,
			)
		)
		: array();

	ob_start();
	?>
	<form class="wf-hero-search" action="<?php echo esc_url( $target ); ?>" method="get" role="search">
		<div class="wf-hero-search__field">
			<?php WF_Icons::render( 'search' ); ?>
			<label class="screen-reader-text" for="wf-hero-q"><?php esc_html_e( 'Search opportunities', 'workfoster' ); ?></label>
			<input type="search" id="wf-hero-q" name="search_keywords" placeholder="<?php esc_attr_e( 'Job title, skill or company', 'workfoster' ); ?>" />
		</div>

		<?php if ( ! is_wp_error( $terms ) && $terms ) : ?>
			<div class="wf-hero-search__field wf-hero-search__field--select">
				<label class="screen-reader-text" for="wf-hero-cat"><?php esc_html_e( 'Category', 'workfoster' ); ?></label>
				<select id="wf-hero-cat" name="search_category">
					<option value=""><?php esc_html_e( 'All categories', 'workfoster' ); ?></option>
					<?php foreach ( $terms as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		<?php endif; ?>

		<button type="submit" class="wf-btn"><?php echo esc_html( $atts['label'] ); ?></button>
	</form>
	<style>
		.wf-hero-search{display:flex;gap:8px;align-items:center;padding:8px;background:#fff;border:1px solid var(--wf-line);border-radius:var(--wf-radius-pill);box-shadow:var(--wf-shadow)}
		.wf-hero-search__field{position:relative;flex:1 1 auto;min-width:0}
		.wf-hero-search__field svg{position:absolute;left:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:var(--wf-muted)}
		.wf-hero-search input[type=search]{width:100%;border:0;background:transparent;padding:12px 12px 12px 42px;font-size:15px;color:var(--wf-ink)}
		.wf-hero-search input[type=search]:focus{outline:none}
		.wf-hero-search__field--select{flex:0 0 190px;border-left:1px solid var(--wf-line)}
		.wf-hero-search select{width:100%;border:0;background:transparent;padding:12px 14px;font-size:15px;color:var(--wf-ink);appearance:none;cursor:pointer}
		.wf-hero-search select:focus{outline:none}
		.wf-hero-search .wf-btn{flex:none}
		@media (max-width:767px){
			.wf-hero-search{flex-direction:column;align-items:stretch;border-radius:var(--wf-radius-lg);padding:12px;gap:10px}
			.wf-hero-search__field--select{flex:1 1 auto;border-left:0;border-top:1px solid var(--wf-line)}
			.wf-hero-search .wf-btn{width:100%}
		}
	</style>
	<?php
	return ob_get_clean();
}
add_shortcode( 'wf_career_search', 'workfoster_sc_career_search' );

/**
 * Contact Form 7 wrapped with the thank-you panel.
 *
 * [wf_form id="18" title="…" text="…" button="…" button_url="…"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function workfoster_sc_form( $atts ) {
	$atts = shortcode_atts(
		array(
			'id'         => '',
			'title'      => __( 'Application received', 'workfoster' ),
			'text'       => __( 'Thank you for applying through Workfoster. Our talent team reviews every submission within two working days and will email you the next step.', 'workfoster' ),
			'button'     => __( 'Browse more opportunities', 'workfoster' ),
			'button_url' => '',
			'reference'  => 'yes',
		),
		$atts,
		'wf_form'
	);

	if ( ! $atts['id'] || ! shortcode_exists( 'contact-form-7' ) ) {
		return '<p><em>' . esc_html__( 'Form unavailable — check that Contact Form 7 is active.', 'workfoster' ) . '</em></p>';
	}

	$button_url = $atts['button_url'] ? $atts['button_url'] : workfoster_careers_url();

	ob_start();
	?>
	<div class="wf-form-wrap">
		<div class="wf-form-inner wf-form">
			<?php echo do_shortcode( '[contact-form-7 id="' . esc_attr( $atts['id'] ) . '"]' ); ?>
		</div>

		<div class="wf-thanks" role="status" aria-live="polite">
			<div class="wf-thanks__icon"><?php WF_Icons::render( 'check' ); ?></div>
			<h3><?php echo esc_html( $atts['title'] ); ?></h3>
			<p><?php echo esc_html( $atts['text'] ); ?></p>

			<?php if ( 'yes' === $atts['reference'] ) : ?>
				<span class="wf-thanks__ref">
					<?php esc_html_e( 'Your reference:', 'workfoster' ); ?>
					<strong data-wf-ref>&mdash;</strong>
				</span>
			<?php endif; ?>

			<div class="wf-thanks__actions">
				<a class="wf-btn" href="<?php echo esc_url( $button_url ); ?>"><?php echo esc_html( $atts['button'] ); ?></a>
				<a class="wf-btn wf-btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'workfoster' ); ?></a>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'wf_form', 'workfoster_sc_form' );

/**
 * FAQ accordion built from `Question :: Answer` pairs, one per line.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Enclosed content.
 * @return string
 */
function workfoster_sc_faq( $atts, $content = '' ) {
	$content = wp_strip_all_tags( (string) $content );
	$rows    = array_filter( array_map( 'trim', explode( "\n", $content ) ) );

	if ( ! $rows ) {
		return '';
	}

	ob_start();
	echo '<div class="wf-faq">';

	$index = 0;

	foreach ( $rows as $row ) {
		$parts = array_map( 'trim', explode( '::', $row, 2 ) );

		if ( count( $parts ) < 2 ) {
			continue;
		}

		printf(
			'<details class="wf-faq__item"%1$s><summary class="wf-faq__q">%2$s</summary><div class="wf-faq__a"><p>%3$s</p></div></details>',
			0 === $index ? ' open' : '',
			esc_html( $parts[0] ),
			esc_html( $parts[1] )
		);

		$index++;
	}

	echo '</div>';

	return ob_get_clean();
}
add_shortcode( 'wf_faq', 'workfoster_sc_faq' );

/**
 * Current year, for the footer copyright.
 *
 * @return string
 */
function workfoster_sc_year() {
	return esc_html( wp_date( 'Y' ) );
}
add_shortcode( 'wf_year', 'workfoster_sc_year' );
