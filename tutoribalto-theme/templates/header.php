<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary">
		<?php esc_html_e( 'Skip to content', 'tutoribalto-theme' ); ?>
	</a>

	<header id="masthead" class="site-header">
		<div class="header-container">
			<!-- Logo -->
			<div class="site-branding">
				<?php
				if ( has_custom_logo() ) {
					the_custom_logo();
				} else {
					?>
					<h1 class="site-title">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php bloginfo( 'name' ); ?>
						</a>
					</h1>
					<?php
				}
				?>
			</div>

			<!-- Primary Navigation -->
			<nav id="site-navigation" class="main-navigation" role="navigation">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>

			<!-- Header Right: Search + Menu Destra -->
			<div class="header-right">
				<?php do_action( 'tutoribalto_header_search' ); ?>

				<?php if ( class_exists( 'WooCommerce' ) ) : ?>
					<!-- Cart Icon -->
					<div class="header-cart">
						<a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
							<i class="fa fa-shopping-cart"></i>
							<span class="cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<main id="primary" class="site-main">
