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

    if('wwo_enable_wholesale_customer' == woo_get_enabled_user_role()){
        global $wpdb;
        $price=$v_price;
        $product=$v_product;
        $table_name = $wpdb->prefix . "options"; 	
    
        $results = $wpdb->get_results( 'SELECT * FROM '.$table_name.' WHERE option_value = "enable_role"' );
    
        foreach ($results as $result){
    
            $ifwholesale = str_replace("wholesale_customer","wholesale",$result->option_name);
    
            $finaldata = str_replace("wwo_enable_","_",$ifwholesale).'_price';
    
            $finalno = str_replace("wwo_enable_","",$ifwholesale).'_price';
    
            if($result->option_name == woo_get_enabled_user_role()){
    
                $variations = $product->get_available_variations();
    
                $lowestvar = array();
    
                foreach ($variations as $variation){
    
                    $lowestvar[] = get_post_meta($variation['variation_id'], $finaldata, true);
    
                    $lowestnor[] = get_post_meta($variation['variation_id'],'_price', true);
    
                    array_multisort($lowestvar, SORT_ASC);
    
                    array_multisort($lowestnor, SORT_ASC);
    
                }
    
        
    
                $lrrp = min($lowestnor);
    
                $hrrp = max($lowestnor);
    
                $lwrrp = min($lowestvar);
    
                $hwrrp = max($lowestvar);	
    
                
    
                $wwo_percentage = get_option( 'wwo_percentage' );
    
                $wwo_savings = get_option( 'wwo_savings_label' );
    
                $wwo_rrp = get_option( 'wwo_rrp_label' );
    
                $wwo_wholesale_label = get_option( 'wwo_wholesale_label' );
    
                
    
                if($lrrp == $hrrp){
    
                    if($wwo_rrp !=''){
    
                        $rrp_price = '<div class="woo_retail"><span class="woo_retail_label">'.$wwo_rrp.':</span> <span class="woo_retail_price">'.wc_price($lrrp).'</span></div>';		
    
                    }
    
                } else {
    
                    if($wwo_rrp !=''){
    
                        $rrp_price = '<div class="woo_retail"><span class="woo_retail_label">'.$wwo_rrp.':</span> <span class="woo_retail_price">'.wc_price($lrrp).' - '.wc_price($hrrp).'</span></div>';		
    
                    }
    
                }
    
                
    
                if(get_post_meta($variation['variation_id'], $finaldata, true)){
    
                    if($lwrrp == $hwrrp){
    
                        if($wwo_wholesale_label !=''){
    
                            $price =  $rrp_price.'<div class="woo_wholesale"><span class="woo_wholesale_label">'.$wwo_wholesale_label.':</span> <span class="woo_wholesale_price">'.wc_price($lwrrp).'</span></div>';	 
    
                        }
    
                    } else {
    
                        if($wwo_wholesale_label !=''){
    
                            $price =  $rrp_price.'<div class="woo_wholesale"><span class="woo_wholesale_label">'.$wwo_wholesale_label.':</span> <span class="woo_wholesale_price">'.wc_price($lwrrp).' - '.wc_price($hwrrp).'</span></div>';	
    
                        }
    
                    }
    
                } 
    
    
    
            }
    
        }
    
        return $price;
    }else{

// Product Price

$prod_prices = array( $v_product->get_variation_price( 'min', true ), 

                            $v_product->get_variation_price( 'max', true ) );

$prod_price = $prod_prices[0]!==$prod_prices[1] ? sprintf(__('<span class="from">a partire da</span> %1$s', 'woocommerce'), 

                       wc_price( $prod_prices[0] ) ) : wc_price( $prod_prices[0] );



// Regular Price

$regular_prices = array( $v_product->get_variation_regular_price( 'min', true ), 

                          $v_product->get_variation_regular_price( 'max', true ) );

sort( $regular_prices );

$regular_price = $regular_prices[0]!==$regular_prices[1] ? sprintf(__('<span class="from">a partire da</span> %1$s','woocommerce')

                      , wc_price( $regular_prices[0] ) ) : wc_price( $regular_prices[0] );



if ( $prod_price !== $regular_price ) {

$prod_price = '<del>'.$regular_price.$v_product->get_price_suffix() . '</del> <ins>' . 

                       $prod_price . $v_product->get_price_suffix() . '</ins>';

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
                per avere questi prodotti negli Stati Uniti, devono essere acquistati dal rivenditore KVP<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>
                
                <br>
    
    " ), 'error' );
          
            }else if(ICL_LANGUAGE_CODE=='de'){
           
                wc_add_notice( __( "Sehr geehrter Kunde,<br>
                Um diese Produkte aus Vereinigte Staaten, zu beziehen müssen sie über den KVP Händler erworben werden<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>                
                <br>
    
    " ), 'error' );
           
            }else if(ICL_LANGUAGE_CODE=='es'){
          
                wc_add_notice( __( "Estimado cliente,<br>
                para tener estos productos en Estados Unidos, deben comprarse al distribuidor KVP<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>                
                <br>
    
    " ), 'error' );
          
            }else if(ICL_LANGUAGE_CODE=='fr'){
          
                wc_add_notice( __( "Cher Client,<br>
                pour avoir ces produits de États-Unis, il faut les acheter chez le revendeur KVP<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>                
                <br>
    
    " ), 'error' );
          
            }else if(ICL_LANGUAGE_CODE=='en'){
          
                wc_add_notice( __( "Dear Customer,<br>
                to have these products in United States, they must be purchased from the KVP dealer<br><br>
                
<a href='https://baltousa.com/' style='text-decoration:underline;font-size:22px;' target='_blank'>www.baltousa.com</a>                
                
                <br>
    
    " ), 'error' );
          
            }
    } /*
    
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
               
                //if($fiscal_number!=$fiscal_repeat){
              //      wc_add_notice( __( 'Il campo Codice fiscale deve essere ripetuto uguale' ), 'error' );
             //   }else 
             //   if (strlen(trim($fiscal_number)) != 16 || !is_numeric($vsdata)) 
             //   {
            //        wc_add_notice( __( 'wwcodice fiscale errato' ), 'error' );
            //    }
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


add_action( 'woocommerce_created_customer', 'sww_approve_customer_user' );
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

   add_shortcode('mapfilter', 'map_filter_with_hi_shortcode');

// Shortcode callback function
function map_filter_with_hi_shortcode($atts, $content = null) {
    // Process attributes if needed
    // Example: $attribute = $atts['attribute'];

    // Generate the map filter HTML and store it in a variable
    $map_filter_html = ''; // Replace this with the code that generates the map filter HTML

    // Print "hi"
    $hi_text = '<div id="image-map-container" class="mapparent"><div id="image-map" style="max-width: 100%" class="image-mapper"><img id="img_bg" src="https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/CANE-SOLO-1.png" usemap="#image-map" />
    <svg class="image-mapper-svg" style="width: 100%;">
    <polygon class="cc" points="312,205,236,337,378,423,520,411,602,371,696,347,672,267" class="image-mapper-shape" data-area-index="3" style="fill: transparent; stroke: transparent; stroke-width: 0; opacity: 0.6; cursor: pointer;"></polygon>
    <polygon class="dd" points="371,397,711,361,511,449,733,496,571,538,569,488,752,670,711,678,675,658,641,550,499,585" class="image-mapper-shape" data-area-index="2" style="fill: transparent; stroke:transparent; stroke-width: 0; opacity: 0.6; cursor: pointer;"></polygon>
    <polygon class="bb" points="192,316,243,448,259,555,260,628,225,651,250,667,303,674,325,606,320,471,324,438,325,418" class="image-mapper-shape" data-area-index="1" style="fill: transparent; stroke:transparent; stroke-width: 0; opacity: 0.6; cursor: pointer;"></polygon>
    <polygon class="aa" points="192,310,146,192,86,175,59,166,59,139,90,121,55,90,69,68,117,69,185,50,216,64,243,116,272,171,284,179,293,194,303,201" class="image-mapper-shape" data-area-index="0" style="fill: transparent; stroke: transparent; stroke-width: 0; opacity: 0.6; cursor: pointer;"></polygon>
    </svg>
    <map name="image-map"> <area class="bb" title="" alt="" coords="210,244,156,609,529,681,651,1004,1113,1007,1121,731,791,226,536,154,403,258" shape="poly" target="" /> <area class="aa" title="" alt="" coords="640,1007,1207,1018,1189,2448,762,2377" shape="poly" target="" /> <area class="cc" title="" alt="" coords="1128,728,1117,1004,1221,1029,1207,1577,2117,1548,2562,1498,2551,1129,2311,885" shape="poly" target="" /> <area class="dd" title="" alt="" coords="2092,1552,2286,1821,2472,1982,2501,2104,2286,2305,2311,2362,2687,2466,2777,1939,2630,1760,2566,1505" shape="poly" target="" /> </map>
    </div></div>
    <script>
    $(document).ready(function(){

        var firstLi = $(".products-tabs-title li:first");

        // Trigger a click event on the a tag within the first li
        firstLi.find("span").click(function(){
  
          $("#img_bg").attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/CANE-SOLO-1.png");
        }); 
    });
    $(".aa").hover(function(){ $("#img_bg").attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/TESTA-COLLO-2.png");
        var secondLi = $("ul.products-tabs-title li:eq(1)");

        // Trigger the click event on the 2nd <li> element
       
      });
    $(".bb").hover(function(){ $("#img_bg").attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/ZAMPE-ANTERIORI-2.png");
        var fourLi = $("ul.products-tabs-title li:eq(3)");

        // Trigger the click event on the 2nd <li> element
    
      });
    $(".cc").hover(function(){ $("#img_bg").attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/SCHIENA-BACINO-2.png");
        var thirdLi = $("ul.products-tabs-title li:eq(2)");

        // Trigger the click event on the 2nd <li> element
    
      });
    $(".dd").hover(function(){ $("#img_bg").attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/ZAMPE-POST.-1.png");
        var fiveLi = $("ul.products-tabs-title li:eq(4)");

        // Trigger the click event on the 2nd <li> element
      
      });


      $(".aa").click(function(){// $("#img_bg").attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/TESTA-COLLO-2.png");
        var secondLi = $("ul.products-tabs-title li:eq(1)");

        // Trigger the click event on the 2nd <li> element
        secondLi.trigger("click");
      });
    $(".bb").click(function(){ //$("#img_bg").attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/ZAMPE-ANTERIORI-2.png");
        var fourLi = $("ul.products-tabs-title li:eq(3)");

        // Trigger the click event on the 2nd <li> element
        fourLi.trigger("click");
      });
    $(".cc").click(function(){ //$("#img_bg").attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/SCHIENA-BACINO-2.png");
        var thirdLi = $("ul.products-tabs-title li:eq(2)");

        // Trigger the click event on the 2nd <li> element
        thirdLi.trigger("click");
      });
    $(".dd").click(function(){// $("#img_bg").attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/ZAMPE-POST.-1.png");
        var fiveLi = $("ul.products-tabs-title li:eq(4)");

        // Trigger the click event on the 2nd <li> element
        fiveLi.trigger("click");
      });
    
    $("#img_bg").mouseout(function(){ $(this).attr("src","https://www.staging9.tutoribalto.com/wp-content/uploads/2023/08/CANE-SOLO-1.png");
      });</script><style>.mapparent{position:relative;} .mapparent svg{position:absolute;}  #image-map-container {
        display: inline-block;
        border: 0px solid #DDD;
        padding: 2px;
        border-radius: 3px;
        max-width: 100%;
    }#image-map {
        display: inline-block;
        max-width: 100%;
    }
    .image-mapper {
        position: relative;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .image-mapper-img {
        max-width: 100%;
        max-height: 100%;
    }
    .image-mapper-svg {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        height: 100%;
        width: 100%;
    }#img_bg{max-width:85%;}</style>';

    // Combine the map filter HTML and the "hi" text
    $output = $map_filter_html . $hi_text;

    return $output;
}
function wpsites_footer_gallery() {

 ?>
 <script>
      $(document).ready(function() {
    // Attach an event listener to the radio buttons
    $('input[name="billing_client_type"]').change(function() {
      // Check if the "Azienda" radio button is checked
      if ($('#billing_client_type_corp').is(':checked')) {
        // Trigger your custom event or perform actions here
        $('#billing_fiscal_repeat_field').hide();
      }else{
        $('#billing_fiscal_repeat_field').show();
      }
    });
  });
function legfilter(){
var fiveLi = $("ul.products-tabs-title li:eq(4)");
fiveLi.trigger("click");
}
   
function headfilter(){
var secondLi = $("ul.products-tabs-title li:eq(1)");
secondLi.trigger("click");
}
    
function frontlegfilter(){
var fourLi = $("ul.products-tabs-title li:eq(3)");
fourLi.trigger("click");
}

function chestfilter(){
var thirdLi = $("ul.products-tabs-title li:eq(2)");
thirdLi.trigger("click");
}
function showinformation(){
    $("#slider-40-slide-390-layer-18").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-18").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-18").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-18").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-18").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-18").prop("style").setProperty("opacity", "1", "important");
}
function hideinformation(){
    $("#slider-40-slide-390-layer-18").prop("style").setProperty("opacity", "0", "important");
}
function showheadinfo(){
    $("#slider-40-slide-390-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    $("#slider-40-slide-390-layer-10").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-10").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-10").prop("style").setProperty("opacity", "1", "important"); 
    $("#slider-40-slide-390-layer-10").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-10").prop("style").setProperty("opacity", "1", "important");
}
function hideheadinfo(){
    $("#slider-40-slide-390-layer-10").prop("style").setProperty("opacity", "0", "important");
}
function showbodyinfo(){
    $("#slider-40-slide-390-layer-19").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-19").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-19").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-19").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-19").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-19").prop("style").setProperty("opacity", "1", "important");
}
function hidebodyinfo(){
    $("#slider-40-slide-390-layer-19").prop("style").setProperty("opacity", "0", "important");
}
function backleginfoshow(){
    $("#slider-40-slide-390-layer-20").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-20").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-20").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-20").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-20").prop("style").setProperty("opacity", "1", "important");
    $("#slider-40-slide-390-layer-20").prop("style").setProperty("opacity", "1", "important");
}
function backleginfohide(){
    $("#slider-40-slide-390-layer-20").prop("style").setProperty("opacity", "0", "important");
}
    </script>
 <?php 
    }
    
    add_action('wp_footer', 'wpsites_footer_gallery');