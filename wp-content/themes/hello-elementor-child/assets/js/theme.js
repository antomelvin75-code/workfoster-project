/**
 * Workfoster — front-end behaviour.
 *
 * Deliberately dependency-free: Elementor already ships jQuery, but nothing
 * here needs it and keeping it vanilla means the file can be deferred safely.
 */
( function () {
	'use strict';

	/**
	 * Swap a Contact Form 7 form for the thank-you panel that sits next to it.
	 *
	 * Markup contract (see the shortcodes in workfoster-core):
	 *   .wf-form-wrap
	 *     └ .wf-form-inner   → holds the CF7 shortcode output
	 *     └ .wf-thanks       → hidden until `wpcf7mailsent` fires
	 */
	function initFormThankYou() {
		document.addEventListener( 'wpcf7mailsent', function ( event ) {
			var wrap = event.target.closest( '.wf-form-wrap' );

			if ( ! wrap ) {
				return;
			}

			var panel = wrap.querySelector( '.wf-thanks' );

			if ( ! panel ) {
				return;
			}

			// Give applicants something to quote when they follow up.
			var refField = panel.querySelector( '[data-wf-ref]' );

			if ( refField ) {
				refField.textContent = buildReference( event.detail && event.detail.contactFormId );
			}

			wrap.classList.add( 'is-sent' );
			panel.classList.add( 'is-visible' );
			panel.setAttribute( 'tabindex', '-1' );
			panel.focus( { preventScroll: true } );

			panel.scrollIntoView( { behavior: 'smooth', block: 'center' } );
		}, false );
	}

	/**
	 * Human-readable reference: WF-<yymmdd>-<formId><random>.
	 *
	 * @param {number|string} formId Contact Form 7 post ID.
	 * @return {string}
	 */
	function buildReference( formId ) {
		var now = new Date();
		var stamp = [
			String( now.getFullYear() ).slice( -2 ),
			String( now.getMonth() + 1 ).padStart( 2, '0' ),
			String( now.getDate() ).padStart( 2, '0' )
		].join( '' );

		var tail = String( Math.floor( Math.random() * 9000 ) + 1000 );

		return 'WF-' + stamp + '-' + ( formId || '0' ) + tail;
	}

	/**
	 * Only one FAQ answer open at a time, per group.
	 */
	function initFaqGroups() {
		document.querySelectorAll( '.wf-faq' ).forEach( function ( group ) {
			var items = Array.prototype.slice.call( group.querySelectorAll( '.wf-faq__item' ) );

			items.forEach( function ( item ) {
				item.addEventListener( 'toggle', function () {
					if ( ! item.open ) {
						return;
					}

					items.forEach( function ( other ) {
						if ( other !== item ) {
							other.open = false;
						}
					} );
				} );
			} );
		} );
	}

	/**
	 * Count-up for the stat strip, fired once when it scrolls into view.
	 */
	function initCounters() {
		var targets = document.querySelectorAll( '[data-wf-countup]' );

		if ( ! targets.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		var observer = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) {
					return;
				}

				countUp( entry.target );
				observer.unobserve( entry.target );
			} );
		}, { threshold: .4 } );

		targets.forEach( function ( target ) {
			observer.observe( target );
		} );
	}

	function countUp( el ) {
		var end = parseFloat( el.dataset.wfCountup );
		var suffix = el.dataset.wfSuffix || '';
		var decimals = ( el.dataset.wfDecimals ? parseInt( el.dataset.wfDecimals, 10 ) : 0 );
		var duration = 1200;
		var started = null;

		if ( isNaN( end ) ) {
			return;
		}

		function frame( now ) {
			if ( null === started ) {
				started = now;
			}

			var progress = Math.min( ( now - started ) / duration, 1 );
			// easeOutCubic
			var eased = 1 - Math.pow( 1 - progress, 3 );

			el.textContent = ( end * eased ).toFixed( decimals ) + suffix;

			if ( progress < 1 ) {
				window.requestAnimationFrame( frame );
			}
		}

		window.requestAnimationFrame( frame );
	}

	/*
	 * Note: job search and filtering are handled server-side by WP Job Manager
	 * (its own AJAX form), and the hero search is a plain GET form pointed at
	 * the Careers page — so neither needs anything here.
	 */
	function boot() {
		initFormThankYou();
		initFaqGroups();
		initCounters();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
