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



<?php if(ICL_LANGUAGE_CODE=='it'){
 ?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:15px;margin-bottom:10px;" id="vacanzeo"> Dal 9 al 20 agosto ci prendiamo una piccola pausa!
Tutti gli ordini ricevuti in questo periodo 
verranno spediti a partire dal 21 agosto.
 </div>
<?php
}
?>
 <?php if(ICL_LANGUAGE_CODE=='en'){
?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:15px;margin-bottom:10px;" id="vacanzeo">From 9 to 20 August we take a little break!
All orders received in this period will be shipped from August 21st.</div>
<?php
}
?>

<?php if(ICL_LANGUAGE_CODE=='es'){
?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:15px;margin-bottom:10px;" id="vacanzeo">¡Del 9 al 20 de agosto nos tomamos un pequeño descanso!
Todos los pedidos recibidos en este período se enviarán a partir del 21 de agosto. </div>
<?php
}
?>

<?php if(ICL_LANGUAGE_CODE=='fr'){
?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:15px;margin-bottom:10px;" id="vacanzeo">Du 9 au 20 août on fait une petite pause !
Toutes les commandes reçues pendant cette période seront expédiées à partir du 21 août. </div>
<?php
}
?>

<?php if(ICL_LANGUAGE_CODE=='de'){
?>
<div style="background-color:#27A8D1; color:#fff;padding: 8px;font-size:15px;margin-bottom:10px;" id="vacanzeo">Vom 9. bis 20. August machen wir eine kleine Pause!
Alle in diesem Zeitraum eingegangenen Bestellungen werden ab dem 21. August versendet.</div>
<?php
}
?>


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
