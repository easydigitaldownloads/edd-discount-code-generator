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
	global $edd_options;

	if ( isset( $_GET['edd-message'] ) && 'discounts_added' == $_GET['edd-message'] && current_user_can( 'manage_shop_discounts' ) ) {
		 $url = admin_url( 'edit.php?post_type=download&edd-action=discount_codes_recent_export' );
		 $message = $_GET['edd-number'] .' '. __( 'codes generated', 'edd_dcg' ) .'. <a href="'. $url .'">'. __( 'Export to CSV', 'edd_dcg' ) .'</a>';
		 add_settings_error( 'edd-dcg-notices', 'edd-discounts-added', $message, 'updated' );
		 settings_errors( 'edd-dcg-notices' );
	}

}
add_action( 'admin_notices', 'edd_dcg_admin_messages', 10 );