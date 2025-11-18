<?php
/**
 * Assets Manager Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_Assets_Manager
 *
 * Handles enqueuing of CSS and JavaScript files.
 */
class Tutoribalto_Assets_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Enqueue styles
	 */
	public function enqueue_styles(): void {
		$version = TUTORIBALTO_THEME_VERSION;

		// Main stylesheet.
		wp_enqueue_style(
			'tutoribalto-main',
			TUTORIBALTO_THEME_ASSETS . '/css/main.css',
			array(),
			$version
		);

		// WooCommerce styles (only on WC pages).
		if ( class_exists( 'WooCommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
			wp_enqueue_style(
				'tutoribalto-woocommerce',
				TUTORIBALTO_THEME_ASSETS . '/css/woocommerce.css',
				array( 'tutoribalto-main' ),
				$version
			);
		}

		// Theme stylesheet (required by WordPress).
		wp_enqueue_style(
			'tutoribalto-theme',
			get_stylesheet_uri(),
			array(),
			$version
		);
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts(): void {
		$version = TUTORIBALTO_THEME_VERSION;

		// Main JavaScript.
		wp_enqueue_script(
			'tutoribalto-main',
			TUTORIBALTO_THEME_ASSETS . '/js/main.js',
			array( 'jquery' ),
			$version,
			true
		);

		// Localize script with theme data.
		wp_localize_script(
			'tutoribalto-main',
			'tutoribaltoData',
			array(
				'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'tutoribalto_nonce' ),
				'currentLanguage' => defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'it',
				'strings'         => $this->get_localized_strings(),
			)
		);

		// Checkout specific scripts.
		if ( is_checkout() ) {
			wp_enqueue_script(
				'tutoribalto-checkout',
				TUTORIBALTO_THEME_ASSETS . '/js/checkout.js',
				array( 'jquery', 'tutoribalto-main' ),
				$version,
				true
			);
		}

		// Product pages scripts.
		if ( is_product() ) {
			wp_enqueue_script(
				'tutoribalto-product',
				TUTORIBALTO_THEME_ASSETS . '/js/product.js',
				array( 'jquery', 'tutoribalto-main' ),
				$version,
				true
			);
		}

		// Comment reply script.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Enqueue admin assets
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( string $hook ): void {
		wp_enqueue_style(
			'tutoribalto-admin',
			TUTORIBALTO_THEME_ASSETS . '/css/admin.css',
			array(),
			TUTORIBALTO_THEME_VERSION
		);
	}

	/**
	 * Get localized strings for JavaScript
	 *
	 * @return array
	 */
	private function get_localized_strings(): array {
		$lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'it';

		$strings = array(
			'it' => array(
				'from'           => 'a partire da',
				'selectPaw'      => 'Scegli la zampa',
				'pawNote'        => 'guardando il cane da dietro',
				'requiredField'  => 'Campo obbligatorio',
				'invalidEmail'   => 'Email non valida',
				'invalidFiscal'  => 'Codice fiscale non valido',
			),
			'en' => array(
				'from'           => 'from',
				'selectPaw'      => 'Choose the paw',
				'pawNote'        => 'looking at the dog from behind',
				'requiredField'  => 'Required field',
				'invalidEmail'   => 'Invalid email',
				'invalidFiscal'  => 'Invalid fiscal code',
			),
			'de' => array(
				'from'           => 'ab',
				'selectPaw'      => 'Wähle die Pfote',
				'pawNote'        => 'schaue den Hund von hinten an',
				'requiredField'  => 'Pflichtfeld',
				'invalidEmail'   => 'Ungültige E-Mail',
				'invalidFiscal'  => 'Ungültige Steuernummer',
			),
			'fr' => array(
				'from'           => 'à partir de',
				'selectPaw'      => 'Choisissez la patte',
				'pawNote'        => 'regardant le chien par derrière',
				'requiredField'  => 'Champ obligatoire',
				'invalidEmail'   => 'Email invalide',
				'invalidFiscal'  => 'Code fiscal invalide',
			),
			'es' => array(
				'from'           => 'desde',
				'selectPaw'      => 'Elige la pata',
				'pawNote'        => 'mirando al perro desde atrás',
				'requiredField'  => 'Campo obligatorio',
				'invalidEmail'   => 'Email inválido',
				'invalidFiscal'  => 'Código fiscal inválido',
			),
		);

		return $strings[ $lang ] ?? $strings['it'];
	}
}
