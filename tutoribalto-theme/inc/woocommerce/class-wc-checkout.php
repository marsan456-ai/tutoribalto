<?php
/**
 * WooCommerce Checkout Validation Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_WC_Checkout
 *
 * Handles custom checkout field validation and processing.
 */
class Tutoribalto_WC_Checkout {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Validate checkout fields.
		add_action( 'woocommerce_checkout_process', array( $this, 'validate_checkout_fields' ) );
	}

	/**
	 * Validate checkout fields
	 */
	public function validate_checkout_fields(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$billing_country = isset( $_POST['billing_country'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) ) : '';
		$billing_email = isset( $_POST['billing_email'] ) ? sanitize_email( wp_unslash( $_POST['billing_email'] ) ) : '';
		$billing_verify_email = isset( $_POST['billing_verify_email'] ) ? sanitize_email( wp_unslash( $_POST['billing_verify_email'] ) ) : '';
		$client_type = isset( $_POST['billing_client_type'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_client_type'] ) ) : 'private';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// Validate email match.
		$this->validate_email_match( $billing_email, $billing_verify_email );

		// Country-specific validations.
		if ( 'IT' === $billing_country ) {
			$this->validate_italian_fields( $client_type );
		}
	}

	/**
	 * Validate email fields match
	 *
	 * @param string $email Primary email.
	 * @param string $verify_email Verification email.
	 */
	private function validate_email_match( string $email, string $verify_email ): void {
		if ( $email !== $verify_email ) {
			wc_add_notice(
				__( 'Email and Confirm Email fields must be same.', 'tutoribalto-theme' ),
				'error'
			);
		}
	}

	/**
	 * Validate Italian-specific fields
	 *
	 * @param string $client_type Client type (private or corp).
	 */
	private function validate_italian_fields( string $client_type ): void {
		if ( 'corp' === $client_type ) {
			$this->validate_company_fields();
		} else {
			$this->validate_private_fiscal_code();
		}
	}

	/**
	 * Validate company fields for Italian companies
	 */
	private function validate_company_fields(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$rs = isset( $_POST['billing_rs'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_rs'] ) ) : '';
		$pec = isset( $_POST['billing_pec'] ) ? sanitize_email( wp_unslash( $_POST['billing_pec'] ) ) : '';
		$codice_sdi = isset( $_POST['billing_codice_sdi'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_codice_sdi'] ) ) : '';
		$piva = isset( $_POST['billing_piva'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_piva'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// Validate Ragione Sociale.
		if ( empty( trim( $rs ) ) ) {
			wc_add_notice( __( 'Ragione Sociale campo vuoto', 'tutoribalto-theme' ), 'error' );
		}

		// Validate PEC.
		if ( empty( trim( $pec ) ) ) {
			wc_add_notice( __( 'P.E.C. campo vuoto', 'tutoribalto-theme' ), 'error' );
		}

		// Validate Codice SDI.
		if ( empty( trim( $codice_sdi ) ) ) {
			wc_add_notice( __( 'Codice SDI campo vuoto', 'tutoribalto-theme' ), 'error' );
		}

		// Validate Partita IVA.
		if ( empty( trim( $piva ) ) ) {
			wc_add_notice( __( 'Partita IVA campo vuoto', 'tutoribalto-theme' ), 'error' );
		} elseif ( ! $this->validate_partita_iva( $piva ) ) {
			wc_add_notice( __( 'Partita IVA non valida', 'tutoribalto-theme' ), 'error' );
		}
	}

	/**
	 * Validate private customer fiscal code
	 */
	private function validate_private_fiscal_code(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$fiscal_code = isset( $_POST['billing_fiscal'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['billing_fiscal'] ) ) ) : '';
		$fiscal_repeat = isset( $_POST['billing_fiscal_repeat'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_POST['billing_fiscal_repeat'] ) ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		// Check if empty.
		if ( empty( trim( $fiscal_code ) ) ) {
			wc_add_notice( __( 'Codice fiscale campo vuoto', 'tutoribalto-theme' ), 'error' );
			return;
		}

		// Check if matches repeat field.
		if ( $fiscal_code !== $fiscal_repeat ) {
			wc_add_notice( __( 'Il campo Codice fiscale deve essere ripetuto uguale', 'tutoribalto-theme' ), 'error' );
			return;
		}

		// Validate format.
		if ( ! $this->validate_codice_fiscale( $fiscal_code ) ) {
			wc_add_notice( __( 'Codice fiscale errato', 'tutoribalto-theme' ), 'error' );
		}
	}

	/**
	 * Validate Italian fiscal code (Codice Fiscale)
	 *
	 * @param string $fiscal_code Fiscal code.
	 * @return bool
	 */
	private function validate_codice_fiscale( string $fiscal_code ): bool {
		// Check length (must be 16 characters).
		if ( 16 !== strlen( $fiscal_code ) ) {
			return false;
		}

		// Check format: first 6 letters, then 2 numbers (birth year), then 1 letter (month), etc.
		if ( ! preg_match( '/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $fiscal_code ) ) {
			return false;
		}

		// Validate checksum (last character).
		return $this->validate_fiscal_code_checksum( $fiscal_code );
	}

	/**
	 * Validate fiscal code checksum
	 *
	 * @param string $fiscal_code Fiscal code.
	 * @return bool
	 */
	private function validate_fiscal_code_checksum( string $fiscal_code ): bool {
		// Fiscal code checksum algorithm.
		$odd_chars = array(
			'0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
			'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
			'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
			'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23,
		);

		$even_chars = array(
			'0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
			'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9,
			'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
			'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25,
		);

		$check_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$sum = 0;
		for ( $i = 0; $i < 15; $i++ ) {
			$char = $fiscal_code[ $i ];
			if ( 0 === $i % 2 ) {
				// Odd position (1st, 3rd, 5th...).
				$sum += $odd_chars[ $char ] ?? 0;
			} else {
				// Even position (2nd, 4th, 6th...).
				$sum += $even_chars[ $char ] ?? 0;
			}
		}

		$check_char = $check_chars[ $sum % 26 ];
		return $check_char === $fiscal_code[15];
	}

	/**
	 * Validate Italian VAT number (Partita IVA)
	 *
	 * @param string $piva Partita IVA.
	 * @return bool
	 */
	private function validate_partita_iva( string $piva ): bool {
		// Remove spaces and normalize.
		$piva = str_replace( ' ', '', $piva );

		// Must be 11 digits.
		if ( ! preg_match( '/^[0-9]{11}$/', $piva ) ) {
			return false;
		}

		// Validate checksum (Luhn algorithm variant).
		$sum = 0;
		for ( $i = 0; $i < 11; $i++ ) {
			$digit = (int) $piva[ $i ];

			if ( 0 === $i % 2 ) {
				// Even position: multiply by 1.
				$sum += $digit;
			} else {
				// Odd position: multiply by 2, if > 9 subtract 9.
				$doubled = $digit * 2;
				$sum += ( $doubled > 9 ) ? ( $doubled - 9 ) : $doubled;
			}
		}

		return 0 === ( $sum % 10 );
	}
}
