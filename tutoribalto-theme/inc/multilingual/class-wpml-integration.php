<?php
/**
 * WPML Integration Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_WPML_Integration
 *
 * Handles WPML multilingual integration and customizations.
 */
class Tutoribalto_WPML_Integration {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add language switcher customizations.
		add_action( 'wp_footer', array( $this, 'add_language_switcher_script' ) );

		// Register strings for translation.
		add_action( 'init', array( $this, 'register_strings' ) );
	}

	/**
	 * Add language switcher script
	 *
	 * Fixes WPML language switcher dropdown behavior.
	 */
	public function add_language_switcher_script(): void {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			const languageToggle = document.querySelector(".js-wpml-ls-item-toggle");
			const languageList = document.querySelector(".wpml-ls-sub-menu");

			if (languageToggle && languageList) {
				languageToggle.addEventListener("click", function() {
					const parentList = $('.wpml-ls-current-language ul');

					if (!parentList.hasClass('wpml-ls-sub-menu')) {
						parentList.removeClass('color-scheme-dark sub-menu');
						parentList.addClass('wpml-ls-sub-menu');
					} else {
						languageList.classList.toggle("color-scheme-dark");
						languageList.classList.toggle("sub-menu");
						parentList.removeClass('wpml-ls-sub-menu');
					}
				});
			}
		});
		</script>
		<?php
	}

	/**
	 * Register translatable strings
	 */
	public function register_strings(): void {
		if ( ! function_exists( 'icl_register_string' ) ) {
			return;
		}

		// Register common theme strings.
		$strings = array(
			'Select options'                     => __( 'Select options', 'tutoribalto-theme' ),
			'View Product'                       => __( 'View Product', 'tutoribalto-theme' ),
			'Add to cart'                        => __( 'Add to cart', 'tutoribalto-theme' ),
			'From'                               => __( 'a partire da', 'tutoribalto-theme' ),
			'Choose the paw'                     => __( 'Scegli la zampa', 'tutoribalto-theme' ),
			'looking at the dog from behind'     => __( 'guardando il cane da dietro', 'tutoribalto-theme' ),
			'Required field'                     => __( 'Campo obbligatorio', 'tutoribalto-theme' ),
			'Invalid email'                      => __( 'Email non valida', 'tutoribalto-theme' ),
			'Email and Confirm Email must match' => __( 'Email e Conferma Email devono corrispondere', 'tutoribalto-theme' ),
		);

		foreach ( $strings as $name => $value ) {
			icl_register_string( 'tutoribalto-theme', $name, $value );
		}
	}

	/**
	 * Get current language code
	 *
	 * @return string
	 */
	public static function get_current_language(): string {
		return defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'it';
	}

	/**
	 * Get translated object ID
	 *
	 * @param int    $object_id Object ID.
	 * @param string $object_type Object type (post, page, product, etc).
	 * @param string $lang Language code (optional, current language by default).
	 * @return int|null
	 */
	public static function get_translated_id( int $object_id, string $object_type = 'post', ?string $lang = null ): ?int {
		if ( ! function_exists( 'apply_filters' ) ) {
			return $object_id;
		}

		$lang = $lang ?? self::get_current_language();

		$translated_id = apply_filters( 'wpml_object_id', $object_id, $object_type, true, $lang );

		return $translated_id ? (int) $translated_id : null;
	}

	/**
	 * Get all active languages
	 *
	 * @return array
	 */
	public static function get_active_languages(): array {
		if ( ! function_exists( 'apply_filters' ) ) {
			return array( 'it' );
		}

		$languages = apply_filters( 'wpml_active_languages', null );

		if ( ! is_array( $languages ) ) {
			return array( 'it' );
		}

		return array_keys( $languages );
	}
}
