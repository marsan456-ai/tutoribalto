<?php
/**
 * Theme Setup Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_Theme_Setup
 *
 * Handles theme setup, support features, and WordPress hooks.
 */
class Tutoribalto_Theme_Setup {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'after_setup_theme', array( $this, 'content_width' ), 0 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		add_action( 'init', array( $this, 'register_menus' ) );
	}

	/**
	 * Theme setup
	 */
	public function setup(): void {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 1200, 9999 );

		// Add custom image sizes.
		add_image_size( 'tutoribalto-product-thumb', 300, 300, true );
		add_image_size( 'tutoribalto-product-large', 600, 600, true );
		add_image_size( 'tutoribalto-hero', 1920, 800, true );

		// Switch default core markup for search form, comment form, and comments.
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for core custom logo.
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 80,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		// WooCommerce support.
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		// Editor styles.
		add_theme_support( 'editor-styles' );
		add_editor_style( 'assets/css/editor-style.css' );

		// Responsive embeds.
		add_theme_support( 'responsive-embeds' );

		// Block editor wide alignment.
		add_theme_support( 'align-wide' );
	}

	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet.
	 */
	public function content_width(): void {
		$GLOBALS['content_width'] = apply_filters( 'tutoribalto_content_width', 1200 );
	}

	/**
	 * Register widget areas.
	 */
	public function widgets_init(): void {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Sidebar', 'tutoribalto-theme' ),
				'id'            => 'sidebar-1',
				'description'   => esc_html__( 'Add widgets here.', 'tutoribalto-theme' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer Area 1', 'tutoribalto-theme' ),
				'id'            => 'footer-1',
				'description'   => esc_html__( 'Footer widget area 1.', 'tutoribalto-theme' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer Area 2', 'tutoribalto-theme' ),
				'id'            => 'footer-2',
				'description'   => esc_html__( 'Footer widget area 2.', 'tutoribalto-theme' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer Area 3', 'tutoribalto-theme' ),
				'id'            => 'footer-3',
				'description'   => esc_html__( 'Footer widget area 3.', 'tutoribalto-theme' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}

	/**
	 * Register navigation menus.
	 */
	public function register_menus(): void {
		register_nav_menus(
			array(
				'primary'       => esc_html__( 'Primary Menu', 'tutoribalto-theme' ),
				'menu-destra'   => esc_html__( 'Menu di Destra', 'tutoribalto-theme' ), // From basel-child.
				'footer'        => esc_html__( 'Footer Menu', 'tutoribalto-theme' ),
			)
		);
	}
}
