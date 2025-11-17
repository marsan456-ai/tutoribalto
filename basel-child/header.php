<?php
/**
 * The Header template for our theme
 */
?><!DOCTYPE html>
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<style>
  <?php if(ICL_LANGUAGE_CODE=='it'){
?>
#menu-item-wpml-ls-597-it:before {
    background-color: transparent !important;
	content: url('https://www.tutoribalto.com/wp-content/uploads/2023/05/MAPPAMONDO-new.svg');
	}
#menu-item-wpml-ls-597-it:before {
    background-color: transparent !important;
	content: url(https://www.tutoribalto.com/wp-content/uploads/2023/05/MAPPAMONDO-new.svg);
	width:20px;
	margin-top:-12px;
}

<?php
}
?>
 <?php if(ICL_LANGUAGE_CODE=='en'){
?>
#menu-item-wpml-ls-597-en:before {
    background-color: transparent !important;
	content: url(https://www.tutoribalto.com/wp-content/uploads/2023/05/MAPPAMONDO-new.svg);
	width:20px !important;
	margin-top:-12px;
}

<?php
}
?>

 <?php if(ICL_LANGUAGE_CODE=='es'){
?>
#menu-item-wpml-ls-597-es:before {
    background-color: transparent !important;
	content: url(https://www.tutoribalto.com/wp-content/uploads/2023/05/MAPPAMONDO-new.svg);
	width:20px !important;
	margin-top:-12px;
}

<?php
}
?>

 <?php if(ICL_LANGUAGE_CODE=='de'){
?>
#menu-item-wpml-ls-597-de:before {
    background-color: transparent !important;
	content: url(https://www.tutoribalto.com/wp-content/uploads/2023/05/MAPPAMONDO-new.svg);
	width:20px !important;
	margin-top:-12px;
}
.single-product-content .tabs li a {
    font-size: 18px!important;}
<?php
}
?>

 <?php if(ICL_LANGUAGE_CODE=='fr'){
?>
#menu-item-wpml-ls-597-fr:before {
    background-color: transparent !important;
	content: url(https://www.tutoribalto.com/wp-content/uploads/2023/05/MAPPAMONDO-new.svg);
	width:20px !important;
	margin-top:-12px;
}

<?php
}
?>

</style>

<script>

	
</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>><div id="subscriber_form" > 
 	 <div id="sub_div_form"> 
  	 	<span id="kv_form_close"> X </span>
 	 	 	<div class="chart_sz"></div>
 	 </div>
</div>
<?php if ( basel_needs_header() ): ?>
	<?php do_action( 'basel_after_body_open' ); ?>
	<?php 
		basel_header_block_mobile_nav(); 
		$cart_position = basel_get_opt( 'cart_position' );
		if( $cart_position == 'side' || ! basel_woocommerce_installed() ) {
			?>
				<div class="cart-widget-side">
					<div class="widget-heading">
						<h3 class="widget-title"><?php esc_html_e( 'Shopping cart', 'basel' ); ?></h3>
						<a href="#" class="widget-close"><?php esc_html_e( 'close', 'basel' ); ?></a>
					</div>
					<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
				</div>
			<?php
		}
	?>
<?php endif ?>
<div class="website-wrapper">
<?php if ( basel_needs_header() ): ?>
	<?php if( basel_get_opt( 'top-bar' ) ): ?>
		<div class="topbar-wrapp color-scheme-<?php echo esc_attr( basel_get_opt( 'top-bar-color' ) ); ?>">
			<div class="container">
				<div class="topbar-content">
					<div class="top-bar-left">
						
						<?php if( basel_get_opt( 'header_text' ) != '' ): ?>
							<?php echo do_shortcode( basel_get_opt( 'header_text' ) ); ?>
						<?php endif; ?>						
						
					</div>
					<div class="top-bar-right">
						<div class="topbar-menu">
							<?php 
								if( has_nav_menu( 'top-bar-menu' ) ) {
									wp_nav_menu(
										array(
											'theme_location' => 'top-bar-menu',
											'walker' => new BASEL_Mega_Menu_Walker()
										)
									);
								}
							 ?>
						</div>
					</div>
				</div>
			</div>
		</div> <!--END TOP HEADER-->
	<?php endif; ?>

	<?php 
		$header_class = 'main-header';
		$header = apply_filters( 'basel_header_design', basel_get_opt( 'header' ) );
		$header_bg = basel_get_opt( 'header_background' );
		$header_has_bg = ( ! empty($header_bg['background-color']) || ! empty($header_bg['background-image']));

		$header_class .= ( $header_has_bg ) ? ' header-has-bg' : ' header-has-no-bg';
		$header_class .= ' header-' . $header;
		$header_class .= ' icons-design-' . basel_get_opt( 'icons_design' );
		$header_class .= ' color-scheme-' . basel_get_opt( 'header_color_scheme' );
	?>
	<style> .optional {display: none;} </style>
	<!-- HEADER -->
	<header class="<?php echo esc_attr( $header_class ); ?>">

		<?php basel_generate_header( $header ); // location: inc/template-tags.php ?>
								<div class="german_open"></div><div class="france_open"></div><div class="uk_open"></div><div class="usa_open"></div><div class="spain_open"></div><div class="ca_open"></div><div class="ir_open"></div><div class="au_open"></div><div class="nz_open"></div>
	</header><!--END MAIN HEADER-->

	<div class="clear"></div>
	
	<?php basel_page_top_part(); ?>
<?php endif ?>
