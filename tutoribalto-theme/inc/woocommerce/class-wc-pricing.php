<?php
/**
 * WooCommerce Pricing Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_WC_Pricing
 *
 * Handles custom pricing display for variable products and wholesale prices.
 */
class Tutoribalto_WC_Pricing {

	/**
	 * Wholesale role name
	 *
	 * @var string
	 */
	private const WHOLESALE_ROLE = 'wholesale_customer';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Customize variable product price display.
		add_filter( 'woocommerce_variable_sale_price_html', array( $this, 'custom_variable_price' ), 10, 2 );
		add_filter( 'woocommerce_variable_price_html', array( $this, 'custom_variable_price' ), 10, 2 );

		// Add "from" text to variable prices (Italian only).
		add_action( 'wp_head', array( $this, 'add_price_styling' ) );
	}

	/**
	 * Custom variable product price display
	 *
	 * Shows wholesale prices for logged-in wholesale customers,
	 * standard "from X" prices for others.
	 *
	 * @param string     $price_html Price HTML.
	 * @param WC_Product $product Product object.
	 * @return string
	 */
	public function custom_variable_price( string $price_html, $product ): string {
		// Check if user is wholesale customer.
		if ( $this->is_wholesale_customer() ) {
			return $this->get_wholesale_variable_price( $product );
		}

		return $this->get_standard_variable_price( $product );
	}

	/**
	 * Check if current user is wholesale customer
	 *
	 * @return bool
	 */
	private function is_wholesale_customer(): bool {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user = wp_get_current_user();
		return in_array( self::WHOLESALE_ROLE, $user->roles, true );
	}

	/**
	 * Get wholesale variable price display
	 *
	 * @param WC_Product $product Product object.
	 * @return string
	 */
	private function get_wholesale_variable_price( $product ): string {
		if ( ! $product->is_type( 'variable' ) ) {
			return '';
		}

		$variations = $product->get_available_variations();
		if ( empty( $variations ) ) {
			return '';
		}

		$wholesale_prices = array();
		$regular_prices = array();

		// Collect all variation prices.
		foreach ( $variations as $variation ) {
			$variation_id = $variation['variation_id'];

			// Get wholesale price meta.
			$wholesale_price = get_post_meta( $variation_id, '_wholesale_price', true );
			if ( $wholesale_price ) {
				$wholesale_prices[] = (float) $wholesale_price;
			}

			// Get regular price.
			$regular_price = get_post_meta( $variation_id, '_price', true );
			if ( $regular_price ) {
				$regular_prices[] = (float) $regular_price;
			}
		}

		if ( empty( $wholesale_prices ) ) {
			return $this->get_standard_variable_price( $product );
		}

		// Get min/max prices.
		$min_regular = min( $regular_prices );
		$max_regular = max( $regular_prices );
		$min_wholesale = min( $wholesale_prices );
		$max_wholesale = max( $wholesale_prices );

		// Get labels from options.
		$rrp_label = get_option( 'wwo_rrp_label', __( 'RRP', 'tutoribalto-theme' ) );
		$wholesale_label = get_option( 'wwo_wholesale_label', __( 'Wholesale', 'tutoribalto-theme' ) );

		// Build price HTML.
		$price_html = '';

		// RRP (Regular Retail Price).
		if ( ! empty( $rrp_label ) ) {
			$rrp_price_display = ( $min_regular === $max_regular )
				? wc_price( $min_regular )
				: wc_price( $min_regular ) . ' - ' . wc_price( $max_regular );

			$price_html .= sprintf(
				'<div class="woo_retail"><span class="woo_retail_label">%s:</span> <span class="woo_retail_price">%s</span></div>',
				esc_html( $rrp_label ),
				$rrp_price_display
			);
		}

		// Wholesale price.
		if ( ! empty( $wholesale_label ) ) {
			$wholesale_price_display = ( $min_wholesale === $max_wholesale )
				? wc_price( $min_wholesale )
				: wc_price( $min_wholesale ) . ' - ' . wc_price( $max_wholesale );

			$price_html .= sprintf(
				'<div class="woo_wholesale"><span class="woo_wholesale_label">%s:</span> <span class="woo_wholesale_price">%s</span></div>',
				esc_html( $wholesale_label ),
				$wholesale_price_display
			);
		}

		return $price_html;
	}

	/**
	 * Get standard variable price display (for non-wholesale customers)
	 *
	 * @param WC_Product $product Product object.
	 * @return string
	 */
	private function get_standard_variable_price( $product ): string {
		if ( ! $product->is_type( 'variable' ) ) {
			return '';
		}

		$min_price = $product->get_variation_price( 'min', true );
		$max_price = $product->get_variation_price( 'max', true );

		// If prices are the same, show single price.
		if ( $min_price === $max_price ) {
			$price_html = wc_price( $min_price );
		} else {
			// Show "from" price.
			$price_html = sprintf(
				'<span class="from">%s</span> %s',
				esc_html__( 'a partire da', 'tutoribalto-theme' ),
				wc_price( $min_price )
			);
		}

		// Check for sale.
		$min_regular = $product->get_variation_regular_price( 'min', true );
		$max_regular = $product->get_variation_regular_price( 'max', true );

		$regular_price_html = ( $min_regular === $max_regular )
			? wc_price( $min_regular )
			: sprintf(
				'<span class="from">%s</span> %s',
				esc_html__( 'a partire da', 'tutoribalto-theme' ),
				wc_price( $min_regular )
			);

		// If on sale, show strikethrough regular price.
		if ( $min_price !== $min_regular ) {
			$price_html = sprintf(
				'<del>%s</del> <ins>%s</ins>',
				$regular_price_html . $product->get_price_suffix(),
				$price_html . $product->get_price_suffix()
			);
		}

		return $price_html;
	}

	/**
	 * Add custom pricing styles (Italian version)
	 */
	public function add_price_styling(): void {
		$lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'it';

		if ( 'it' !== $lang ) {
			return;
		}

		?>
		<style type="text/css">
			ins:before {
				content: "- ";
			}
			ins {
				margin-left: 5px;
			}
			.summary-inner ins {
				font-size: 20px;
			}
			.single-product-content .price del .amount {
				font-size: 20px;
			}
			.single-product-content .price br {
				display: contents;
			}
		</style>
		<?php
	}
}
