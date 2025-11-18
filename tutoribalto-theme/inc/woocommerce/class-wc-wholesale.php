<?php
/**
 * WooCommerce Wholesale Class
 *
 * @package Tutoribalto_Theme
 */

declare(strict_types=1);

/**
 * Class Tutoribalto_WC_Wholesale
 *
 * Handles B2B wholesale customer registration and approval.
 */
class Tutoribalto_WC_Wholesale {

	/**
	 * Wholesale role name
	 *
	 * @var string
	 */
	private const WHOLESALE_ROLE = 'wholesale_customer';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Add wholesale fields to registration form.
		add_action( 'woocommerce_register_form_start', array( $this, 'add_wholesale_registration_fields' ) );

		// Handle wholesale customer creation.
		add_action( 'woocommerce_created_customer', array( $this, 'handle_wholesale_registration' ), 10, 3 );

		// Create wholesale role if it doesn't exist.
		add_action( 'init', array( $this, 'create_wholesale_role' ) );
	}

	/**
	 * Create wholesale customer role
	 */
	public function create_wholesale_role(): void {
		if ( get_role( self::WHOLESALE_ROLE ) ) {
			return;
		}

		// Get customer role capabilities as base.
		$customer_role = get_role( 'customer' );
		if ( ! $customer_role ) {
			return;
		}

		add_role(
			self::WHOLESALE_ROLE,
			__( 'Wholesale Customer', 'tutoribalto-theme' ),
			$customer_role->capabilities
		);
	}

	/**
	 * Add wholesale registration fields to form
	 */
	public function add_wholesale_registration_fields(): void {
		// Get checkout object for field rendering.
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$checkout = WC()->checkout();
		$fields = $checkout->get_checkout_fields( 'billing' );

		// Remove email field (already in registration form).
		unset( $fields['billing_email'] );

		// Modify client type field to show only "Company".
		if ( isset( $fields['billing_client_type'] ) ) {
			$fields['billing_client_type']['options'] = array( 'corp' => __( 'Azienda', 'tutoribalto-theme' ) );
		}

		// Render fields.
		foreach ( $fields as $key => $field ) {
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		}

		// Veterinarian-specific fields.
		$this->render_veterinarian_fields();

		// Hide unnecessary fields.
		echo '<style type="text/css">#billing_ctasse_field { display: none; }</style>';
	}

	/**
	 * Render veterinarian-specific fields
	 */
	private function render_veterinarian_fields(): void {
		?>
		<p class="form-row form-row-wide validate-required" id="billing_numero_albo_veterinari_field">
			<label for="billing_numero_albo_veterinari">
				<?php esc_html_e( 'Numero albo veterinari', 'tutoribalto-theme' ); ?>
				<span class="required">*</span>
			</label>
			<input
				type="text"
				class="input-text"
				name="billing_numero_albo_veterinari"
				id="billing_numero_albo_veterinari"
				placeholder="<?php esc_attr_e( 'Numero albo veterinari', 'tutoribalto-theme' ); ?>"
				value=""
			/>
		</p>

		<p class="form-row form-row-wide validate-required" id="billing_provincia_alb_field">
			<label for="billing_provincia_alb">
				<?php esc_html_e( 'Provincia albo', 'tutoribalto-theme' ); ?>
				<span class="required">*</span>
			</label>
			<input
				type="text"
				class="input-text"
				name="billing_provincia_alb"
				id="billing_provincia_alb"
				placeholder="<?php esc_attr_e( 'Provincia albo', 'tutoribalto-theme' ); ?>"
				value=""
			/>
		</p>
		<?php
	}

	/**
	 * Handle wholesale customer registration
	 *
	 * @param int    $customer_id Customer ID.
	 * @param array  $new_customer_data Customer data.
	 * @param string $password_generated Generated password.
	 */
	public function handle_wholesale_registration( int $customer_id, array $new_customer_data, string $password_generated ): void {
		// Check if this is a wholesale registration.
		if ( ! $this->is_wholesale_registration() ) {
			return;
		}

		// Set wholesale role.
		$user = new WP_User( $customer_id );
		$user->set_role( self::WHOLESALE_ROLE );

		// Mark as pending approval.
		update_user_meta( $customer_id, 'wholesale_pending', 1 );

		// Save veterinarian data.
		$this->save_veterinarian_data( $customer_id );

		// Send admin notification.
		$this->send_admin_notification( $customer_id, $new_customer_data );

		// Custom action hook for extensions.
		do_action( 'tutoribalto_wholesale_registration', $customer_id, $new_customer_data, $password_generated );
	}

	/**
	 * Check if current registration is wholesale
	 *
	 * @return bool
	 */
	private function is_wholesale_registration(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$account_type = $_POST['account_type'] ?? '';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$is_vet = isset( $_POST['is_vet'] ) ? (int) $_POST['is_vet'] : 0;
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$wholesale = $_POST['wholesale'] ?? '';

		return 'wholesale' === $account_type || 1 === $is_vet || '1' === $wholesale;
	}

	/**
	 * Save veterinarian data to user meta
	 *
	 * @param int $customer_id Customer ID.
	 */
	private function save_veterinarian_data( int $customer_id ): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$numero_albo = isset( $_POST['billing_numero_albo_veterinari'] )
			? sanitize_text_field( wp_unslash( $_POST['billing_numero_albo_veterinari'] ) )
			: '';

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$provincia_albo = isset( $_POST['billing_provincia_alb'] )
			? sanitize_text_field( wp_unslash( $_POST['billing_provincia_alb'] ) )
			: '';

		if ( $numero_albo ) {
			update_user_meta( $customer_id, 'billing_numero_albo_veterinari', $numero_albo );
		}

		if ( $provincia_albo ) {
			update_user_meta( $customer_id, 'billing_provincia_alb', $provincia_albo );
		}
	}

	/**
	 * Send admin notification about new wholesale registration
	 *
	 * @param int   $customer_id Customer ID.
	 * @param array $customer_data Customer data.
	 */
	private function send_admin_notification( int $customer_id, array $customer_data ): void {
		$admin_email = get_option( 'admin_email' );
		if ( ! $admin_email ) {
			return;
		}

		$subject = sprintf(
			/* translators: %s: Site name */
			__( '[%s] Nuova registrazione wholesale (veterinario)', 'tutoribalto-theme' ),
			get_bloginfo( 'name' )
		);

		$message = sprintf(
			/* translators: 1: Customer ID, 2: Email, 3: Username */
			__( "Nuova registrazione wholesale (veterinario)\n\nID utente: %1\$d\nEmail: %2\$s\nUsername: %3\$s\n\nControlla l'area admin per approvare l'utente.", 'tutoribalto-theme' ),
			$customer_id,
			$customer_data['user_email'] ?? '',
			$customer_data['user_login'] ?? ''
		);

		wp_mail( $admin_email, $subject, $message );
	}
}
