/**
 * Homepage layout enhancements for Eglatone.
 *
 * 1. Turns the Service and Featured Content sections into compact
 *    2-up Owl carousels.
 * 2. Powers the "... more" button that AJAX-loads the next batch of
 *    posts into the blog grid.
 *
 * @package Eglatone
 */

( function( $ ) {
	'use strict';

	var settings = window.eglatoneLayout || {};

	/**
	 * 2-up section carousels.
	 */
	function initSectionCarousels() {
		if ( typeof $.fn.owlCarousel !== 'function' ) {
			// Owl is not on the page - make sure the content stays visible.
			$( '.eglatone-2up-carousel' ).removeClass( 'owl-carousel' ).addClass( 'no-carousel-fallback' );
			return;
		}

		$( '.eglatone-2up-carousel' ).each( function() {
			var $carousel = $( this ),
				count     = $carousel.children().length,
				columns   = parseInt( $carousel.attr( 'data-columns' ), 10 ),
				perView,
				midView;

			if ( ! count || $carousel.hasClass( 'owl-loaded' ) ) {
				return;
			}

			if ( ! columns || columns < 1 || columns > 4 ) {
				columns = 3;
			}

			// Never show more slots than there are cards - avoids a half-empty row.
			perView = Math.min( columns, count );
			midView = Math.min( 2, perView );

			$carousel.addClass( 'columns-' + perView );

			$carousel.owlCarousel( {
				items: perView,
				margin: 30,
				nav: count > perView,
				dots: false, // Arrows only - set to "count > perView" to bring dots back.
				loop: count > perView,
				autoplay: false,
				autoHeight: false,
				rtl: !! settings.rtl,
				navText: [
					'<span class="screen-reader-text">Previous</span>',
					'<span class="screen-reader-text">Next</span>'
				],
				responsive: {
					0: {
						items: 1,
						margin: 20
					},
					600: {
						items: midView,
						margin: 24
					},
					992: {
						items: perView
					}
				}
			} );
		} );
	}

	/**
	 * "... more" loader for the blog grid.
	 */
	function initLoadMore() {
		var $button = $( '.eglatone-load-more-button' );

		if ( ! $button.length || ! settings.ajaxUrl ) {
			return;
		}

		$button.on( 'click', function( event ) {
			event.preventDefault();

			var $btn    = $( this ),
				$wrap   = $( $btn.data( 'target' ) || '#infinite-post-wrap' ),
				$holder = $btn.closest( '.eglatone-load-more' ),
				page    = parseInt( $btn.attr( 'data-page' ), 10 ) || 1;

			if ( $btn.hasClass( 'is-busy' ) || ! $wrap.length ) {
				return;
			}

			$btn.addClass( 'is-busy' ).prop( 'disabled', true );
			$holder.find( '.eglatone-load-more-finished' ).text( '' );

			$.post( settings.ajaxUrl, {
				action: 'eglatone_load_more',
				nonce: settings.nonce,
				page: page
			} )
				.done( function( response ) {
					if ( ! response || ! response.success || ! response.data || ! response.data.html ) {
						$btn.remove();
						$holder.find( '.eglatone-load-more-finished' ).text( settings.doneText || '' );
						return;
					}

					var $new = $( response.data.html );

					$wrap.append( $new );

					$btn.attr( 'data-page', response.data.page );

					if ( ! response.data.has_more ) {
						$btn.remove();
						$holder.find( '.eglatone-load-more-finished' ).text( settings.doneText || '' );
					}

					// Let other scripts know new posts are in the DOM.
					$( document.body ).trigger( 'eglatone-posts-loaded', [ $new ] );
				} )
				.fail( function() {
					$holder.find( '.eglatone-load-more-finished' ).text( settings.errorText || '' );
				} )
				.always( function() {
					$btn.removeClass( 'is-busy' ).prop( 'disabled', false );
				} );
		} );
	}

	/**
	 * TECHED.TV ticker.
	 *
	 * Duplicates the item list until it comfortably overflows the viewport, then
	 * CSS animates the track by exactly -50%, so the seam is invisible and the
	 * loop is seamless regardless of how many items there are.
	 */
	function initTicker() {
		var $ticker = $( '#eglatone-ticker-section' );

		if ( ! $ticker.length ) {
			return;
		}

		var $viewport = $ticker.find( '.ticker-viewport' ),
			$track    = $ticker.find( '.ticker-track' ),
			speed     = parseInt( $ticker.attr( 'data-speed' ), 10 ) || 45,
			reduced   = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		if ( ! $track.length || ! $track.children().length ) {
			return;
		}

		if ( reduced ) {
			// No animation - leave it as a plain horizontally scrollable strip.
			$ticker.addClass( 'ticker-static' );
			return;
		}

		function build() {
			// Reset to the original set before measuring again.
			$track.find( '.ticker-clone' ).remove();

			var $originals   = $track.children(),
				originalWide = 0,
				viewportWide = $viewport.outerWidth(),
				guard        = 0;

			$originals.each( function() {
				originalWide += $( this ).outerWidth( true );
			} );

			if ( ! originalWide || ! viewportWide ) {
				return;
			}

			/*
			 * Repeat the originals until one copy of the set is at least as wide
			 * as the viewport, so there is never a visible gap mid-scroll.
			 */
			var copyWide = originalWide;

			while ( copyWide < viewportWide && guard < 20 ) {
				$originals.clone().addClass( 'ticker-clone' ).appendTo( $track );
				copyWide += originalWide;
				guard++;
			}

			// One more full duplicate: the track animates from 0 to -50%.
			$track.children().clone().addClass( 'ticker-clone' ).appendTo( $track );

			$track.css( {
				'animation-duration': ( speed * Math.max( 1, copyWide / viewportWide ) ).toFixed( 2 ) + 's'
			} );

			$ticker.addClass( 'ticker-ready' );
		}

		build();

		var resizeTimer = null;

		$( window ).on( 'resize', function() {
			clearTimeout( resizeTimer );
			resizeTimer = setTimeout( function() {
				$ticker.removeClass( 'ticker-ready' );
				build();
			}, 250 );
		} );
	}

	/**
	 * Single posts: lift the audio player and its Play / Download / Embed links
	 * out of the content and into the media frame under the artwork, so the
	 * image and the episode read as one unit.
	 *
	 * Done in JS rather than PHP on purpose: the player markup belongs to the
	 * podcast plugin, and moving nodes survives plugin updates that would break
	 * any regex applied to the_content().
	 */
	function initSingleMedia() {
		var $frame = $( '.eglatone-single-frame' );

		if ( ! $frame.length ) {
			return;
		}

		var $slot    = $frame.find( '.eglatone-single-player' ),
			$content = $( '.entry-content' ).first();

		if ( ! $slot.length || ! $content.length ) {
			return;
		}

		// Common podcast plugin containers, then a bare <audio> as a fallback.
		var selectors = [
			'.powerpress_player',
			'.powerpress_links',
			'.ssp-player',
			'.podcast_player',
			'.wp-block-audio',
			'.wp-audio-shortcode'
		];

		var $found = $content.find( selectors.join( ',' ) );

		if ( ! $found.length ) {
			$found = $content.children( 'audio' ).first();
		}

		if ( ! $found.length ) {
			// Nothing to move - drop the empty slot so it leaves no gap.
			$slot.remove();
			return;
		}

		$found.appendTo( $slot );
		$frame.addClass( 'has-player' );
	}

	/**
	 * Achievements hero: collapse the tail of the introduction behind a
	 * "Read more" button.
	 *
	 * The collapse is applied by JS, not by CSS alone, so that with JS disabled
	 * the whole introduction stays readable rather than being permanently cut.
	 */
	function initHero() {
		var $hero = $( '#eglatone-hero-section' );

		if ( ! $hero.length ) {
			return;
		}

		var $more   = $hero.find( '.hero-text-more' ),
			$toggle = $hero.find( '.hero-more-toggle' );

		if ( ! $more.length || ! $toggle.length ) {
			return;
		}

		$hero.addClass( 'is-collapsible' );

		$toggle.on( 'click', function() {
			var expanded = $hero.toggleClass( 'is-expanded' ).hasClass( 'is-expanded' );

			$toggle
				.attr( 'aria-expanded', expanded ? 'true' : 'false' )
				.text( expanded ? $toggle.data( 'less' ) : $toggle.data( 'more' ) );
		} );
	}

	/**
	 * Hero contact popup: open/close, focus handling, and AJAX submit.
	 */
	function initHeroForm() {
		var $dialog = $( '#eglatone-hero-dialog' );

		if ( ! $dialog.length ) {
			return;
		}

		var $opener = $( '.hero-open-form' ),
			$panel  = $dialog.find( '.hero-dialog-panel' ),
			$last   = null;

		function open() {
			$last = document.activeElement;

			$dialog.removeAttr( 'hidden' );
			$( 'body' ).addClass( 'hero-dialog-open' );

			// Focus the first field so keyboard users land inside the dialog.
			var $first = $panel.find( 'input, textarea, button' ).not( '[tabindex="-1"]' ).first();

			if ( $first.length ) {
				$first.trigger( 'focus' );
			}
		}

		function close() {
			$dialog.attr( 'hidden', 'hidden' );
			$( 'body' ).removeClass( 'hero-dialog-open' );

			if ( $last && $last.focus ) {
				$last.focus();
			}
		}

		$opener.on( 'click', function( event ) {
			event.preventDefault();
			open();
		} );

		$dialog.on( 'click', '[data-hero-close]', function( event ) {
			event.preventDefault();
			close();
		} );

		$( document ).on( 'keydown', function( event ) {
			if ( 27 === event.keyCode && ! $dialog.attr( 'hidden' ) ) {
				close();
			}
		} );

		// Keep Tab inside the dialog while it is open.
		$dialog.on( 'keydown', function( event ) {
			if ( 9 !== event.keyCode ) {
				return;
			}

			var $focusable = $panel.find( 'a[href], button, input, textarea, select' )
				.filter( ':visible' )
				.not( '[tabindex="-1"]' );

			if ( ! $focusable.length ) {
				return;
			}

			var first = $focusable.first()[ 0 ],
				last  = $focusable.last()[ 0 ];

			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		} );

		// Built-in form submit. A shortcode form handles its own submission.
		var $form = $dialog.find( '.hero-form' );

		if ( ! $form.length || ! settings.ajaxUrl ) {
			return;
		}

		$form.on( 'submit', function( event ) {
			event.preventDefault();

			if ( $form.hasClass( 'is-sending' ) ) {
				return;
			}

			var $status = $form.find( '.hero-form-status' );

			$status.removeClass( 'is-error is-success' ).text( '' );
			$form.addClass( 'is-sending' );

			$.post( settings.ajaxUrl, {
				action: 'eglatone_hero_contact',
				nonce: $form.data( 'nonce' ),
				name: $form.find( '[name="name"]' ).val(),
				email: $form.find( '[name="email"]' ).val(),
				subject: $form.find( '[name="subject"]' ).val(),
				message: $form.find( '[name="message"]' ).val(),
				website: $form.find( '[name="website"]' ).val(),
				started: $form.find( '[name="started"]' ).val()
			} )
				.done( function( response ) {
					var ok  = response && response.success,
						msg = response && response.data && response.data.message;

					$status
						.addClass( ok ? 'is-success' : 'is-error' )
						.text( msg || '' );

					if ( ok ) {
						$form.find( 'input[type="text"], input[type="email"], textarea' ).val( '' );
					}
				} )
				.fail( function() {
					$status.addClass( 'is-error' ).text( 'Something went wrong. Please try again.' );
				} )
				.always( function() {
					$form.removeClass( 'is-sending' );
				} );
		} );
	}

	/**
	 * Press mentions strip - same seamless marquee as the ticker.
	 */
	function initPress() {
		var $press = $( '#eglatone-press-section' );

		if ( ! $press.length ) {
			return;
		}

		var $viewport = $press.find( '.press-viewport' ),
			$track    = $press.find( '.press-track' ),
			speed     = parseInt( $press.attr( 'data-speed' ), 10 ) || 40,
			reduced   = window.matchMedia &&
				window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

		if ( ! $track.length || ! $track.children().length ) {
			return;
		}

		if ( reduced ) {
			$press.addClass( 'is-static' );
			return;
		}

		function build() {
			$press.removeClass( 'is-ready' );
			$track.find( '.press-clone' ).remove();

			var $originals   = $track.children(),
				originalWide = 0,
				viewportWide = $viewport.outerWidth(),
				guard        = 0;

			$originals.each( function() {
				originalWide += $( this ).outerWidth( true );
			} );

			if ( ! originalWide || ! viewportWide ) {
				return;
			}

			var copyWide = originalWide;

			while ( copyWide < viewportWide && guard < 20 ) {
				$originals.clone().addClass( 'press-clone' ).appendTo( $track );
				copyWide += originalWide;
				guard++;
			}

			$track.children().clone().addClass( 'press-clone' ).appendTo( $track );

			$track.css( {
				'animation-duration': ( speed * Math.max( 1, copyWide / viewportWide ) ).toFixed( 2 ) + 's'
			} );

			$press.addClass( 'is-ready' );
		}

		// Logos change the track width as they load, so measure again after.
		build();
		$( window ).on( 'load', build );

		var timer = null;

		$( window ).on( 'resize', function() {
			clearTimeout( timer );
			timer = setTimeout( build, 250 );
		} );
	}

	$( function() {
		initSectionCarousels();
		initLoadMore();
		initTicker();
		initSingleMedia();
		initHero();
		initHeroForm();
		initPress();
	} );

} )( jQuery );
