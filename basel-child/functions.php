<?php



add_action( 'wp_enqueue_scripts', 'basel_child_enqueue_styles', 1000 );



function basel_child_enqueue_styles() {

	$version = basel_get_theme_info( 'Version' );

	

	if( basel_get_opt( 'minified_css' ) ) {

		wp_enqueue_style( 'basel-style', get_template_directory_uri() . '/style.min.css', array('bootstrap'), $version );

	} else {

		wp_enqueue_style( 'basel-style', get_template_directory_uri() . '/style.css', array('bootstrap'), $version );

	}

    wp_enqueue_script( 'script-theme', get_stylesheet_directory_uri() . '/assets/js/script.js?V='.time());
    wp_localize_script( 'script-theme', 'the_ajax_script', array(
        'ajax_url' => esc_js(admin_url( 'admin-ajax.php' )),
        'current_language' => ICL_LANGUAGE_CODE,
         ) );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array('bootstrap'), $version );

}

// **********************************************************************// 
// ! Header blocks search
// **********************************************************************// 

if( ! function_exists( 'basel_header_block_search' ) ) {
	function basel_header_block_search() {
		$header_search = basel_get_opt( 'header_search' );
		if( $header_search == 'disable' ) return;

		$classes = 'search-button';
		$classes .= ' basel-search-' . $header_search;
		if ( basel_get_opt( 'mobile_search_icon' ) ) $classes .= ' mobile-search-icon';
		?>


<div class="main-nav2 site-navigation basel-navigation menu-right" role="navigation" >
				
	<?php
wp_nav_menu( array( 
    'theme_location' => 'menu-destra', 
    'container_class' => 'menu-principale-container' ) ); 
?>
	</div>
	
			<div class="<?php echo esc_attr( $classes ); ?>"> 



				<a href="#">
					<i class="fa fa-search"></i>
				</a>
				<div class="basel-search-wrapper">
					<div class="basel-search-inner">
						<span class="basel-close-search"><?php esc_html_e('close', 'basel'); ?></span>
						<?php basel_header_block_search_extended( false, true, array('thumbnail' => 1, 'price' => 1), false ); ?>
					</div>
				</div>
			</div>
		<?php
	}
}


//New Menu

function wpb_custom_new_menu() {
  register_nav_menu('menu-destra',__( 'Menu di destra' ));
}
add_action( 'init', 'wpb_custom_new_menu' );


//Hide Price Range for WooCommerce Variable Products

add_filter( 'woocommerce_variable_sale_price_html', 

'lw_variable_product_price', 10, 2 );

add_filter( 'woocommerce_variable_price_html', 

'lw_variable_product_price', 10, 2 );


function lw_variable_product_price( $v_price, $v_product ) {

    if ( 'wwo_enable_wholesale_customer' === woo_get_enabled_user_role() ) {
        global $wpdb;
        $price = $v_price;
        $product = $v_product;
        $table_name = $wpdb->prefix . "options"; 	
        $results = $wpdb->get_results( 'SELECT * FROM ' . $table_name . ' WHERE option_value = "enable_role"' );

        foreach ( $results as $result ) {
            $ifwholesale = str_replace("wholesale_customer", "wholesale", $result->option_name);
            $finaldata = str_replace("wwo_enable_", "_", $ifwholesale) . '_price';
            $finalno = str_replace("wwo_enable_", "", $ifwholesale) . '_price';

            if ( $result->option_name === woo_get_enabled_user_role() ) {
                $variations = $product->get_available_variations();
                $lowestvar = array();
                $lowestnor = array();

                foreach ( $variations as $variation ) {
                    $lowestvar[] = get_post_meta( $variation['variation_id'], $finaldata, true );
                    $lowestnor[] = get_post_meta( $variation['variation_id'], '_price', true );
                }

                array_multisort( $lowestvar, SORT_ASC );
                array_multisort( $lowestnor, SORT_ASC );

                $lrrp = min($lowestnor);
                $hrrp = max($lowestnor);
                $lwrrp = min($lowestvar);
                $hwrrp = max($lowestvar);	

                $wwo_percentage       = get_option( 'wwo_percentage' );
                $wwo_savings          = get_option( 'wwo_savings_label' );
                $wwo_rrp              = get_option( 'wwo_rrp_label' );
                $wwo_wholesale_label  = get_option( 'wwo_wholesale_label' );

                // RRP (prezzo originale)
                $rrp_price = '';
                if ( $wwo_rrp !== '' ) {
                    if ( $lrrp == $hrrp ) {
                        $rrp_price = '<div class="woo_retail"><span class="woo_retail_label">' . $wwo_rrp . ':</span> <span class="woo_retail_price">' . wc_price( $lrrp ) . '</span></div>';
                    } else {
                        $rrp_price = '<div class="woo_retail"><span class="woo_retail_label">' . $wwo_rrp . ':</span> <span class="woo_retail_price">' . wc_price( $lrrp ) . ' - ' . wc_price( $hrrp ) . '</span></div>';
                    }
                }

                // Prezzo all'ingrosso
                if ( isset( $variation['variation_id'] ) && get_post_meta( $variation['variation_id'], $finaldata, true ) ) {
                    if ( $wwo_wholesale_label !== '' ) {
                        if ( $lwrrp == $hwrrp ) {
                            $price = $rrp_price . '<div class="woo_wholesale"><span class="woo_wholesale_label">' . $wwo_wholesale_label . ':</span> <span class="woo_wholesale_price">' . wc_price( $lwrrp ) . '</span></div>';
                        } else {
                            $price = $rrp_price . '<div class="woo_wholesale"><span class="woo_wholesale_label">' . $wwo_wholesale_label . ':</span> <span class="woo_wholesale_price">' . wc_price( $lwrrp ) . ' - ' . wc_price( $hwrrp ) . '</span></div>';
                        }
                    }
                }
            }
        }

        return $price;

    } else {
        // Prezzo per cliente standard (non wholesale)
        $prod_prices = array(
            $v_product->get_variation_price( 'min', true ),
            $v_product->get_variation_price( 'max', true )
        );

        $prod_price = $prod_prices[0] !== $prod_prices[1]
            ? sprintf( __( '<span class="from">a partire da</span> %1$s', 'woocommerce' ), wc_price( $prod_prices[0] ) )
            : wc_price( $prod_prices[0] );

        $regular_prices = array(
            $v_product->get_variation_regular_price( 'min', true ),
            $v_product->get_variation_regular_price( 'max', true )
        );

        sort( $regular_prices );
        $regular_price = $regular_prices[0] !== $regular_prices[1]
            ? sprintf( __( '<span class="from">a partire da</span> %1$s', 'woocommerce' ), wc_price( $regular_prices[0] ) )
            : wc_price( $regular_prices[0] );

        if ( $prod_price !== $regular_price ) {
            $prod_price = '<del>' . $regular_price . $v_product->get_price_suffix() . '</del> <ins>' . $prod_price . $v_product->get_price_suffix() . '</ins>';
        }

        return $prod_price;
    }
}



add_action( 'woocommerce_after_shop_loop_item_title', 'custom_field_display_below_title', 2 );

function custom_field_display_below_title(){

    global $product;



    // Get the custom field value

    $custom_field = get_field('testo_anteprima');



    // Display

    if( ! empty($custom_field) ){

        echo '<p class="sottotitoloop">'.$custom_field.'</p>';

    }

}
// shail customization 
add_action('woocommerce_checkout_process', 'validation_checkout');

function validation_checkout() { 
    $fiscal_number = $_POST['billing_fiscal'];
    $fiscal_repeat = $_POST['billing_fiscal_repeat'];
    $billing_country = $_POST['billing_country'];
 /* 
if($billing_country=='GB'){
        if(ICL_LANGUAGE_CODE=='en'){
            
            wc_add_notice( __( "Dear Customer,<br>
            to have these products en England, they must be purchased from the KVP dealer<br><br>
            
            KVP EU Limited - Unit 21 Horn Park Business Estate<br>
            Broadwindsor Road - Beaminster - Dorset - DT8 3PT - United Kingdom<br>
            phone: <a href='tel:+44(0) 1308 867020'>+44(0) 1308 867020</a> - fax: +44(0) 1308 800123<br>
            website: www.kvpvet.com<br>
            email (General): <a href='mailto:info@kvpeu.com'>info@kvpeu.com</a> - email (Sales): <a href='mailto:sales@kvpeu.com'>sales@kvpeu.com</a><br>
            <br>
" ), 'error' );
       
        }else if(ICL_LANGUAGE_CODE=='it'){
      
            wc_add_notice( __( "Gentile Cliente, <br>
            per avere questi prodotti in Inghilterra, devono essere acquistati dal rivenditore KVP <br> <br>
            
            KVP EU Limited - Unit 21 Horn Park Business Estate <br>
            Broadwindsor Road - Beaminster - Dorset - DT8 3PT - United Kingdom <br>
            phone: <a href='tel:+44(0) 1308 867020'>+44(0) 1308 867020</a> - fax: +44(0) 1308 800123 <br>
            website: www.kvpvet.com <br>
            email (General): <a href='mailto:info@kvpeu.com'>info@kvpeu.com</a> - email (Sales): <a href='mailto:sales@kvpeu.com'>sales@kvpeu.com</a> <br>
            
            <br>

" ), 'error' );
      
        }else if(ICL_LANGUAGE_CODE=='de'){
       
            wc_add_notice( __( "Sehr geehrter Kunde,<br>
            Um diese Produkte in England, zu beziehen müssen sie über den KVP Händler erworben werden<br><br>
            
            KVP EU Limited - Unit 21 Horn Park Business Estate<br>
            Broadwindsor Road - Beaminster - Dorset - DT8 3PT - United Kingdom<br>
            phone:  <a href='tel:+44(0) 1308 867020'>+44(0) 1308 867020</a> - fax: +44(0) 1308 800123<br>
            website: www.kvpvet.com<br>
            email (General): <a href='mailto:info@kvpeu.com'>info@kvpeu.com</a> - email (Sales): <a href='mailto:sales@kvpeu.com'>sales@kvpeu.com</a> <br>
            <br>

" ), 'error' );
       
        }else if(ICL_LANGUAGE_CODE=='es'){
      
            wc_add_notice( __( "Estimado cliente,<br>
            para tener estos productos en Inglaterra, deben comprarse al distribuidor KVP<br><br>
            
            KVP EU Limited - Unit 21 Horn Park Business Estate<br>
            Broadwindsor Road - Beaminster - Dorset - DT8 3PT - United Kingdom<br>
            phone:  <a href='tel:+44(0) 1308 867020'>+44(0) 1308 867020</a> - fax: +44(0) 1308 800123<br>
            website: www.kvpvet.com<br>
            email (General): <a href='mailto:info@kvpeu.com'>info@kvpeu.com</a> - email (Sales): <a href='mailto:sales@kvpeu.com'>sales@kvpeu.com</a> <br>
             <br>

" ), 'error' );
      
        }else if(ICL_LANGUAGE_CODE=='fr'){
      
            wc_add_notice( __( "Cher Client,<br>
            Pour avoir ces produits en Angleterre, il faut les acheter chez le revendeur KVP<br><br>
            
            KVP EU Limited - Unit 21 Horn Park Business Estate<br>
            Broadwindsor Road - Beaminster - Dorset - DT8 3PT - United Kingdom<br>
            phone: <a href='tel:+44(0) 1308 867020'>+44(0) 1308 867020</a> - fax: +44(0) 1308 800123<br>
            website: www.kvpvet.com<br>
            email (General): <a href='mailto:info@kvpeu.com'>info@kvpeu.com</a> - email (Sales): <a href='mailto:sales@kvpeu.com'>sales@kvpeu.com</a> <br>
            
            <br>

" ), 'error' );
      
        }
           
    }
else if($billing_country=='FR'){
    
           
            if(ICL_LANGUAGE_CODE=='it'){
          
                wc_add_notice( __( "Gentile Cliente,<br>
                per avere questi prodotti in Francia, devono essere acquistati dal rivenditore Mikan<br><br>
                
                Mikan, Usine Créative, parc Vendée Sud Loire 1<br>
                85600 Boufféré - France<br>
                phone:  <a href='tel:+33 (0)2 51 62 15 73'>+33 (0)2 51 62 15 73</a><br>
                website: www.mikan-vet.com<br>
                email: <a href='mailto:info@mikan-vet.com'>info@mikan-vet.com</a><br>
                
                <br>
    
    " ), 'error' );
          
            }else if(ICL_LANGUAGE_CODE=='de'){
           
                wc_add_notice( __( "Sehr geehrter Kunde,<br>
                Um diese Produkte aus Frankreich, zu beziehen müssen sie über den Mikan Händler erworben werden<br><br>
                
                Mikan, Usine Créative, parc Vendée Sud Loire 1<br>
                85600 Boufféré - France<br>
                phone:  <a href='tel:+33 (0)2 51 62 15 73'>+33 (0)2 51 62 15 73</a><br>
                website: www.mikan-vet.com<br>
                email: <a href='mailto:info@mikan-vet.com'>info@mikan-vet.com</a><br>
                <br><br>
    
    " ), 'error' );
           
            }else if(ICL_LANGUAGE_CODE=='es'){
          
                wc_add_notice( __( "Estimado cliente,<br>
                para tener estos productos en Francia, deben comprarse al distribuidor Mikan<br><br>
                
                Mikan, Usine Créative, parc Vendée Sud Loire 1<br>
                85600 Boufféré - France<br>
                phone:  <a href='tel:+33 (0)2 51 62 15 73'>+33 (0)2 51 62 15 73</a><br>
                website: www.mikan-vet.com<br>
                email: <a href='mailto:info@mikan-vet.com'>info@mikan-vet.com</a><br>
                <br>
    
    " ), 'error' );
          
            }else if(ICL_LANGUAGE_CODE=='fr'){
          
                wc_add_notice( __( "Cher Client,<br>
                pour avoir ces produits de France, il faut les acheter chez le revendeur Mikan<br><br>
                
                Mikan, Usine Créative, parc Vendée Sud Loire 1<br>
                85600 Boufféré - France<br>
                phone: <a href='tel:+33 (0)2 51 62 15 73'>+33 (0)2 51 62 15 73</a><br>
                website: www.mikan-vet.com<br>
                email: <a href='mailto:info@mikan-vet.com'>info@mikan-vet.com</a><br>
                
                <br>
    
    " ), 'error' );
          
            }else if(ICL_LANGUAGE_CODE=='en'){
          
                wc_add_notice( __( "Dear Customer,<br>
                to have these products in France, they must be purchased from the Mikan dealer<br><br>
                
                Mikan, Usine Créative, parc Vendée Sud Loire 1<br>
                85600 Boufféré - France<br>
                phone: <a href='tel:+33 (0)2 51 62 15 73'>+33 (0)2 51 62 15 73</a><br>
                website: www.mikan-vet.com<br>
                email: <a href='mailto:info@mikan-vet.com'>info@mikan-vet.com</a><br>
                
                <br>
    
    " ), 'error' );
          
            }
    } 
    else */
    if($billing_country=='US'){
    
           
            if(ICL_LANGUAGE_CODE=='it'){
          
                wc_add_notice( __( "Gentile Cliente,<br>
                per avere questi prodotti negli Stati Uniti, devono essere acquistati dal rivenditore Balto USA<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>
                
                <br>
    
    " ), 'error' );
          
            }else if(ICL_LANGUAGE_CODE=='de'){
           
                wc_add_notice( __( "Sehr geehrter Kunde,<br>
                Um diese Produkte aus Vereinigte Staaten, zu beziehen müssen sie über den Balto USA Händler erworben werden<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>                
                <br>
    
    " ), 'error' );
           
            }else if(ICL_LANGUAGE_CODE=='es'){
          
                wc_add_notice( __( "Estimado cliente,<br>
                para tener estos productos en Estados Unidos, deben comprarse al distribuidor Balto USA<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>                
                <br>
    
    " ), 'error' );
          
            }else if(ICL_LANGUAGE_CODE=='fr'){
          
                wc_add_notice( __( "Cher Client,<br>
                pour avoir ces produits de États-Unis, il faut les acheter chez le revendeur Balto USA<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>                
                <br>
    
    " ), 'error' );
          
            }else if(ICL_LANGUAGE_CODE=='en'){
          
                wc_add_notice( __( "Dear Customer,<br>
                to have these products in United States, they must be purchased from the Balto USA dealer<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>                
                
                <br>
    
    " ), 'error' );
          
            }
    }  elseif($billing_country=='MT'){
        
       
                if(ICL_LANGUAGE_CODE!='in'){
              
                    wc_add_notice( __( "Dear Customer, <br>
                   To have these products from Malta, they must be purchased from the Borg Cardona dealer

                    BORG CARDONA & CO.LTD.<br>
                     ELTEX, DR ZAMMIT STREET - Balzanbzn1434 - MALTA<br>
                     phone: +356 21442698 ext.451 - mobile: +356 99434570<br>
                     website: <a href='https://www.borgcardona.com.mt' target='_blank'>www.borgcardona.com.mt</a><br>
                     email: sales@borgcardona.com.mt
                    
                    <br>
        
        " ), 'error' );
    
        }
    
      }elseif($billing_country=='CA'){
      
        if(ICL_LANGUAGE_CODE=='it'){
          
                wc_add_notice( __( "Gentile Cliente,<br>
                per avere questi prodotti in Canada, devono essere acquistati dal rivenditore Balto Canada<br><br>
                
<a href='https://baltocanada.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>https://baltocanada.com</a>
                
                <br>
    
    " ), 'error' );
          
            }elseif(ICL_LANGUAGE_CODE!='it'){
              
                  wc_add_notice( __( "Dear Customer,<br>
                to have these products in Canada, they must be purchased from the Balto Canada dealer<br><br>
                
<a href='https://baltocanada.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>https://baltocanada.com</a>                
                
                <br>
    
    " ), 'error' );
    
        }
    
      }elseif($billing_country=='IE'){
        
       
        if(ICL_LANGUAGE_CODE=='it'){
          
                wc_add_notice( __( "Gentile Cliente,<br>
                per avere questi prodotti in Irlanda, devono essere acquistati dal rivenditore Balto Canada<br><br>
                
                
                <br>
    
    " ), 'error' );
          
            }elseif(ICL_LANGUAGE_CODE!='it'){
              
                   wc_add_notice( __( "Dear Customer,<br>
                to have these products in Ireland, they must be purchased from the Balto Canada dealer<br><br>
                     
                
                <br>
    
    " ), 'error' );
    
        }
    
      }elseif($billing_country=='GB'){
        
       
        if(ICL_LANGUAGE_CODE=='it'){
          
                wc_add_notice( __( "Gentile Cliente,<br>
                per avere questi prodotti nel Regno Unito, devono essere acquistati dal rivenditore Balto UK<br><br>
                
<a href='https://baltouk.co.uk/' style='text-decoration:underline;font-size:22px;' target='_blank'>https://baltouk.co.uk</a>
                
                <br>
    
    " ), 'error' );
          
            }elseif(ICL_LANGUAGE_CODE!='it'){
              
               wc_add_notice( __( "Dear Customer,<br>
                to have these products in United Kingdom, they must be purchased from the Balto UK dealer<br><br>
                
<a href='https://baltouk.co.uk/' style='text-decoration:underline;font-size:22px;' target='_blank'>https://baltouk.co.uk</a>                
                
                <br>
    
    " ), 'error' );
    
        }
    
      }elseif($billing_country=='NZ'){
        
       
        if(ICL_LANGUAGE_CODE=='it'){
          
                wc_add_notice( __( "Gentile Cliente,<br>
                per avere questi prodotti in Nuova Zelanda, devono essere acquistati dal rivenditore KVP<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>
                
                <br>
    
    " ), 'error' );
          
            }elseif(ICL_LANGUAGE_CODE!='it'){
              
                     wc_add_notice( __( "Dear Customer,<br>
                to have these products in New Zeland, they must be purchased from the KVP dealer<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>                
                
                <br>
    
    " ), 'error' );
    
        }
    
      }
    
    
    /*
    
      elseif($billing_country=='DE'){
        
       
                if(ICL_LANGUAGE_CODE=='it'){
              
                    wc_add_notice( __( "Gentile Cliente, <br>
                    per avere questi prodotti dalla Germania,  <br>devono essere acquistati dal rivenditore PET PHYSIO <br> <br>
                    
                    PET PHYSIO GmbH - Nikolaus-Otto-Str. 4 - Halle 8 <br>
                    40721 Hilden - Germany <br>
                    phone: <a href='tel:+49 (0)800 07 22222'>+49 (0)800 07 22222 </a><br>
                    website: www.petphysio-shop.de <br>
                    email:<a href='mailto:kundendienst@pet-physio.de'> kundendienst@pet-physio.de</a> <br>
                    
                    <br>
        
        " ), 'error' );
              
                }else if(ICL_LANGUAGE_CODE=='de'){
               
                    wc_add_notice( __( "Sehr geehrter Kunde, <br>
                    um diese Produkte aus Deutschland, zu beziehen <br>
                    müssen sie über den PetPhisio Händler erworben werden <br> <br>
                    
                    PET PHYSIO GmbH - Nikolaus-Otto-Str. 4 - Halle 8 <br>
                    40721 Hilden - Germany <br>
                    phone: <a href='tel:+49 (0)800 07 22222'>+49 (0)800 07 22222 </a><br>
                    website: www.petphysio-shop.de <br>
                    email:<a href='mailto:kundendienst@pet-physio.de'> kundendienst@pet-physio.de</a> <br>
                   
                    <br>
        
        " ), 'error' );
               
                }else if(ICL_LANGUAGE_CODE=='es'){
              
                    wc_add_notice( __( "Estimado cliente, <br>
                    para tener estos productos en Alemania, <br>
                    deben comprarse al distribuidor PET PHYSIO <br> <br>
                    
                    PET PHYSIO GmbH - Nikolaus-Otto-Str. 4 - Halle 8 <br>
                    40721 Hilden - Germany <br>
                    phone: <a href='tel:+49 (0)800 07 22222'>+49 (0)800 07 22222 </a><br>
                    website: www.petphysio-shop.de <br>
                    email:<a href='mailto:kundendienst@pet-physio.de'> kundendienst@pet-physio.de</a> <br>
                    <br>
        
        " ), 'error' );
              
                }else if(ICL_LANGUAGE_CODE=='fr'){
              
                    wc_add_notice( __( "Cher Client,<br>
                    Pour avoir ces produits de Alemania, il faut les acheter chez le revendeur PET PHYSIO<br><br>
                    
                    PET PHYSIO GmbH - Nikolaus-Otto-Str. 4 - Halle 8<br>
                    40721 Hilden - Germany<br>
                    phone: <a href='tel:+49 (0)800 07 22222'>+49 (0)800 07 22222</a><br>
                    website: www.petphysio-shop.de<br>
                    email: <a href='mailto:kundendienst@pet-physio.de'>kundendienst@pet-physio.de</a><br>
                     

                    <br>
        
        " ), 'error' );
              
                }else if(ICL_LANGUAGE_CODE=='en'){
              
                    wc_add_notice( __( "Dear Customer,<br>
                    To have these products from Germany, they must be purchased from the PET PHYSIO dealer<br><br>
                    
                    PET PHYSIO GmbH - Nikolaus-Otto-Str. 4 - Halle 8<br>
                    40721 Hilden - Germany<br>
                    phone: <a href='tel:+49 (0)800 07 22222'>+49 (0)800 07 22222</a><br>
                    website: www.petphysio-shop.de<br>
                    email: <a href='mailto:kundendienst@pet-physio.de'>kundendienst@pet-physio.de</a><br>
                    
                    <br>
        
        " ), 'error' );
              
                }



    }*/
    $rdatapp=str_split($fiscal_number);
    $vsdata = $rdatapp[6].$rdatapp[7];
    if ($_POST['billing_email'] !=$_POST['billing_verify_email'] ) {
        wc_add_notice( __( 'Email and Confirm Email fields must be same' ), 'error' );
    }
        if ($_POST['billing_country'] == "IT")
        {
            if ($_POST['billing_client_type'] == "corp") {
               // if (empty(trim($fiscal_number))) 
              //  {
             //       wc_add_notice( __( 'Codice fiscale campo vuoto' ), 'error' );
             //   }else if($fiscal_number!=$fiscal_repeat){
             //       wc_add_notice( __( 'Il campo Codice fiscale deve essere ripetuto uguale' ), 'error' );
             //   }  
             //   else if ((strlen(trim($fiscal_number)) != 16 || !is_numeric($vsdata)) && !empty(trim($fiscal_number))) {
             //       wc_add_notice( __( 'Ccodice fiscale errato' ), 'error' );
             //   }
    
                $rs = $_POST['billing_rs'];
                if (empty(trim($rs))) 
                {
                    wc_add_notice( __( 'Ragione Sociale campo vuoto' ), 'error' );
                }
                $PEC = $_POST['billing_pec'];
                if (empty(trim($PEC))) 
                {
                    wc_add_notice( __( 'P.E.C. campo vuoto' ), 'error' );
                }
                 $billing_Codice_SDI = $_POST['billing_codice_sdi'];
                if (empty(trim($billing_Codice_SDI))) 
                {
                    wc_add_notice( __( 'Codice SDI campo vuoto' ), 'error' );
                }
                $piva = $_POST['billing_piva'];
                if (empty(trim($piva))) 
                {
                    wc_add_notice( __( 'Partita IVA campo vuoto' ), 'error' );
                }
            }
            else {
                if (empty(trim($fiscal_number))) 
                {
                    wc_add_notice( __( 'Codice fiscale campo vuoto' ), 'error' );
                }else if($fiscal_number!=$fiscal_repeat){
                    wc_add_notice( __( 'Il campo Codice fiscale deve essere ripetuto uguale' ), 'error' );
                }  
                else if ((strlen(trim($fiscal_number)) != 16 || !is_numeric($vsdata)) && !empty(trim($fiscal_number))) {
                    wc_add_notice( __( 'Ccodice fiscale errato' ), 'error' );
                }
            }
    
        }


  }
add_filter('woocommerce_billing_fields', 'custom_woocommerce_billing_fields');
function custom_woocommerce_billing_fields($fields)
{
	//if(ICL_LANGUAGE_CODE=='it'){
        
	$fields['billing_Codice_SDI'] = array(
        'label' => __('Codice SDI ', 'woocommerce'), // Add custom field label
        'placeholder' => _x('Codice SDI', 'placeholder', 'woocommerce'), // Add custom field placeholder
        'required' => false, // if field is required or not
        'clear' => false, // add clear or not
        'type' => 'text', // add field type
        'class' => array('my-css')    // add class name
    );
    $fields['billing_PEC'] = array(
        'label' => __('P.E.C. ', 'woocommerce'), // Add custom field label
        'placeholder' => _x('P.E.C.', 'placeholder', 'woocommerce'), // Add custom field placeholder
        'required' => false, // if field is required or not
        'clear' => false, // add clear or not
        'type' => 'text', // add field type
        'class' => array('my-css')    // add class name
    );	
	//}
    
    return $fields;
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'misha_editable_order_meta_billing' );    
function misha_editable_order_meta_billing( $order ){
  $billing_Codice_SDI = get_post_meta( $order->id, '_billing_Codice_SDI', true );
  $billing_PEC = get_post_meta( $order->id, '_billing_PEC', true );
  if($billing_Codice_SDI!=''){ 
  ?>
<div class="address">
    <p>
      <strong>Codice SDI:</strong>
      <?php echo $billing_Codice_SDI; ?>
    </p>
  </div>
  <?php
}
if($billing_PEC!=''){ 
  ?>
<div class="address">
    <p>
      <strong>PEC:</strong>
      <?php echo $billing_PEC; ?>
    </p>
  </div>
  <?php
}
  }


//add_action( 'woocommerce_created_customer', 'sww_approve_customer_user' );

// 1) registra l'hook solo se la funzione esiste, altrimenti usa uno shim
if ( function_exists( 'sww_approve_customer_user' ) ) {
    add_action( 'woocommerce_created_customer', 'sww_approve_customer_user', 10, 3 );
} else {
    add_action( 'woocommerce_created_customer', 'tutoribalto_wholesale_after_customer_created', 10, 3 );
}

/**
 * Shim compatibile: mantiene in vita il flusso wholesale anche se la vecchia sww_approve_customer_user non esiste più.
 * Non genera fatal e puoi personalizzare la logica sotto.
 */
function tutoribalto_wholesale_after_customer_created( $customer_id, $new_customer_data, $password_generated ) {

    // Riconosciamo se la registrazione è “wholesale” (adatta alla tua form):
    $is_wholesale =
        ( isset($_POST['account_type']) && $_POST['account_type'] === 'wholesale' ) ||
        ( isset($_POST['is_vet']) && (int) $_POST['is_vet'] === 1 ) ||
        ( isset($_POST['wholesale']) && $_POST['wholesale'] === '1' );

    if ( ! $is_wholesale ) {
        // Registrazione standard: non fare nulla di speciale.
        return;
    }

    // 1) Se hai un ruolo wholesale (es. “wholesale_customer”), assegnalo se esiste:
    $role = 'wholesale_customer'; // <-- metti il tuo ruolo reale qui
    if ( get_role( $role ) ) {
        $user = new WP_User( $customer_id );
        $user->set_role( $role );
    }

    // 2) Imposta meta “in approvazione” per gestire il flusso (se ti serve)
    update_user_meta( $customer_id, 'wholesale_pending', 1 );

    // 3) Notifica admin (opzionale)
    $admin_email = get_option( 'admin_email' );
    if ( $admin_email ) {
        $msg  = "Nuova registrazione wholesale (veterinario)\n\n";
        $msg .= "ID utente: {$customer_id}\n";
        $msg .= "Email: " . ( $new_customer_data['user_email'] ?? '' ) . "\n";
        $msg .= "Username: " . ( $new_customer_data['user_login'] ?? '' ) . "\n";
        wp_mail( $admin_email, 'Nuova richiesta wholesale', $msg );
    }

    // 4) Agganci interni per ulteriore logica custom
    do_action( 'tutoribalto_wholesale_marked_pending', $customer_id, $new_customer_data, $password_generated );
}


function zk_add_billing_form_to_registration(){
  global $woocommerce;
  $checkout = $woocommerce->checkout();
  ?>
  <?php foreach ( $checkout->get_checkout_fields( 'billing' ) as $key => $field ) : ?>

      <?php if($key!='billing_email'){ 
        if($key=='billing_client_type'){
          // $field['options'] = array_shift($field['options']);
          $field['options']=array();
          $field['options']['corp']='Azienda';
           //print_r($field['options']);
        }
      
          woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
      } ?>

  <?php endforeach; ?>
   
    <p  class="form-row form-row-wide validate-required" id="billing_numero_albo_veterinari_field" data-priority="" style=""><label for="billing_PEC" class=""> Numero albo veterinari <span class="required" >*</span></label><input type="text" class="input-text " name="billing_numero_albo_veterinari" id="billing_numero_albo_veterinari" placeholder="Numero albo veterinari" value=""></p>
    <p  class="form-row form-row-wide validate-required" id="billing_provincia_alb" data-priority="" style=""><label for="billing_provincia_alb" class=""> Provincia albo <span class="required" >*</span></label><input type="text" class="input-text " name="billing_provincia_alb" id="billing_provincia_alb" placeholder="Provincia albo" value=""></p>
     <style type="text/css">#billing_ctasse_field { display: none; }</style>
  <?php
}
add_action('woocommerce_register_form_start','zk_add_billing_form_to_registration');
function startsWith ($string, $startString) 
{ 
    $len = strlen($startString); 
    return (substr($string, 0, $len) === $startString); 
} 
add_action ( 'wp_head', 'hook_inHeader' );
function hook_inHeader() {
  if(ICL_LANGUAGE_CODE=='it'){
    ?>
    <style>
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

/**
function register_scripts() {
  if ( !is_admin() ) {
    // include your script
    wp_enqueue_script( 'email-confirm', get_bloginfo( 'template_url' ) . '/js/email-confirm.js' );
  }
}
add_action( 'wp_enqueue_scripts', 'register_scripts' );
 */
 
/**
 * @snippet       Remove Add Cart, Add View Product @ WooCommerce Loop
 * @how-to        Get CustomizeWoo.com FREE
 * @author        Rodolfo Melogli
 * @testedwith    WooCommerce 3.6.2
 * @donate $9     https://businessbloomer.com/bloomer-armada/
 */
  
// First, remove Add to Cart Button
  
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
  
// Second, add View Product Button
  
add_action( 'woocommerce_after_shop_loop_item', 'bbloomer_view_product_button', 10 );
  
function bbloomer_view_product_button() {
global $product;
$button_text = __('View Product', 'woocommerce');
$link = $product->get_permalink();
echo '<a href="' . $link . '" class="button addtocartbutton">' .__( 'Select options', 'woocommerce' ). '</a>';
}

 /* 
add_action('woocommerce_after_add_to_cart_button','cmk_additional_button');
function cmk_additional_button() {
   if(ICL_LANGUAGE_CODE=='en'){
    echo '<div class="c_text"><br><br><p>Are you in: <a class="uk_open" href="">England</a>, <a href="" class="france_open">France</a>, <a class="german_open" href="">Germany</a> ?</p></div>';
   }else if(ICL_LANGUAGE_CODE=='it'){
    echo '<div class="c_text"><br><br><p>Ti trovi in: <a class="uk_open" href="">Inghilterra</a>, <a href="" class="france_open">Francia</a>, <a class="german_open" href="">Germania</a> ?</p></div>';
   }else if(ICL_LANGUAGE_CODE=='de'){
    echo '<div class="c_text"><br><br><p>Bist du in: <a class="uk_open" href="">England</a>, <a href="" class="france_open">Frankreich</a>, <a class="german_open" href="">Deutschland</a> ?</p></div>';
   }else if(ICL_LANGUAGE_CODE=='es'){
    echo '<div class="c_text"><br><br><p>Estás en: <a class="uk_open" href="">Inglaterra</a>, <a href="" class="france_open">Francia</a>, <a class="german_open" href="">Alemania</a> ?</p></div>';
   }else if(ICL_LANGUAGE_CODE=='fr'){
    echo '<div class="c_text"><br><br><p>Es-tu en: <a class="uk_open" href="">Angleterre</a>, <a href="" class="france_open">France</a>, <a class="german_open" href="">Allemagne </a> ?</p></div>';
   }
  //  echo '<a href="'.$yourCustomLinkValue.'" target="_blank">Buy on Kindle</a>';    
}

 /* 

add_action('woocommerce_after_add_to_cart_button','cmz_additional_button');
function cmz_additional_button() {
if( is_single(32886) ){
    echo '<div class="y_text"><p style="display: block; margin-top: -20px; margin-bottom: 15px; font-weight: 700;font-size: 15px;" class="discountita">10% discount applied by purchasing 3 or more boxes 
(the price is updated on the cart)</p></div>';
   }
   }


   add_action('woocommerce_after_add_to_cart_button','cmo_additional_button');
function cmo_additional_button() {
if( is_single(32646) ){
    echo '<div class="y_text"><p style="display: block; margin-top: -20px; margin-bottom: 15px; font-weight: 700;font-size: 15px;" class="discounteng">Acquistando 3 o più scatole riceverai un sconto del 10% (il prezzo si aggiorna sul carrello)</p></div>';
   }
   }

   add_action('woocommerce_after_add_to_cart_button','cmw_additional_button');
function cmw_additional_button() {
if( is_single(32961) ){
    echo '<div class="y_text"><p style="display: block; margin-top: -20px; margin-bottom: 15px; font-weight: 700;font-size: 15px;" class="discountita">10% discount applied by purchasing 3 or more boxes 
(the price is updated on the cart)</p></div>';
   }
   }


   add_action('woocommerce_after_add_to_cart_button','cmq_additional_button');
function cmq_additional_button() {
if( is_single(32962) ){
    echo '<div class="y_text"><p style="display: block; margin-top: -20px; margin-bottom: 15px; font-weight: 700;font-size: 15px;" class="discountita">10% discount applied by purchasing 3 or more boxes 
(the price is updated on the cart)</p></div>';
   }
   }



   add_action('woocommerce_after_add_to_cart_button','cmx_additional_button');
function cmx_additional_button() {
if( is_single(32963) ){
    echo '<div class="y_text"><p style="display: block; margin-top: -20px; margin-bottom: 15px; font-weight: 700;font-size: 15px;" class="discountita">10% discount applied by purchasing 3 or more boxes 
(the price is updated on the cart)</p></div>';
   }
   } */
 

   function wpsites_footer_gallery() {

    ?>
   <style>
    .sizes_tab { display:none !important; }
    .groessen_tab { display:none !important; }
    .tailles_tab  { display:none !important; }
    .tabla-de-tallas_tab  { display:none !important; }
    .woocommerce-Tabs-panel--tabla-de-tallas  { display:none !important; }
    .woocommerce-Tabs-panel--sizes  { display:none !important; }
    .woocommerce-Tabs-panel--groessen  { display:none !important; }
    .woocommerce-Tabs-panel--tailles  { display:none !important; }
   
 </style>
    <script>
          jQuery(document).ready(function() {

<?php if(ICL_LANGUAGE_CODE=='en'){
?> jQuery('.chart_sz').append(jQuery('.woocommerce-Tabs-panel--sizes').html()); <?php 
 }else  if(ICL_LANGUAGE_CODE=='de'){
    ?> jQuery('.chart_sz').append(jQuery('.woocommerce-Tabs-panel--groessen').html()); <?php 
  }else  if(ICL_LANGUAGE_CODE=='fr'){
    ?> jQuery('.chart_sz').append(jQuery('.woocommerce-Tabs-panel--tailles').html()); <?php 
   }else  if(ICL_LANGUAGE_CODE=='es'){
    ?> jQuery('.chart_sz').append(jQuery('.woocommerce-Tabs-panel--tabla-de-tallas').html()); <?php 
   } ?>
const languageToggle = document.querySelector(".js-wpml-ls-item-toggle");
const languageList = document.querySelector(".wpml-ls-sub-menu");

languageToggle.addEventListener("click", function() {
    console.log('language change');
    if (!jQuery('.wpml-ls-current-language ul').hasClass('wpml-ls-sub-menu')) {
        jQuery('.wpml-ls-current-language ul').removeClass('color-scheme-dark sub-menu');
        jQuery('.wpml-ls-current-language ul').addClass('wpml-ls-sub-menu');
    } else {
        languageList.classList.toggle("color-scheme-dark");
        languageList.classList.toggle("sub-menu");
        jQuery('.wpml-ls-current-language ul').removeClass('wpml-ls-sub-menu');
    }
});
});
   function legfilter(){
   var fiveLi = jQuery("ul.products-tabs-title li:eq(4)");
   fiveLi.trigger("click");
   }
      
   function headfilter(){
   var secondLi = jQuery("ul.products-tabs-title li:eq(1)");
   secondLi.trigger("click");
   }
       
   function frontlegfilter(){
   var fourLi = jQuery("ul.products-tabs-title li:eq(3)");
   fourLi.trigger("click");
   }
   
   function chestfilter(){
   var thirdLi = jQuery("ul.products-tabs-title li:eq(2)");
   thirdLi.trigger("click");
   }
  
   function showinformation(){ 
    if (jQuery("#slider-41-slide-386-layer-18").length > 0) {
     jQuery("#slider-41-slide-386-layer-18").prop("style").setProperty("opacity", "1", "important");
     jQuery("#slider-41-slide-386-layer-18").prop("style").setProperty("opacity", "1", "important");
     jQuery("#slider-41-slide-386-layer-18").prop("style").setProperty("opacity", "1", "important");
     jQuery("#slider-41-slide-386-layer-18").prop("style").setProperty("opacity", "1", "important");
     jQuery("#slider-41-slide-386-layer-18").prop("style").setProperty("opacity", "1", "important");
     jQuery("#slider-41-slide-386-layer-18").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-42-slide-391-layer-18").length > 0) {
    jQuery("#slider-42-slide-391-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-18").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-43-slide-396-layer-18").length > 0) { 

    jQuery("#slider-43-slide-396-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-18").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-45-slide-406-layer-18").length > 0) { 
    jQuery("#slider-45-slide-406-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-18").prop("style").setProperty("opacity", "1", "important");
    }

    if (jQuery("#slider-44-slide-401-layer-18").length > 0) {  
    jQuery("#slider-44-slide-401-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-18").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-18").prop("style").setProperty("opacity", "1", "important");
    }




}
function hideinformation(){
    if (jQuery("#slider-41-slide-386-layer-18").length > 0) {  jQuery("#slider-41-slide-386-layer-18").prop("style").setProperty("opacity", "0", "important"); }
        if (jQuery("#slider-42-slide-391-layer-18").length > 0) {   jQuery("#slider-42-slide-391-layer-18").prop("style").setProperty("opacity", "0", "important"); }
         if (jQuery("#slider-44-slide-401-layer-18").length > 0) {   jQuery("#slider-44-slide-401-layer-18").prop("style").setProperty("opacity", "0", "important"); }
        if (jQuery("#slider-43-slide-396-layer-18").length > 0) {    jQuery("#slider-43-slide-396-layer-18").prop("style").setProperty("opacity", "0", "important"); }
        if (jQuery("#slider-45-slide-406-layer-18").length > 0) {   jQuery("#slider-45-slide-406-layer-18").prop("style").setProperty("opacity", "0", "important"); }
}
function showheadinfo(){
    if (jQuery("#slider-41-slide-386-layer-10").length > 0) {
     jQuery("#slider-41-slide-386-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-41-slide-386-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-41-slide-386-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-10").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-42-slide-391-layer-10").length > 0) {
    jQuery("#slider-42-slide-391-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-42-slide-391-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-42-slide-391-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-10").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-44-slide-401-layer-10").length > 0) {
    jQuery("#slider-44-slide-401-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-44-slide-401-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-44-slide-401-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-10").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-43-slide-396-layer-10").length > 0) {
    jQuery("#slider-43-slide-396-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-43-slide-396-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-43-slide-396-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-10").prop("style").setProperty("opacity", "1", "important");
    } if (jQuery("#slider-45-slide-406-layer-10").length > 0) {
    jQuery("#slider-45-slide-406-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-45-slide-406-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-45-slide-406-layer-10").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-10").prop("style").setProperty("opacity", "1", "important");

    }

}
function hideheadinfo(){
    if (jQuery("#slider-41-slide-386-layer-10").length > 0) {     jQuery("#slider-41-slide-386-layer-10").prop("style").setProperty("opacity", "0", "important"); }
    if (jQuery("#slider-42-slide-391-layer-10").length > 0) {  jQuery("#slider-42-slide-391-layer-10").prop("style").setProperty("opacity", "0", "important"); }
    if (jQuery("#slider-44-slide-401-layer-10").length > 0) {  jQuery("#slider-44-slide-401-layer-10").prop("style").setProperty("opacity", "0", "important"); }
   if (jQuery("#slider-43-slide-396-layer-10").length > 0) {  jQuery("#slider-43-slide-396-layer-10").prop("style").setProperty("opacity", "0", "important"); }
 if (jQuery("#slider-45-slide-406-layer-10").length > 0) {  jQuery("#slider-45-slide-406-layer-10").prop("style").setProperty("opacity", "0", "important"); }
}
function showbodyinfo(){
    if (jQuery("#slider-41-slide-386-layer-19").length > 0) {  
          jQuery("#slider-41-slide-386-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-19").prop("style").setProperty("opacity", "1", "important");
    } 
    if (jQuery("#slider-42-slide-391-layer-19").length > 0) { 
    jQuery("#slider-42-slide-391-layer-19").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-42-slide-391-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-19").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-42-slide-391-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-42-slide-391-layer-19").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-44-slide-401-layer-19").length > 0) {
    jQuery("#slider-44-slide-401-layer-19").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-44-slide-401-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-19").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-44-slide-401-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-44-slide-401-layer-19").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-43-slide-396-layer-19").length > 0) {
    jQuery("#slider-43-slide-396-layer-19").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-43-slide-396-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-19").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-43-slide-396-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-43-slide-396-layer-19").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-45-slide-406-layer-19").length > 0) {
    jQuery("#slider-45-slide-406-layer-19").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-45-slide-406-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-19").prop("style").setProperty("opacity", "1", "important"); 
    jQuery("#slider-45-slide-406-layer-19").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-45-slide-406-layer-19").prop("style").setProperty("opacity", "1", "important");
    }
}
function hidebodyinfo(){
    if (jQuery("#slider-41-slide-386-layer-19").length > 0) {   jQuery("#slider-41-slide-386-layer-19").prop("style").setProperty("opacity", "0", "important"); }
    if (jQuery("#slider-42-slide-391-layer-19").length > 0) {   jQuery("#slider-42-slide-391-layer-19").prop("style").setProperty("opacity", "0", "important"); } 
    if (jQuery("#slider-44-slide-401-layer-19").length > 0) { jQuery("#slider-44-slide-401-layer-19").prop("style").setProperty("opacity", "0", "important"); }
if (jQuery("#slider-43-slide-396-layer-19").length > 0) {jQuery("#slider-43-slide-396-layer-19").prop("style").setProperty("opacity", "0", "important"); }
if (jQuery("#slider-45-slide-406-layer-19").length > 0) { jQuery("#slider-45-slide-406-layer-19").prop("style").setProperty("opacity", "0", "important"); }
}
function backleginfoshow(){
    if (jQuery("#slider-41-slide-386-layer-20").length > 0) {
    jQuery("#slider-41-slide-386-layer-20").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-20").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-20").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-20").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-20").prop("style").setProperty("opacity", "1", "important");
    jQuery("#slider-41-slide-386-layer-20").prop("style").setProperty("opacity", "1", "important");
    }
    if (jQuery("#slider-42-slide-391-layer-20").length > 0) {
    jQuery("#slider-42-slide-391-layer-20").prop("style").setProperty("opacity", "1", "important"); 
jQuery("#slider-42-slide-391-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-42-slide-391-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-42-slide-391-layer-20").prop("style").setProperty("opacity", "1", "important"); 
jQuery("#slider-42-slide-391-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-42-slide-391-layer-20").prop("style").setProperty("opacity", "1", "important");
    }
if (jQuery("#slider-44-slide-401-layer-20").length > 0) {
jQuery("#slider-44-slide-401-layer-20").prop("style").setProperty("opacity", "1", "important"); 
jQuery("#slider-44-slide-401-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-44-slide-401-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-44-slide-401-layer-20").prop("style").setProperty("opacity", "1", "important"); 
jQuery("#slider-44-slide-401-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-44-slide-401-layer-20").prop("style").setProperty("opacity", "1", "important");
}
if (jQuery("#slider-43-slide-396-layer-20").length > 0) {
jQuery("#slider-43-slide-396-layer-20").prop("style").setProperty("opacity", "1", "important"); 
jQuery("#slider-43-slide-396-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-43-slide-396-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-43-slide-396-layer-20").prop("style").setProperty("opacity", "1", "important"); 
jQuery("#slider-43-slide-396-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-43-slide-396-layer-20").prop("style").setProperty("opacity", "1", "important");
}
if (jQuery("#slider-45-slide-406-layer-20").length > 0) {
jQuery("#slider-45-slide-406-layer-20").prop("style").setProperty("opacity", "1", "important"); 
jQuery("#slider-45-slide-406-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-45-slide-406-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-45-slide-406-layer-20").prop("style").setProperty("opacity", "1", "important"); 
jQuery("#slider-45-slide-406-layer-20").prop("style").setProperty("opacity", "1", "important");
jQuery("#slider-45-slide-406-layer-20").prop("style").setProperty("opacity", "1", "important");
}
    
}
function backleginfohide(){
    if (jQuery("#slider-41-slide-386-layer-20").length > 0) {
        jQuery("#slider-41-slide-386-layer-20").prop("style").setProperty("opacity", "0", "important");
    }
    if (jQuery("#slider-42-slide-391-layer-20").length > 0) {
        jQuery("#slider-42-slide-391-layer-20").prop("style").setProperty("opacity", "0", "important");
    }
    if (jQuery("#slider-44-slide-401-layer-20").length > 0) {
        jQuery("#slider-44-slide-401-layer-20").prop("style").setProperty("opacity", "0", "important");
    }
    if (jQuery("#slider-43-slide-396-layer-20").length > 0) {
        jQuery("#slider-43-slide-396-layer-20").prop("style").setProperty("opacity", "0", "important");
    }
    if (jQuery("#slider-45-slide-406-layer-20").length > 0) {
        jQuery("#slider-45-slide-406-layer-20").prop("style").setProperty("opacity", "0", "important");
    }
}
       </script>
    <?php 
       }
       
       add_action('wp_footer', 'wpsites_footer_gallery');




function upsell_popup_after_add_to_cart() {
    if (!is_product()) return;

    global $product;

    // Lingua corrente
    $lang = apply_filters('wpml_current_language', null);
    if (!$lang) $lang = 'it';

    // ID prodotto principale da escludere (con WPML)
$excluded_product_ids = array(
    apply_filters('wpml_object_id', 47771, 'product', true, $lang),
    apply_filters('wpml_object_id', 48414, 'product', true, $lang),
    apply_filters('wpml_object_id', 48667, 'product', true, $lang),
    apply_filters('wpml_object_id', 48772, 'product', true, $lang)
);


    // Se il prodotto attuale è tra quelli da escludere
    if (in_array($product->get_id(), $excluded_product_ids)) return;

    // Categorie da escludere
    $excluded_category_slugs = array('ricambi','balto-care');

    // Controllo se il prodotto è in una delle categorie da escludere (anche tradotte)
    foreach ($excluded_category_slugs as $slug) {
        $translated_term = apply_filters('wpml_object_id', get_term_by('slug', $slug, 'product_cat')->term_id, 'product_cat', true, $lang);
        if (has_term($translated_term, 'product_cat', $product->get_id())) return;
    }


    $translations = array(
        'it' => array(
            'title'        => 'NON DIMENTICARTI DI TENERE PULITO IL TUO TUTORE',
            'subtitle'     => 'PER TE IL NOSTRO BALTO CLEANING KIT',
            'offer'        => 'È IN OFFERTA!',
            'price'        => '12,90€ anziché <del>18,90€</del>',
            'add_button'   => 'AGGIUNGI AL CARRELLO',
            'close_button' => 'CHIUDI',
        ),
        'en' => array(
            'title'        => 'DON’T FORGET TO KEEP YOUR BRACE CLEAN',
            'subtitle'     => 'OUR BALTO CLEANING KIT FOR YOU',
            'offer'        => 'IS ON SALE!',
            'price'        => '€12.90 instead of <del>€18.90</del>',
            'add_button'   => 'ADD TO CART',
            'close_button' => 'CLOSE',
        ),
        'de' => array(
            'title'        => 'VERGISS NICHT, DEINE SCHIENE SAUBER ZU HALTEN',
            'subtitle'     => 'UNSER BALTO CLEANING KIT FÜR DICH',
            'offer'        => 'IST IM ANGEBOT!',
            'price'        => '12,90 € statt <del>18,90 €</del>',
            'add_button'   => 'IN DEN WARENKORB',
            'close_button' => 'SCHLIESSEN',
        ),
        'fr' => array(
            'title'        => 'N’OUBLIEZ PAS DE GARDER VOTRE ATTELLE PROPRE',
            'subtitle'     => 'NOTRE KIT DE NETTOYAGE BALTO POUR VOUS',
            'offer'        => 'EST EN PROMO !',
            'price'        => '12,90 € au lieu de <del>18,90 €</del>',
            'add_button'   => 'AJOUTER AU PANIER',
            'close_button' => 'FERMER',
        ),
        'es' => array(
            'title'        => 'NO OLVIDES MANTENER LIMPIO TU TUTOR',
            'subtitle'     => 'NUESTRO BALTO CLEANING KIT PARA TI',
            'offer'        => '¡ESTÁ EN OFERTA!',
            'price'        => '12,90 € en lugar de <del>18,90 €</del>',
            'add_button'   => 'AÑADIR AL CARRITO',
            'close_button' => 'CERRAR',
        ),
    );

    $t = isset($translations[$lang]) ? $translations[$lang] : $translations['it'];
    $product_id = apply_filters('wpml_object_id', 47771, 'product', true, $lang);
    $image_url = 'https://www.tutoribalto.com/wp-content/uploads/2025/06/KIT-PULIZIA-TUTORI-BALTO-2-300x300.jpg';
    ?>

    <style>
        #upsell-popup {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
        }

.popup-content {
    background: #fff;
    max-width: 600px;
    margin: 5% auto;
    border-radius: 0px;
    padding: 10px 10px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 30px;
    text-align: center;
    z-index:99999999;
}
        .popup-content img {
            max-width: 200px;
            height: auto;
        }

        .popup-text {
            flex: 1 1 300px;
        }

        .popup-text h2 {
            color: #04AAD7;
            font-size: 1.2rem;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .popup-text h3 {
            color: #312D5A;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .popup-text h4 {
            color:  #04AAD7;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .popup-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

.popup-buttons a.button, .popup-buttons button {
  background-color: #04AAD7;
  color: #fff;
  font-weight: 600;
  padding: 12px 25px;
  border: none;
  border-radius: 0px;
  text-decoration: none;
  cursor: pointer;
  flex: 1 1 180px;
  max-width: 220px;
  font-size: 12px;
}
#close-upsell-popup{background-color:#E9E9ED;color:#312D5A;margin-bottom:15px;}

        .popup-buttons button:hover,
        .popup-buttons a.button:hover {
            background-color: #038eb6;
        }

        @media (max-width: 768px) {
  .popup-content {
    flex-direction: column;
    padding: 10px 15px;
    margin: 10px;
  }

            .popup-text h2 {
                font-size: 1rem;
            }

            .popup-text h3,
            .popup-text h4 {
                font-size: 0.9rem;
            }

            .popup-text {
  flex: 1 1 250px;
}
.popup-buttons a.button, .popup-buttons button {
  font-size: 11px;
  min-width: 150px;
}
.popup-buttons {
  display: block;}
.popup-content img {
  margin-bottom: -30px;
}

        }
    </style>

    <div id="upsell-popup">
        <div class="popup-content">
            <img src="<?php echo esc_url($image_url); ?>" alt="Balto Cleaning Kit">
            <div class="popup-text">
                <h2><?php echo esc_html($t['title']); ?></h2>
                <h3><?php echo esc_html($t['subtitle']); ?></h3>
                <h4><?php echo esc_html($t['offer']); ?></h4>
                <p style="font-size:1.2rem; font-weight:700; margin-top:20px;"><?php echo $t['price']; ?></p>
                <div class="popup-buttons">
                    <a href="?add-to-cart=<?php echo esc_attr($product_id); ?>" class="button alt">
                        <?php echo esc_html($t['add_button']); ?>
                    </a>
                    <button type="button" id="close-upsell-popup"><?php echo esc_html($t['close_button']); ?></button>
                </div>
            </div>
        </div>
    </div>

<script>
jQuery(document).ready(function($) {
    $('body').on('added_to_cart', function() {
        $('#upsell-popup').fadeIn();
    });

    $('#close-upsell-popup').on('click', function() {
        $('#upsell-popup').fadeOut();
    });

    // ✅ Chiudi anche cliccando fuori dal popup
    $('#upsell-popup').on('click', function(e) {
        if ($(e.target).is('#upsell-popup')) {
            $(this).fadeOut();
        }
    });
});
</script>
    <?php
}
add_action('wp_footer', 'upsell_popup_after_add_to_cart', 99);


function hide_meta_sizeimpsz_for_specific_categories() {
    // Verifica se siamo su una pagina prodotto
    if (is_product()) {
        global $post;
        
        // Ottieni le categorie del prodotto
        $terms = get_the_terms($post->ID, 'product_cat');
        $hide_element = false;
        
        if ($terms && !is_wp_error($terms)) {
            // Array delle categorie target
            $target_categories = array(815, 816, 817, 818, 819);
            
            foreach ($terms as $term) {
                if (in_array($term->term_id, $target_categories)) {
                    $hide_element = true;
                    break;
                }
            }
        }
        
        // Se il prodotto appartiene alle categorie target, aggiungi CSS per nascondere l'elemento
        if ($hide_element) {
            echo '<style type="text/css">
                .meta-sizeimpsz {
                    display: none !important;
                }
            </style>';
        }
    }
}
add_action('wp_head', 'hide_meta_sizeimpsz_for_specific_categories');



/* wp-content/mu-plugins/redirect-to-live.php */
add_action('plugins_loaded', function () {
  if (php_sapi_name() === 'cli') return; // evita WP-CLI
  $host = $_SERVER['HTTP_HOST'] ?? '';
  if (stripos($host, 'staging21.tutoribalto.com') === 0) {
    // evita redirect su admin/ajax/cron se necessario
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    wp_redirect('https://www.tutoribalto.com' . $uri, 302);
    exit;
  }
});

// functions.php — aggiunge note nelle raccomandazioni d'uso multilingua
add_action('wp_enqueue_scripts', function () {
    if (is_product()) {
        wp_add_inline_script(
            'jquery-core',
            <<<JS
(function(){
    function addNotices(root){
        var panels = root.querySelectorAll('.woocommerce-Tabs-panel, .wc-tab, .woocommerce-tabs, .product .summary, .entry-summary');
        panels.forEach(function(panel){
            var headings = panel.querySelectorAll('h5');
            headings.forEach(function(h5){
                var t = (h5.textContent || '').trim().toUpperCase();

                // Cattura titoli nelle varie lingue
                if (!(
                    /RACCOMANDAZIONI\\s+D[’']USO/.test(t) ||       // IT
                    /RECOMMENDATIONS\\s+FOR\\s+USE/.test(t) ||     // EN
                    /RECOMMANDATIONS\\s+D[’']USAGE/.test(t) ||     // FR
                    /ANWENDUNGSTIPPS/.test(t) ||                   // DE
                    /RECOMENDACIONES\\s+DE\\s+USO/.test(t)         // ES
                )) return;

                // Trova la UL subito dopo
                var ul = h5.nextElementSibling;
                while (ul && !(ul.tagName === 'UL' && /\\blist\\b/.test(ul.className))) {
                    ul = ul.nextElementSibling;
                }
                if (!ul) return;

                // Evita doppioni
                var exists = ul.innerHTML.includes('Il prodotto non è un presidio medico') 
                          || ul.innerHTML.includes('Tenere lontano dalla portata dei bambini')
                          || ul.innerHTML.includes('This product is not a medical device')
                          || ul.innerHTML.includes('Keep out of reach of children')
                          || ul.innerHTML.includes('Ce produit n’est pas un dispositif médical')
                          || ul.innerHTML.includes('Tenir hors de portée des enfants')
                          || ul.innerHTML.includes('Dieses Produkt ist kein Medizinprodukt')
                          || ul.innerHTML.includes('Außer Reichweite von Kindern aufbewahren')
                          || ul.innerHTML.includes('Este producto no es un dispositivo médico')
                          || ul.innerHTML.includes('Mantener fuera del alcance de los niños');
                if (exists) return;

                // Detect language by heading
                var lang = 'it';
                if (/RECOMMENDATIONS\\s+FOR\\s+USE/.test(t)) lang = 'en';
                else if (/RECOMMANDATIONS\\s+D[’']USAGE/.test(t)) lang = 'fr';
                else if (/ANWENDUNGSTIPPS/.test(t)) lang = 'de';
                else if (/RECOMENDACIONES\\s+DE\\s+USO/.test(t)) lang = 'es';

                // Crea <li> comuni a tutte le lingue
                var liChild = document.createElement('li');
                liChild.className = 'star';
                var liNotMed = document.createElement('li');
                liNotMed.className = 'star';

                switch(lang){
                    case 'en':
                        liChild.textContent = 'Keep out of reach of children.';
                        liNotMed.textContent = 'This product is not a medical device.';
                        break;
                    case 'fr':
                        liChild.textContent = 'Tenir hors de portée des enfants.';
                        liNotMed.textContent = 'Ce produit n’est pas un dispositif médical.';
                        break;
                    case 'de':
                        liChild.textContent = 'Außer Reichweite von Kindern aufbewahren.';
                        liNotMed.textContent = 'Dieses Produkt ist kein Medizinprodukt.';
                        break;
                    case 'es':
                        liChild.textContent = 'Mantener fuera del alcance de los niños.';
                        liNotMed.textContent = 'Este producto no es un dispositivo médico.';
                        break;
                    default: // it
                        var liVet = document.createElement('li');
                        liVet.className = 'star';
                        liVet.textContent = 'Il prodotto non è un presidio medico.';
                        ul.appendChild(liVet);

                        liChild.textContent = 'Prodotto non detraibile come spesa veterinaria.';
                        liNotMed.textContent = 'Tenere lontano dalla portata dei bambini.';
                        break;
                }

                ul.appendChild(liChild);
                ul.appendChild(liNotMed);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){ addNotices(document); });
    } else {
        addNotices(document);
    }

    var obs = new MutationObserver(function(){ addNotices(document); });
    obs.observe(document.body, { childList: true, subtree: true });
})();
JS
        );
    }
});
