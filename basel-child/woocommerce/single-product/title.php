<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woothemes.com/document/template-structure/
 * @author     WooThemes
 * @package    WooCommerce/Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$is_quick_view = basel_loop_prop( 'is_quick_view' );
?>
<?php
// ID della categoria 'balto care' nella lingua principale (es. italiano)
$default_term_id = 819; // ← Sostituisci con l’ID reale della categoria in IT

// Ottieni l'ID tradotto nella lingua corrente
$translated_term_id = apply_filters( 'wpml_object_id', $default_term_id, 'product_cat', true );

// Controlla se il prodotto appartiene alla categoria tradotta
if ( has_term( $translated_term_id, 'product_cat' ) ) {
    echo '<img src="https://www.tutoribalto.com/wp-content/uploads/2025/05/logo-balto-care.png" alt="Balto Care Logo" style="max-width:138px; margin:0px 0px 20px 0px"><style> p.price > span.woocommerce-Price-amount > bdi {font-size:1.6rem}</style>';
}
?>


<h2 itemprop="name" class="product_title entry-title"><?php if( $is_quick_view ): ?><a href="<?php the_permalink(); ?>"><?php endif; ?><?php the_title(); ?><?php if( $is_quick_view ): ?></a><?php endif; ?></h2>
<?php

if(get_field('sub_title'))
{
	echo '<h1 class="sottotitolo">' . get_field('sub_title') . '</h1>';
}

?>