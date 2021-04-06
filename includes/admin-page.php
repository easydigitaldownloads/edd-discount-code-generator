<?php

/**
 * Add Coupon Generator link
 * *
 * @access      private
 * @since       1.0
 * @return      void
*/

function edd_dcg_add_licenses_link() {

	global $edd_dcg_licenses_page;

	$edd_dcg_licenses_page = add_submenu_page( 'edit.php?post_type=download', __( 'Easy Digital Download Discount Code Generator', 'edd_dcg' ), __( 'Code Generator', 'edd_dcg' ), 'manage_options', 'edd-dc-generator', 'edd_dcg_page' );
	remove_submenu_page( 'edit.php?post_type=download', 'edd-dc-generator' );

}
add_action( 'admin_menu', 'edd_dcg_add_licenses_link', 10 );

function edd_dcg_add_bulk_link() {
	$url = admin_url('edit.php?post_type=download&page=edd-dc-generator');
	$html = '<a class="button" href="'. $url .'">'. __('Generate Codes', 'edd_dcg') .'</a>';
	echo $html;
}

add_action( 'edd_discounts_page_top', 'edd_dcg_add_bulk_link' );

function edd_dcg_page() {
    ?>
    <div class="wrap">
        <h2><?php _e( 'Discount Code Generator', 'edd_dcg' ); ?></h2>
		<?php
        require_once EDD_DCG_PLUGIN_DIR . 'includes/add-discount.php';
        ?>
    </div>
    <?php
}

function edd_dcg_admin_messages() {
	$number = ! empty( $_GET['edd-number'] ) ? (int) $_GET['edd-number'] : false;

	if ( ! $number || ! current_user_can( 'manage_shop_discounts' ) ) {
		return;
	}

	if ( empty( $_GET['edd-message'] ) || 'discounts_added' !== $_GET['edd-message'] ) {
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
		<input type="submit" value="<?php esc_html_e( 'Generate CSV', 'edd_dcg' ); ?>" class="button-secondary"/>
		<span class="spinner"></span>
	</form>
	<?php
	$message = ob_get_clean();
	add_settings_error( 'edd-dcg-notices', 'edd-discounts-added', $message, 'updated' );
	settings_errors( 'edd-dcg-notices' );
}
add_action( 'admin_notices', 'edd_dcg_admin_messages', 10 );
