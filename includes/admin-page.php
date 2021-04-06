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

function edd_dcg_admin_messages() {
	global $edd_options;

	if ( isset( $_GET['edd-message'] ) && 'discounts_added' == $_GET['edd-message'] && current_user_can( 'manage_shop_discounts' ) ) {
		 $url = admin_url( 'edit.php?post_type=download&edd-action=discount_codes_recent_export' );
		 $message = $_GET['edd-number'] .' '. __( 'codes generated', 'edd_dcg' ) .'. <a href="'. $url .'">'. __( 'Export to CSV', 'edd_dcg' ) .'</a>';
		 add_settings_error( 'edd-dcg-notices', 'edd-discounts-added', $message, 'updated' );
		 settings_errors( 'edd-dcg-notices' );
	}

}
add_action( 'admin_notices', 'edd_dcg_admin_messages', 10 );
