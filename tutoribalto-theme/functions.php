<?php
/**
 * Tutoribalto Theme - Main Functions File
 *
 * @package     Tutoribalto_Theme
 * @author      Tutoribalto Development Team
 * @copyright   2025 Tutoribalto
 * @license     GPL-2.0-or-later
 * @version     1.0.0
 *
 * @wordpress-plugin
 */

declare(strict_types=1);

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define theme constants
 */
define( 'TUTORIBALTO_THEME_VERSION', '1.0.0' );
define( 'TUTORIBALTO_THEME_DIR', get_template_directory() );
define( 'TUTORIBALTO_THEME_URI', get_template_directory_uri() );
define( 'TUTORIBALTO_THEME_INC', TUTORIBALTO_THEME_DIR . '/inc' );
define( 'TUTORIBALTO_THEME_ASSETS', TUTORIBALTO_THEME_URI . '/assets' );

/**
 * Minimum Requirements
 */
define( 'TUTORIBALTO_MIN_PHP_VERSION', '8.0' );
define( 'TUTORIBALTO_MIN_WP_VERSION', '6.0' );
define( 'TUTORIBALTO_MIN_WC_VERSION', '7.0' );

/**
 * Check PHP version compatibility
 */
if ( version_compare( PHP_VERSION, TUTORIBALTO_MIN_PHP_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'tutoribalto_php_version_notice' );
	return;
}

/**
 * PHP version notice
 */
function tutoribalto_php_version_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: 1: Current PHP version, 2: Required PHP version */
				esc_html__( 'Tutoribalto Theme requires PHP version %2$s or higher. You are running version %1$s. Please upgrade PHP.', 'tutoribalto-theme' ),
				esc_html( PHP_VERSION ),
				esc_html( TUTORIBALTO_MIN_PHP_VERSION )
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Autoloader for theme classes
 *
 * @param string $class_name Class name to load.
 */
function tutoribalto_autoloader( string $class_name ): void {
	// Only autoload classes in our namespace.
	if ( strpos( $class_name, 'Tutoribalto_' ) !== 0 ) {
		return;
	}

	// Convert class name to file path.
	$class_file = str_replace( '_', '-', strtolower( $class_name ) );
	$class_file = str_replace( 'tutoribalto-', '', $class_file );
	$class_path = TUTORIBALTO_THEME_INC . '/class-' . $class_file . '.php';

	// Check subdirectories.
	$subdirs = array( 'woocommerce', 'multilingual', 'blocks' );
	foreach ( $subdirs as $subdir ) {
		$subdir_path = TUTORIBALTO_THEME_INC . '/' . $subdir . '/class-' . $class_file . '.php';
		if ( file_exists( $subdir_path ) ) {
			require_once $subdir_path;
			return;
		}
	}

	// Load from main inc directory.
	if ( file_exists( $class_path ) ) {
		require_once $class_path;
	}
}
spl_autoload_register( 'tutoribalto_autoloader' );

/**
 * Initialize theme
 */
function tutoribalto_theme_init(): void {
	// Core classes.
	new Tutoribalto_Theme_Setup();
	new Tutoribalto_Assets_Manager();

	// WooCommerce classes (only if WC is active).
	if ( class_exists( 'WooCommerce' ) ) {
		new Tutoribalto_WC_Customizations();
		new Tutoribalto_WC_Pricing();
		new Tutoribalto_WC_Wholesale();
		new Tutoribalto_WC_Checkout();
		new Tutoribalto_WC_Geo_Restrictions();
		new Tutoribalto_WC_Product_Display();
	}

	// Multilingual support (only if WPML is active).
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		new Tutoribalto_WPML_Integration();
	}
}
add_action( 'after_setup_theme', 'tutoribalto_theme_init' );

/**
 * Check WooCommerce dependency
 */
function tutoribalto_check_woocommerce(): void {
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'tutoribalto_woocommerce_notice' );
	}
}
add_action( 'plugins_loaded', 'tutoribalto_check_woocommerce' );

/**
 * WooCommerce missing notice
 */
function tutoribalto_woocommerce_notice(): void {
	?>
	<div class="notice notice-warning">
		<p>
			<?php
			printf(
				/* translators: 1: Theme name */
				esc_html__( '%s requires WooCommerce to be installed and active for full functionality.', 'tutoribalto-theme' ),
				'<strong>Tutoribalto Theme</strong>'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Theme activation hook
 */
function tutoribalto_theme_activation(): void {
	// Flush rewrite rules.
	flush_rewrite_rules();

	// Set default options if needed.
	if ( ! get_option( 'tutoribalto_theme_activated' ) ) {
		update_option( 'tutoribalto_theme_activated', time() );
	}
}
add_action( 'after_switch_theme', 'tutoribalto_theme_activation' );

/**
 * Load plugin textdomain
 */
function tutoribalto_load_textdomain(): void {
	load_theme_textdomain( 'tutoribalto-theme', TUTORIBALTO_THEME_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'tutoribalto_load_textdomain' );
