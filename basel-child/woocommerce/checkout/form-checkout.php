<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.5.0
 */

defined( 'ABSPATH' ) || exit;

//WC 3.5.0
if ( version_compare( WC()->version, '3.5.0', '<' ) ) {
	wc_print_notices();
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', esc_html__( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', wc_get_checkout_url() ); ?>


<?php 
/**
if(ICL_LANGUAGE_CODE=='it'){
 ?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:21px;margin-bottom:10px;text-align:center;" id="vacanzeo"> Dall' 8 al 17 agosto ci prendiamo una piccola pausa!
<br>Tutti gli ordini ricevuti in questo periodo 
verranno spediti a partire dal 18 agosto.
 </div>
<?php
}
?>
 <?php if(ICL_LANGUAGE_CODE=='en'){
?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:21px;margin-bottom:10px;padding:15px;text-align:center;" id="vacanzeo">From 8 to 17 August we take a little break!
<br>All orders received in this period will be shipped from August 18st.</div>
<?php
}
?>

<?php if(ICL_LANGUAGE_CODE=='es'){
?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:21px;margin-bottom:10px;padding:15px;text-align:center;" id="vacanzeo">¡Del 8 al 17 de agosto nos tomamos un pequeño descanso!
<br>Todos los pedidos recibidos en este período se enviarán a partir del 18 de agosto. </div>
<?php
}
?>

<?php if(ICL_LANGUAGE_CODE=='fr'){
?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:21px;margin-bottom:10px;padding:15px;text-align:center;" id="vacanzeo">Du 8 au 17 août on fait une petite pause !
<br>Toutes les commandes reçues pendant cette période seront expédiées à partir du 18 août. </div>
<?php
}
?>

<?php if(ICL_LANGUAGE_CODE=='de'){
?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:15px;margin-bottom:10px;padding:15px;text-align:center;" id="vacanzeo">Vom 8 bis 17 August machen wir eine kleine Pause!
<br>Alle in diesem Zeitraum eingegangenen Bestellungen werden ab dem 18 August versendet.</div>
<?php
}
*/
?>

<div class="container">
<div id="cerror"></div>
<div class="row">
	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( $get_checkout_url ); ?>" enctype="multipart/form-data">

		<div class="col-sm-6">

				<?php if ( $checkout->get_checkout_fields() ) : ?>

					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div class="row" id="customer_details">
						<div class="col-sm-12">
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</div>

						<div class="col-sm-12">
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						</div>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

				<?php endif; ?>

		</div>

		<div class="col-sm-6">
			<div class="checkout-order-review">	
				<h3 id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

			</div>
		</div>
		
	</form>

</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
