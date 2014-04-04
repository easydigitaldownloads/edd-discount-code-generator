<?php
/*
Plugin Name: Easy Digital Downloads - Discount Code Generator
Plugin URL: http://easydigitaldownloads.com/extension/coupon-generator
Description: Create discount codes in bulk.
Version: 1.1
Author: polevaultweb
Author URI: http://polevaultweb.com
*/


if( !class_exists( 'eddDev7DiscountCodeGenerator' ) ){

	class eddDev7DiscountCodeGenerator {

	    private $plugin_name = 'Discount Code Generator';
	    private $plugin_version;
	    private $plugin_author = 'polevaultweb';

	    function __construct() {

	    	$this->plugin_version = '1.1';

	    	if ( ! defined( 'EDD_DCG_PLUGIN_DIR' ) ) {
				define( 'EDD_DCG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'EDD_DCG_PLUGIN_URL' ) ) {
				define( 'EDD_DCG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'EDD_DCG_PLUGIN_FILE' ) ) {
				define( 'EDD_DCG_PLUGIN_FILE', __FILE__ );
			}

	        load_plugin_textdomain( 'edd_dcg', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

	        add_filter('edd_load_scripts_for_these_pages', array($this, 'edd_load_scripts_for_these_pages'));
	        add_filter('edd_load_scripts_for_discounts', array($this, 'edd_load_scripts_for_these_pages'));

	        add_action('edd_reports_tab_export_content_bottom', array($this, 'edd_add_code_export'));
	        add_action('edd_discount_codes_export', array($this, 'edd_export_all_discount_codes') );
	        add_action('edd_discount_codes_recent_export', array($this, 'edd_export_recent_discount_codes') );

			if( is_admin() ) {

				include_once( EDD_DCG_PLUGIN_DIR .'/includes/admin-page.php' );
				include_once( EDD_DCG_PLUGIN_DIR .'/includes/discount-actions.php' );

				if( class_exists( 'EDD_License' ) ) {
					$edddcg_license = new EDD_License( __FILE__, $this->plugin_name, $this->plugin_version, $this->plugin_author );
				}
			}

	    }

	    function edd_load_scripts_for_these_pages($pages) {
	    	$pages[] = 'download_page_edd-dc-generator';
			return $pages;
	    }

	    function edd_add_code_export() {
	    ?>
		    <div class="postbox">
				<h3><span><?php _e('Export Discount Codes in CSV', 'edd_dcg'); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Download a CSV of all discount codes.', 'edd_dcg' ); ?></p>
					<p>
						<form method="post">
							<input type="hidden" name="edd-action" value="discount_codes_export"/>
							<input type="submit" value="<?php _e( 'Generate CSV', 'edd_dcg' ); ?>" class="button-secondary"/>
						</form>
					</p>
				</div>
			</div>
		<?php
	    }

		function edd_export_all_discount_codes() {
			require_once EDD_DCG_PLUGIN_DIR . '/includes/class-export-discount-codes.php';
			$discount_code_export = new EDD_Discount_Codes_Export();
			$discount_code_export->export();
		}

		function edd_export_recent_discount_codes() {
			require_once EDD_DCG_PLUGIN_DIR . '/includes/class-export-discount-codes.php';
			$discount_code_export = new EDD_Discount_Codes_Export(true);
			$discount_code_export->export();
		}

	}
	$eddDev7DiscountCodeGenerator = new eddDev7DiscountCodeGenerator();
}
