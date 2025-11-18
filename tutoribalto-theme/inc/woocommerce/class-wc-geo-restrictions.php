<?php
/**
 * WooCommerce Geo Restrictions Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_WC_Geo_Restrictions
 *
 * Handles geographic restrictions for checkout based on billing country.
 */
class Tutoribalto_WC_Geo_Restrictions {

	/**
	 * Restricted countries configuration
	 *
	 * @var array
	 */
	private array $restricted_countries = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_restricted_countries();

		// Add checkout validation.
		add_action( 'woocommerce_checkout_process', array( $this, 'check_geo_restrictions' ) );
	}

	/**
	 * Initialize restricted countries configuration
	 */
	private function init_restricted_countries(): void {
		$this->restricted_countries = array(
			'US' => array(
				'dealer_name' => 'Balto USA',
				'dealer_url'  => 'https://baltousa.com/',
			),
			'CA' => array(
				'dealer_name' => 'Balto Canada',
				'dealer_url'  => 'https://baltocanada.com/',
			),
			'GB' => array(
				'dealer_name' => 'Balto UK',
				'dealer_url'  => 'https://baltouk.co.uk/',
			),
			'IE' => array(
				'dealer_name' => 'Balto Canada', // Ireland uses Canada dealer.
				'dealer_url'  => '',
			),
			'NZ' => array(
				'dealer_name' => 'KVP',
				'dealer_url'  => 'https://baltousa.com/',
			),
			'MT' => array(
				'dealer_name' => 'Borg Cardona & Co.',
				'dealer_url'  => 'https://www.borgcardona.com.mt',
				'details'     => array(
					'company' => 'BORG CARDONA & CO.LTD.',
					'address' => 'ELTEX, DR ZAMMIT STREET - Balzan BZN 1434 - MALTA',
					'phone'   => '+356 21442698 ext.451',
					'mobile'  => '+356 99434570',
					'email'   => 'sales@borgcardona.com.mt',
				),
			),
		);

		// Allow filtering of restricted countries.
		$this->restricted_countries = apply_filters( 'tutoribalto_restricted_countries', $this->restricted_countries );
	}

	/**
	 * Check geo restrictions during checkout
	 */
	public function check_geo_restrictions(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$billing_country = isset( $_POST['billing_country'] )
			? sanitize_text_field( wp_unslash( $_POST['billing_country'] ) )
			: '';

		if ( empty( $billing_country ) ) {
			return;
		}

		// Check if country is restricted.
		if ( ! isset( $this->restricted_countries[ $billing_country ] ) ) {
			return;
		}

		// Get restriction data.
		$restriction = $this->restricted_countries[ $billing_country ];

		// Get current language.
		$lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'it';

		// Show error message.
		$message = $this->get_restriction_message( $billing_country, $restriction, $lang );
		wc_add_notice( $message, 'error' );
	}

	/**
	 * Get restriction message in appropriate language
	 *
	 * @param string $country_code Country code.
	 * @param array  $restriction Restriction data.
	 * @param string $lang Language code.
	 * @return string
	 */
	private function get_restriction_message( string $country_code, array $restriction, string $lang ): string {
		$messages = $this->get_message_templates();
		$template = $messages[ $lang ] ?? $messages['it'];

		$country_name = $this->get_country_name( $country_code, $lang );
		$dealer_name = esc_html( $restriction['dealer_name'] );
		$dealer_url = ! empty( $restriction['dealer_url'] )
			? '<a href="' . esc_url( $restriction['dealer_url'] ) . '" style="text-decoration:underline;font-size:22px;" target="_blank">' . esc_html( $restriction['dealer_url'] ) . '</a>'
			: '';

		$message = sprintf(
			$template,
			$country_name,
			$dealer_name
		);

		if ( $dealer_url ) {
			$message .= '<br><br>' . $dealer_url;
		}

		// Add detailed dealer info if available (e.g., for Malta).
		if ( ! empty( $restriction['details'] ) ) {
			$message .= $this->format_dealer_details( $restriction['details'] );
		}

		return $message;
	}

	/**
	 * Get message templates by language
	 *
	 * @return array
	 */
	private function get_message_templates(): array {
		return array(
			'it' => __( 'Gentile Cliente,<br>per avere questi prodotti in %1$s, devono essere acquistati dal rivenditore %2$s<br><br>', 'tutoribalto-theme' ),
			'en' => __( 'Dear Customer,<br>to have these products in %1$s, they must be purchased from the %2$s dealer<br><br>', 'tutoribalto-theme' ),
			'de' => __( 'Sehr geehrter Kunde,<br>Um diese Produkte aus %1$s, zu beziehen müssen sie über den %2$s Händler erworben werden<br><br>', 'tutoribalto-theme' ),
			'es' => __( 'Estimado cliente,<br>para tener estos productos en %1$s, deben comprarse al distribuidor %2$s<br><br>', 'tutoribalto-theme' ),
			'fr' => __( 'Cher Client,<br>pour avoir ces produits de %1$s, il faut les acheter chez le revendeur %2$s<br><br>', 'tutoribalto-theme' ),
		);
	}

	/**
	 * Get country name in appropriate language
	 *
	 * @param string $country_code Country code.
	 * @param string $lang Language code.
	 * @return string
	 */
	private function get_country_name( string $country_code, string $lang ): string {
		$country_names = array(
			'US' => array(
				'it' => 'Stati Uniti',
				'en' => 'United States',
				'de' => 'Vereinigte Staaten',
				'es' => 'Estados Unidos',
				'fr' => 'États-Unis',
			),
			'CA' => array(
				'it' => 'Canada',
				'en' => 'Canada',
				'de' => 'Kanada',
				'es' => 'Canadá',
				'fr' => 'Canada',
			),
			'GB' => array(
				'it' => 'Regno Unito',
				'en' => 'United Kingdom',
				'de' => 'Vereinigtes Königreich',
				'es' => 'Reino Unido',
				'fr' => 'Royaume-Uni',
			),
			'IE' => array(
				'it' => 'Irlanda',
				'en' => 'Ireland',
				'de' => 'Irland',
				'es' => 'Irlanda',
				'fr' => 'Irlande',
			),
			'NZ' => array(
				'it' => 'Nuova Zelanda',
				'en' => 'New Zealand',
				'de' => 'Neuseeland',
				'es' => 'Nueva Zelanda',
				'fr' => 'Nouvelle-Zélande',
			),
			'MT' => array(
				'it' => 'Malta',
				'en' => 'Malta',
				'de' => 'Malta',
				'es' => 'Malta',
				'fr' => 'Malte',
			),
		);

		return $country_names[ $country_code ][ $lang ] ?? $country_code;
	}

	/**
	 * Format dealer details (for countries with full contact info)
	 *
	 * @param array $details Dealer details.
	 * @return string
	 */
	private function format_dealer_details( array $details ): string {
		$html = '<br><br>';

		if ( ! empty( $details['company'] ) ) {
			$html .= esc_html( $details['company'] ) . '<br>';
		}

		if ( ! empty( $details['address'] ) ) {
			$html .= esc_html( $details['address'] ) . '<br>';
		}

		if ( ! empty( $details['phone'] ) ) {
			$html .= 'phone: <a href="tel:' . esc_attr( $details['phone'] ) . '">' . esc_html( $details['phone'] ) . '</a><br>';
		}

		if ( ! empty( $details['mobile'] ) ) {
			$html .= 'mobile: <a href="tel:' . esc_attr( $details['mobile'] ) . '">' . esc_html( $details['mobile'] ) . '</a><br>';
		}

		if ( ! empty( $details['website'] ) ) {
			$html .= 'website: <a href="' . esc_url( $details['website'] ) . '" target="_blank">' . esc_html( $details['website'] ) . '</a><br>';
		}

		if ( ! empty( $details['email'] ) ) {
			$html .= 'email: <a href="mailto:' . esc_attr( $details['email'] ) . '">' . esc_html( $details['email'] ) . '</a><br>';
		}

		return $html;
	}
}
