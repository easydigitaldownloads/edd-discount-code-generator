<?php

/**
 * Add Coupon Generator link
 * *
 * @access      private
 * @since       1.0
 * @return      void
*/

/**
 * Adds a hidden discount code generator screen.
 *
 * @return void
 */
function edd_dcg_add_licenses_link() {
	add_submenu_page( null, __( 'Easy Digital Download Discount Code Generator', 'edd_dcg' ), __( 'Code Generator', 'edd_dcg' ), 'manage_options', 'edd-dc-generator', 'edd_dcg_page' );
}
add_action( 'admin_menu', 'edd_dcg_add_licenses_link', 10 );

/**
 * Renders the link to the discount code generator screen.
 *
 * @return void
 */
function edd_dcg_add_bulk_link() {
	$url = add_query_arg(
		array(
			'post_type' => 'download',
			'page'      => 'edd-dc-generator',
		),
		admin_url( 'edit.php' )
	);
	printf(
		'<a class="button button-secondary" href="%1$s">%2$s</a>',
		esc_url( $url ),
		esc_html__( 'Generate Codes', 'edd_dcg' )
	);
}
add_action( 'edd_discounts_page_top', 'edd_dcg_add_bulk_link' );

/**
 * Outputs the discount code generator page.
 *
 * @return void
 */
function edd_dcg_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Discount Code Generator', 'edd_dcg' ); ?></h1>
		<hr class="wp-header-end">
		<?php
		require_once EDD_DCG_PLUGIN_DIR . 'includes/add-discount.php';
		?>
	</div>
	<?php
}

/**
 * Outputs the admin message after codes have been successfully generated.
 *
 * @return void
 */
function edd_dcg_admin_messages() {
	$number = edd_dcg_code_generation_was_successful();
	if ( ! $number ) {
		return;
	}

	ob_start();
	printf(
		/* translators: the number of discount codes generated. */
		esc_html__( '%s codes generated.', 'edd_dcg' ),
		(int) $number
	);
	?>
	<form id="edd-dcg-export" method="post" class="edd-export-form edd-import-export-form">
		<?php wp_nonce_field( 'edd_ajax_export', 'edd_ajax_export' ); ?>
		<input type="hidden" name="edd-dcg-recent" value="<?php echo ( (int) $number ); ?>"/>
		<input type="hidden" name="edd-export-class" value="EDD_Discount_Codes_Export"/>
		<input type="submit" value="<?php esc_attr_e( 'Generate CSV', 'edd_dcg' ); ?>" class="button-secondary"/>
		<span class="spinner"></span>
	</form>
	<?php
	$message = ob_get_clean();
	add_settings_error( 'edd-dcg-notices', 'edd-discounts-added', $message, 'updated' );
	settings_errors( 'edd-dcg-notices' );
}
add_action( 'admin_notices', 'edd_dcg_admin_messages', 10 );

/**
 * Enqueues the export tools script in EDD 3.0.
 *
 * @since 1.1.1
 * @return void
 */
function edd_dcg_enqueue_export_script() {
	if ( ! edd_dcg_code_generation_was_successful() ) {
		return;
	}
	wp_enqueue_script( 'edd-admin-tools-export' );
}
add_action( 'admin_enqueue_scripts', 'edd_dcg_enqueue_export_script' );

/**
 * Determines whether discount code generation was successful.
 * Returns false if the current user does not have sufficient permissions.
 *
 * @since 1.1.1
 * @return bool|int False if not; otherwise returns the number of codes generated.
 */
function edd_dcg_code_generation_was_successful() {
	$number = ! empty( $_GET['edd-number'] ) ? (int) $_GET['edd-number'] : false;

	if ( ! $number || ! current_user_can( 'manage_shop_discounts' ) ) {
		return false;
	}

	if ( empty( $_GET['edd-message'] ) || 'discounts_added' !== $_GET['edd-message'] ) {
		return false;
	}

	return $number;
}
