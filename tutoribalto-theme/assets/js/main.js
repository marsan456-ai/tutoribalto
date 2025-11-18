/**
 * Main JavaScript for Tutoribalto Theme
 *
 * @package Tutoribalto_Theme
 */

(function($) {
	'use strict';

	/**
	 * Initialize theme on document ready
	 */
	$(document).ready(function() {
		// Initialize components
		TutoribaltoTheme.init();
	});

	/**
	 * Main theme object
	 */
	window.TutoribaltoTheme = {

		/**
		 * Initialize all theme components
		 */
		init: function() {
			this.mobileMenu();
			this.searchToggle();
			this.smoothScroll();
			this.cartUpdate();
		},

		/**
		 * Mobile menu toggle
		 */
		mobileMenu: function() {
			$('.mobile-menu-toggle').on('click', function(e) {
				e.preventDefault();
				$('body').toggleClass('mobile-menu-open');
				$('#site-navigation').slideToggle();
			});
		},

		/**
		 * Search toggle
		 */
		searchToggle: function() {
			$('.search-button a').on('click', function(e) {
				e.preventDefault();
				$('.tutoribalto-search-wrapper').fadeIn();
				$('.tutoribalto-search-wrapper input[type="search"]').focus();
			});

			$('.tutoribalto-close-search').on('click', function(e) {
				e.preventDefault();
				$('.tutoribalto-search-wrapper').fadeOut();
			});

			// Close on ESC key
			$(document).on('keyup', function(e) {
				if (e.key === "Escape" || e.keyCode === 27) {
					$('.tutoribalto-search-wrapper').fadeOut();
				}
			});
		},

		/**
		 * Smooth scroll for anchor links
		 */
		smoothScroll: function() {
			$('a[href*="#"]:not([href="#"])').on('click', function(e) {
				if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') &&
					location.hostname === this.hostname) {

					var target = $(this.hash);
					target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

					if (target.length) {
						e.preventDefault();
						$('html, body').animate({
							scrollTop: target.offset().top - 100
						}, 1000);
					}
				}
			});
		},

		/**
		 * Update cart count on AJAX add to cart
		 */
		cartUpdate: function() {
			$(document.body).on('added_to_cart', function() {
				// Update cart count
				$.ajax({
					url: tutoribaltoData.ajaxUrl,
					type: 'POST',
					data: {
						action: 'tutoribalto_get_cart_count'
					},
					success: function(response) {
						if (response.success) {
							$('.cart-count').text(response.data.count);
						}
					}
				});
			});
		}
	};

})(jQuery);
