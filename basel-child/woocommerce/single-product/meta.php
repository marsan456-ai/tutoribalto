<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
		<?php $sku = $product->get_sku(); ?>

		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo true == $sku ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>

	<?php endif; ?>

	<?php echo wc_get_product_category_list( $product->get_id(), '<span class="meta-sep">,</span> ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

	<?php echo wc_get_product_tag_list( $product->get_id(), '<span class="meta-sep">,</span> ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>



<a style="display:block" href="#" class="meta-sizeimpsz">
<div style="width:200px; display:block;float:left;" class="divta">
<img src="https://www.tutoribalto.com/wp-content/uploads/2023/06/TROVA-TAGLIA.svg" style="width:190px;">
</div>
<div  style="width:380px; display:block;float:left;font-size:23px;line-height:26px;" class="meta-sizeimp divta">
<?php 
// Verifica se è un prodotto della categoria accessori (ID 246 o traduzioni)
$is_accessory = false;
if (is_product()) {
    global $post;
    $terms = get_the_terms($post->ID, 'product_cat');
    if ($terms) {
        foreach ($terms as $term) {
            if (in_array($term->term_id, array(246, 627, 628, 629, 630))) {
                $is_accessory = true;
                break;
            }
        }
    }
}

if (!$is_accessory) {
    // Versione standard (non accessori)
    if(ICL_LANGUAGE_CODE=='it'){
    ?><span style="font-weight:800; color:#18ACDF">TABELLA TAGLIE </span><br>
    <span style="font-weight:800; color:#312D5A">COME SCEGLIERE LA<br>
    TAGLIA CORRETTA <br>
    DEL TUTORE?</span>
    <?php
    }
    ?>
    <?php if(ICL_LANGUAGE_CODE=='en'){
    ?><span style="font-weight:800; color:#18ACDF">SIZE CHART </span><br>
    <span style="font-weight:800; color:#312D5A">HOW TO CHOOSE THE<br>
    CORRECT SIZE <br>
    OF THE BRACE?</span>
    <?php
    }
    ?>
    <?php if(ICL_LANGUAGE_CODE=='de'){
    ?><span style="font-weight:800; color:#18ACDF">GRÖSSENTABELLE </span><br>
    <span style="font-weight:800; color:#312D5A">WIE MAN DAS AUSWÄHLT<br>
    RICHTIGE GRÖSSE <br>
    DES WÄCHTERS?</span>
    <?php
    }
    ?>
    <?php if(ICL_LANGUAGE_CODE=='es'){
    ?><span style="font-weight:800; color:#18ACDF">TABLA DE TALLAS </span><br>
    <span style="font-weight:800; color:#312D5A">CÓMO ELEGIR EL<br>
    TAMAÑO CORRECTO <br>
    DEL PROTECTOR?</span>
    <?php
    }
    ?>
    <?php if(ICL_LANGUAGE_CODE=='fr'){
    ?><span style="font-weight:800; color:#18ACDF">TABLEAU DES TAILLES </span><br>
    <span style="font-weight:800; color:#312D5A">COMMENT CHOISIR LE<br>
    TAILLE CORRECTE <br>
    DU GARDIEN ?</span>
    <?php
    }
} else {
    // Versione per accessori (senza "del tutore?")
    if(ICL_LANGUAGE_CODE=='it'){
    ?><span style="font-weight:800; color:#18ACDF">TABELLA TAGLIE </span><br>
    <span style="font-weight:800; color:#312D5A">COME SCEGLIERE LA<br>
    TAGLIA CORRETTA</span>
    <?php
    }
    ?>
    <?php if(ICL_LANGUAGE_CODE=='en'){
    ?><span style="font-weight:800; color:#18ACDF">SIZE CHART </span><br>
    <span style="font-weight:800; color:#312D5A">HOW TO CHOOSE THE<br>
    CORRECT SIZE</span>
    <?php
    }
    ?>
    <?php if(ICL_LANGUAGE_CODE=='de'){
    ?><span style="font-weight:800; color:#18ACDF">GRÖSSENTABELLE </span><br>
    <span style="font-weight:800; color:#312D5A">WIE MAN DAS AUSWÄHLT<br>
    RICHTIGE GRÖSSE</span>
    <?php
    }
    ?>
    <?php if(ICL_LANGUAGE_CODE=='es'){
    ?><span style="font-weight:800; color:#18ACDF">TABLA DE TALLAS </span><br>
    <span style="font-weight:800; color:#312D5A">CÓMO ELEGIR EL<br>
    TAMAÑO CORRECTO</span>
    <?php
    }
    ?>
    <?php if(ICL_LANGUAGE_CODE=='fr'){
    ?><span style="font-weight:800; color:#18ACDF">TABLEAU DES TAILLES </span><br>
    <span style="font-weight:800; color:#312D5A">COMMENT CHOISIR LE<br>
    TAILLE CORRECTE</span>
    <?php
    }
} // Fine condizione
?>
</div>
</a><!-- Trigger the modal with a button -->

<style>
	#subscriber_form{ display:none; }
#subscriber_form {
	margin: 0;
    width: 100%;
    z-index: 2;
    height: 100%;
    max-height: 1080px;
    position: fixed;
    background: rgba(70, 71, 72, 0.69);
	z-index: 999999;
}
#sub_div_form { 
	width: 80%;  
	height: auto;
    margin: 0 auto;
    background: white;
    margin-top: 5%;
    padding: 45px;
    border: 1px solid #9E9E9E;
    border-radius: .25em .25em .4em .4em;
    text-align: center;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}
#sub_div_form span {
	position: relative;
    float: right;
    font-weight: 700;
    margin-top: -40px;
    height: 30px;
    font-size: 18px;
    width: 30px;
    line-height: 15px;
    margin-right: -40px;
    border: 2px solid;
    border-radius: 90px;
    padding: 7px;
}
span#kv_form_close { cursor: pointer;}
span#kv_form_close:hover {
    color: #e0190b;
}
#nickx-gallery div{ max-height:600px; }
@media screen and (max-width: 440px) {
	#sub_div_form { 
		width: 290px; 
		padding: 35px;
	}
}
</style>
<script>
	    setTimeout(function () {
			
 
	jQuery("#subscriber_form").on('click', function(e){  // Close, when click outside of the box
		 if (e.target !== this)
			return;
		else{
			$(this).hide();
		}
	});

	jQuery("#show_popup").on("click", function() {  // Custom Show button code.
		jQuery("#subscriber_form").show();
	});
	jQuery("#kv_form_close").on('click', function(e){  // close button code. 
		jQuery('#subscriber_form').fadeOut('slow');
	});
			 jQuery('.woocommerce-Tabs-panel--description').append(jQuery('.woocommerce-Tabs-panel--taglie').html())
			 jQuery('.woocommerce-Tabs-panel--description').append(jQuery('.woocommerce-Tabs-panel--sizes').html())
			 jQuery('.woocommerce-Tabs-panel--description').append(jQuery('.woocommerce-Tabs-panel--groessen').html())
			 jQuery('.woocommerce-Tabs-panel--description').append(jQuery('.woocommerce-Tabs-panel--tailles').html())
			 jQuery('.woocommerce-Tabs-panel--description').append(jQuery('.woocommerce-Tabs-panel--tabla-de-tallas').html())
			jQuery('.chart_sz').append(jQuery('.woocommerce-Tabs-panel--taglie').html())
        jQuery('.meta-sizeimpsz').click(function() {
			jQuery('#subscriber_form').delay(1000).fadeIn('slow');  //default Auto after 1 Sec
            
           return false;
         }); 
    },1000);
</script>