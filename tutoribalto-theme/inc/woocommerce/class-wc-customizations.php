<?php
/**
 * WooCommerce Customizations Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_WC_Customizations
 *
 * General WooCommerce customizations and hooks.
 */
class Tutoribalto_WC_Customizations {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Custom search with products menu.
		add_action( 'tutoribalto_header_search', array( $this, 'header_search_block' ) );

		// Add custom field below product title in loop.
		add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'display_custom_field_in_loop' ), 2 );

		// Remove add to cart in loop, replace with view product.
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'view_product_button' ), 10 );

		// Upsell popup after add to cart.
		add_action( 'wp_footer', array( $this, 'upsell_popup_cleaning_kit' ), 99 );

		// Hide optional label in forms.
		add_action( 'wp_head', array( $this, 'hide_optional_labels' ) );

		// Add recommendations to product tabs.
		add_action( 'wp_enqueue_scripts', array( $this, 'inject_product_recommendations' ) );

		// Hide meta for specific categories.
		add_action( 'wp_head', array( $this, 'hide_meta_for_categories' ) );

		// Add custom billing fields.
		add_filter( 'woocommerce_billing_fields', array( $this, 'add_custom_billing_fields' ) );

		// Display custom fields in admin order.
		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_custom_order_meta' ) );
	}

	/**
	 * Header search block with menu integration
	 */
	public function header_search_block(): void {
		?>
		<div class="main-nav2 site-navigation tutoribalto-navigation menu-right" role="navigation">
			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'menu-destra',
					'container_class' => 'menu-principale-container',
					'fallback_cb'     => false,
				)
			);
			?>
		</div>

		<div class="search-button tutoribalto-search-fullscreen mobile-search-icon">
			<a href="#" aria-label="<?php esc_attr_e( 'Search', 'tutoribalto-theme' ); ?>">
				<i class="fa fa-search"></i>
			</a>
			<div class="tutoribalto-search-wrapper">
				<div class="tutoribalto-search-inner">
					<span class="tutoribalto-close-search"><?php esc_html_e( 'close', 'tutoribalto-theme' ); ?></span>
					<?php get_product_search_form(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display custom field (ACF) below product title in loop
	 */
	public function display_custom_field_in_loop(): void {
		global $product;

		if ( ! $product ) {
			return;
		}

		// Get ACF field (requires ACF plugin).
		$custom_text = function_exists( 'get_field' ) ? get_field( 'testo_anteprima', $product->get_id() ) : '';

		if ( ! empty( $custom_text ) ) {
			echo '<p class="sottotitoloop">' . esc_html( $custom_text ) . '</p>';
		}
	}

	/**
	 * View product button instead of add to cart in loop
	 *
	 * @global WC_Product $product
	 */
	public function view_product_button(): void {
		global $product;

		if ( ! $product ) {
			return;
		}

		$link = $product->get_permalink();
		echo '<a href="' . esc_url( $link ) . '" class="button addtocartbutton">' .
			esc_html__( 'Select options', 'tutoribalto-theme' ) .
			'</a>';
	}

	/**
	 * Upsell popup for Balto Cleaning Kit
	 *
	 * Shows popup after adding product to cart (excluding specific products/categories).
	 */
	public function upsell_popup_cleaning_kit(): void {
		if ( ! is_product() ) {
			return;
		}

		global $product;
		if ( ! $product ) {
			return;
		}

		// Get current language.
		$lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'it';

		// Excluded product IDs (cleaning kit itself + specific products).
		$excluded_ids = $this->get_excluded_product_ids( $lang );

		if ( in_array( $product->get_id(), $excluded_ids, true ) ) {
			return;
		}

		// Check if product is in excluded categories.
		if ( $this->is_product_in_excluded_categories( $product, array( 'ricambi', 'balto-care' ), $lang ) ) {
			return;
		}

		// Get translations.
		$translations = $this->get_upsell_translations();
		$t = $translations[ $lang ] ?? $translations['it'];

		// Get cleaning kit product ID (translated).
		$cleaning_kit_id = $this->get_cleaning_kit_id( $lang );
		$image_url = 'https://www.tutoribalto.com/wp-content/uploads/2025/06/KIT-PULIZIA-TUTORI-BALTO-2-300x300.jpg';

		include TUTORIBALTO_THEME_DIR . '/templates/parts/upsell-popup.php';
	}

	/**
	 * Get excluded product IDs
	 *
	 * @param string $lang Language code.
	 * @return array
	 */
	private function get_excluded_product_ids( string $lang ): array {
		$base_ids = array( 47771, 48414, 48667, 48772 );
		$excluded = array();

		foreach ( $base_ids as $id ) {
			$translated_id = apply_filters( 'wpml_object_id', $id, 'product', true, $lang );
			if ( $translated_id ) {
				$excluded[] = $translated_id;
			}
		}

		return $excluded;
	}

	/**
	 * Check if product is in excluded categories
	 *
	 * @param WC_Product $product Product object.
	 * @param array      $category_slugs Category slugs.
	 * @param string     $lang Language code.
	 * @return bool
	 */
	private function is_product_in_excluded_categories( $product, array $category_slugs, string $lang ): bool {
		foreach ( $category_slugs as $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( ! $term ) {
				continue;
			}

			$translated_term_id = apply_filters( 'wpml_object_id', $term->term_id, 'product_cat', true, $lang );
			if ( has_term( $translated_term_id, 'product_cat', $product->get_id() ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get cleaning kit product ID (WPML translated)
	 *
	 * @param string $lang Language code.
	 * @return int
	 */
	private function get_cleaning_kit_id( string $lang ): int {
		$base_id = 47771; // Default IT.
		return (int) apply_filters( 'wpml_object_id', $base_id, 'product', true, $lang );
	}

	/**
	 * Get upsell popup translations
	 *
	 * @return array
	 */
	private function get_upsell_translations(): array {
		return array(
			'it' => array(
				'title'        => __( 'NON DIMENTICARTI DI TENERE PULITO IL TUO TUTORE', 'tutoribalto-theme' ),
				'subtitle'     => __( 'PER TE IL NOSTRO BALTO CLEANING KIT', 'tutoribalto-theme' ),
				'offer'        => __( 'È IN OFFERTA!', 'tutoribalto-theme' ),
				'price'        => '12,90€ anziché <del>18,90€</del>',
				'add_button'   => __( 'AGGIUNGI AL CARRELLO', 'tutoribalto-theme' ),
				'close_button' => __( 'CHIUDI', 'tutoribalto-theme' ),
			),
			'en' => array(
				'title'        => __( 'DON'T FORGET TO KEEP YOUR BRACE CLEAN', 'tutoribalto-theme' ),
				'subtitle'     => __( 'OUR BALTO CLEANING KIT FOR YOU', 'tutoribalto-theme' ),
				'offer'        => __( 'IS ON SALE!', 'tutoribalto-theme' ),
				'price'        => '€12.90 instead of <del>€18.90</del>',
				'add_button'   => __( 'ADD TO CART', 'tutoribalto-theme' ),
				'close_button' => __( 'CLOSE', 'tutoribalto-theme' ),
			),
			'de' => array(
				'title'        => __( 'VERGISS NICHT, DEINE SCHIENE SAUBER ZU HALTEN', 'tutoribalto-theme' ),
				'subtitle'     => __( 'UNSER BALTO CLEANING KIT FÜR DICH', 'tutoribalto-theme' ),
				'offer'        => __( 'IST IM ANGEBOT!', 'tutoribalto-theme' ),
				'price'        => '12,90 € statt <del>18,90 €</del>',
				'add_button'   => __( 'IN DEN WARENKORB', 'tutoribalto-theme' ),
				'close_button' => __( 'SCHLIESSEN', 'tutoribalto-theme' ),
			),
			'fr' => array(
				'title'        => __( 'N'OUBLIEZ PAS DE GARDER VOTRE ATTELLE PROPRE', 'tutoribalto-theme' ),
				'subtitle'     => __( 'NOTRE KIT DE NETTOYAGE BALTO POUR VOUS', 'tutoribalto-theme' ),
				'offer'        => __( 'EST EN PROMO !', 'tutoribalto-theme' ),
				'price'        => '12,90 € au lieu de <del>18,90 €</del>',
				'add_button'   => __( 'AJOUTER AU PANIER', 'tutoribalto-theme' ),
				'close_button' => __( 'FERMER', 'tutoribalto-theme' ),
			),
			'es' => array(
				'title'        => __( 'NO OLVIDES MANTENER LIMPIO TU TUTOR', 'tutoribalto-theme' ),
				'subtitle'     => __( 'NUESTRO BALTO CLEANING KIT PARA TI', 'tutoribalto-theme' ),
				'offer'        => __( '¡ESTÁ EN OFERTA!', 'tutoribalto-theme' ),
				'price'        => '12,90 € en lugar de <del>18,90 €</del>',
				'add_button'   => __( 'AÑADIR AL CARRITO', 'tutoribalto-theme' ),
				'close_button' => __( 'CERRAR', 'tutoribalto-theme' ),
			),
		);
	}

	/**
	 * Hide optional labels in forms
	 */
	public function hide_optional_labels(): void {
		echo '<style>.optional { display: none; }</style>';
	}

	/**
	 * Inject product recommendations script
	 */
	public function inject_product_recommendations(): void {
		if ( ! is_product() ) {
			return;
		}

		wp_add_inline_script(
			'tutoribalto-product',
			file_get_contents( TUTORIBALTO_THEME_DIR . '/assets/js/product-recommendations.js' )
		);
	}

	/**
	 * Hide meta for specific categories
	 */
	public function hide_meta_for_categories(): void {
		if ( ! is_product() ) {
			return;
		}

		global $post;
		$terms = get_the_terms( $post->ID, 'product_cat' );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return;
		}

		// Category IDs to hide meta.
		$target_categories = array( 815, 816, 817, 818, 819 );

		foreach ( $terms as $term ) {
			if ( in_array( $term->term_id, $target_categories, true ) ) {
				echo '<style type="text/css">.meta-sizeimpsz { display: none !important; }</style>';
				break;
			}
		}
	}

	/**
	 * Add custom billing fields
	 *
	 * @param array $fields Billing fields.
	 * @return array
	 */
	public function add_custom_billing_fields( array $fields ): array {
		$fields['billing_Codice_SDI'] = array(
			'label'       => __( 'Codice SDI', 'tutoribalto-theme' ),
			'placeholder' => _x( 'Codice SDI', 'placeholder', 'tutoribalto-theme' ),
			'required'    => false,
			'class'       => array( 'form-row-wide' ),
			'type'        => 'text',
		);

		$fields['billing_PEC'] = array(
			'label'       => __( 'P.E.C.', 'tutoribalto-theme' ),
			'placeholder' => _x( 'P.E.C.', 'placeholder', 'tutoribalto-theme' ),
			'required'    => false,
			'class'       => array( 'form-row-wide' ),
			'type'        => 'email',
		);

		return $fields;
	}

	/**
	 * Display custom order meta in admin
	 *
	 * @param WC_Order $order Order object.
	 */
	public function display_custom_order_meta( $order ): void {
		$order_id = $order->get_id();

		$codice_sdi = get_post_meta( $order_id, '_billing_Codice_SDI', true );
		$pec = get_post_meta( $order_id, '_billing_PEC', true );

		if ( $codice_sdi ) {
			echo '<div class="address"><p><strong>' .
				esc_html__( 'Codice SDI:', 'tutoribalto-theme' ) .
				'</strong> ' . esc_html( $codice_sdi ) . '</p></div>';
		}

		if ( $pec ) {
			echo '<div class="address"><p><strong>' .
				esc_html__( 'PEC:', 'tutoribalto-theme' ) .
				'</strong> ' . esc_html( $pec ) . '</p></div>';
		}
	}
}
