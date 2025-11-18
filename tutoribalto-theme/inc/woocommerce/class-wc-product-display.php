<?php
/**
 * WooCommerce Product Display Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_WC_Product_Display
 *
 * Handles custom product display and filtering functionality.
 */
class Tutoribalto_WC_Product_Display {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add product filters JavaScript.
		add_action( 'wp_footer', array( $this, 'add_product_filter_scripts' ) );

		// Add size chart functionality.
		add_action( 'wp_footer', array( $this, 'add_size_chart_scripts' ) );

		// Hide size tabs (moved to custom location).
		add_action( 'wp_head', array( $this, 'hide_size_tabs' ) );

		// Add slider information layers.
		add_action( 'wp_footer', array( $this, 'add_slider_scripts' ) );
	}

	/**
	 * Add product filter scripts
	 */
	public function add_product_filter_scripts(): void {
		if ( ! is_front_page() && ! is_shop() ) {
			return;
		}

		?>
		<script type="text/javascript">
		// Product filter functions for homepage tabs
		function legfilter() {
			var fiveLi = jQuery("ul.products-tabs-title li:eq(4)");
			fiveLi.trigger("click");
		}

		function headfilter() {
			var secondLi = jQuery("ul.products-tabs-title li:eq(1)");
			secondLi.trigger("click");
		}

		function frontlegfilter() {
			var fourLi = jQuery("ul.products-tabs-title li:eq(3)");
			fourLi.trigger("click");
		}

		function chestfilter() {
			var thirdLi = jQuery("ul.products-tabs-title li:eq(2)");
			thirdLi.trigger("click");
		}
		</script>
		<?php
	}

	/**
	 * Add size chart functionality
	 */
	public function add_size_chart_scripts(): void {
		if ( ! is_product() ) {
			return;
		}

		$lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'it';

		// Map language to tab panel class.
		$tab_panels = array(
			'en' => 'sizes',
			'de' => 'groessen',
			'fr' => 'tailles',
			'es' => 'tabla-de-tallas',
		);

		$panel_class = $tab_panels[ $lang ] ?? 'sizes';

		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Move size chart content to custom location
			if ($('.woocommerce-Tabs-panel--<?php echo esc_js( $panel_class ); ?>').length) {
				$('.chart_sz').append($('.woocommerce-Tabs-panel--<?php echo esc_js( $panel_class ); ?>').html());
			}
		});
		</script>
		<?php
	}

	/**
	 * Hide size tabs (CSS)
	 */
	public function hide_size_tabs(): void {
		if ( ! is_product() ) {
			return;
		}

		?>
		<style type="text/css">
		.sizes_tab,
		.groessen_tab,
		.tailles_tab,
		.tabla-de-tallas_tab,
		.woocommerce-Tabs-panel--tabla-de-tallas,
		.woocommerce-Tabs-panel--sizes,
		.woocommerce-Tabs-panel--groessen,
		.woocommerce-Tabs-panel--tailles {
			display: none !important;
		}
		</style>
		<?php
	}

	/**
	 * Add slider information layer scripts
	 *
	 * Handles showing/hiding information overlays on product slider images.
	 */
	public function add_slider_scripts(): void {
		if ( ! is_front_page() ) {
			return;
		}

		?>
		<script type="text/javascript">
		// Slider information layers
		function showinformation() {
			[41, 42, 43, 44, 45].forEach(function(sliderId) {
				var layer = jQuery("#slider-" + sliderId + "-slide-" + getSlideId(sliderId) + "-layer-18");
				if (layer.length > 0) {
					layer.css("opacity", "1", "important");
				}
			});
		}

		function hideinformation() {
			[41, 42, 43, 44, 45].forEach(function(sliderId) {
				var layer = jQuery("#slider-" + sliderId + "-slide-" + getSlideId(sliderId) + "-layer-18");
				if (layer.length > 0) {
					layer.css("opacity", "0", "important");
				}
			});
		}

		function showheadinfo() {
			[41, 42, 43, 44, 45].forEach(function(sliderId) {
				var layer = jQuery("#slider-" + sliderId + "-slide-" + getSlideId(sliderId) + "-layer-10");
				if (layer.length > 0) {
					layer.css("opacity", "1", "important");
				}
			});
		}

		function hideheadinfo() {
			[41, 42, 43, 44, 45].forEach(function(sliderId) {
				var layer = jQuery("#slider-" + sliderId + "-slide-" + getSlideId(sliderId) + "-layer-10");
				if (layer.length > 0) {
					layer.css("opacity", "0", "important");
				}
			});
		}

		function showbodyinfo() {
			[41, 42, 43, 44, 45].forEach(function(sliderId) {
				var layer = jQuery("#slider-" + sliderId + "-slide-" + getSlideId(sliderId) + "-layer-19");
				if (layer.length > 0) {
					layer.css("opacity", "1", "important");
				}
			});
		}

		function hidebodyinfo() {
			[41, 42, 43, 44, 45].forEach(function(sliderId) {
				var layer = jQuery("#slider-" + sliderId + "-slide-" + getSlideId(sliderId) + "-layer-19");
				if (layer.length > 0) {
					layer.css("opacity", "0", "important");
				}
			});
		}

		function backleginfoshow() {
			[41, 42, 43, 44, 45].forEach(function(sliderId) {
				var layer = jQuery("#slider-" + sliderId + "-slide-" + getSlideId(sliderId) + "-layer-20");
				if (layer.length > 0) {
					layer.css("opacity", "1", "important");
				}
			});
		}

		function backleginfohide() {
			[41, 42, 43, 44, 45].forEach(function(sliderId) {
				var layer = jQuery("#slider-" + sliderId + "-slide-" + getSlideId(sliderId) + "-layer-20");
				if (layer.length > 0) {
					layer.css("opacity", "0", "important");
				}
			});
		}

		// Helper function to get slide ID based on slider ID
		function getSlideId(sliderId) {
			var slideIds = {
				41: 386,
				42: 391,
				43: 396,
				44: 401,
				45: 406
			};
			return slideIds[sliderId] || 386;
		}
		</script>
		<?php
	}
}
