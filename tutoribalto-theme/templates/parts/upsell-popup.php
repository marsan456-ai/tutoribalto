<?php
/**
 * Upsell Popup Template
 *
 * @package Tutoribalto_Theme
 * @var array  $t Translation strings
 * @var int    $cleaning_kit_id Product ID
 * @var string $image_url Image URL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
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
	z-index: 99999999;
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
	color: #04AAD7;
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

.popup-buttons a.button,
.popup-buttons button {
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

#close-upsell-popup {
	background-color: #E9E9ED;
	color: #312D5A;
	margin-bottom: 15px;
}

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

	.popup-buttons a.button,
	.popup-buttons button {
		font-size: 11px;
		min-width: 150px;
	}

	.popup-buttons {
		display: block;
	}

	.popup-content img {
		margin-bottom: -30px;
	}
}
</style>

<div id="upsell-popup">
	<div class="popup-content">
		<img src="<?php echo esc_url( $image_url ); ?>" alt="Balto Cleaning Kit">
		<div class="popup-text">
			<h2><?php echo esc_html( $t['title'] ); ?></h2>
			<h3><?php echo esc_html( $t['subtitle'] ); ?></h3>
			<h4><?php echo esc_html( $t['offer'] ); ?></h4>
			<p style="font-size:1.2rem; font-weight:700; margin-top:20px;">
				<?php echo wp_kses_post( $t['price'] ); ?>
			</p>
			<div class="popup-buttons">
				<a href="?add-to-cart=<?php echo esc_attr( $cleaning_kit_id ); ?>" class="button alt">
					<?php echo esc_html( $t['add_button'] ); ?>
				</a>
				<button type="button" id="close-upsell-popup">
					<?php echo esc_html( $t['close_button'] ); ?>
				</button>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	// Show popup on add to cart
	$('body').on('added_to_cart', function() {
		$('#upsell-popup').fadeIn();
	});

	// Close popup on button click
	$('#close-upsell-popup').on('click', function() {
		$('#upsell-popup').fadeOut();
	});

	// Close popup on background click
	$('#upsell-popup').on('click', function(e) {
		if ($(e.target).is('#upsell-popup')) {
			$(this).fadeOut();
		}
	});
});
</script>
