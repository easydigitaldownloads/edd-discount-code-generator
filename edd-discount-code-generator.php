<?php
/**
 * Plugin Name: Easy Digital Downloads - Discount Code Generator
 * Plugin URL: https://easydigitaldownloads.com/downloads/discount-code-generator/
 * Description: Create discount codes in bulk.
 * Version: 1.2
 * Author: Sandhills Development, LLC
 * Author URI: https://sandhillsdev.com
 */


if( !class_exists( 'eddDev7DiscountCodeGenerator' ) ){

	class eddDev7DiscountCodeGenerator {

		private $plugin_name = 'Discount Code Generator';
		private $plugin_version;

	    function __construct() {

	    	$this->plugin_version = '1.2';

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

			add_action( 'edd_reports_tab_export_content_bottom', array( $this, 'edd_add_code_export' ) );

			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				require_once EDD_DCG_PLUGIN_DIR . 'includes/class-cli.php';
			}

			if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

				include_once EDD_DCG_PLUGIN_DIR . '/includes/export-functions.php';
				include_once EDD_DCG_PLUGIN_DIR . '/includes/admin-page.php';
				include_once EDD_DCG_PLUGIN_DIR . '/includes/discount-actions.php';

				if ( class_exists( 'EDD_License' ) ) {
					$edddcg_license = new EDD_License( __FILE__, $this->plugin_name, $this->plugin_version, 'Sandhills Development, LLC', null, null, 143801 );
				}
			}
		}

	    function edd_load_scripts_for_these_pages($pages) {
	    	$pages[] = 'download_page_edd-dc-generator';
			return $pages;
	    }

		/**
		 * Adds the discount code exporter to the Reports > Export screen.
		 *
		 * @return void
		 */
		public function edd_add_code_export() {
			?>
			<div class="postbox edd-export-discount-codes">
				<h2 class="hndle"><?php esc_html_e( 'Export Discount Codes in CSV', 'edd_dcg' ); ?></h2>
				<div class="inside">
					<p><?php esc_html_e( 'Download a CSV of all discount codes.', 'edd_dcg' ); ?></p>
					<form id="edd-dcg-export" method="post" class="edd-export-form edd-import-export-form">
						<?php wp_nonce_field( 'edd_ajax_export', 'edd_ajax_export' ); ?>
						<input type="hidden" name="edd-dcg-recent" value="<?php echo ( ! empty( $_GET['edd-dcg-recent'] ) ? (int) $_GET['edd-dcg-recent'] : '' ); ?>"/>
						<input type="hidden" name="edd-export-class" value="EDD_Discount_Codes_Export"/>
						<input type="submit" value="<?php esc_html_e( 'Generate CSV', 'edd_dcg' ); ?>" class="button-secondary"/>
						<span class="spinner"></span>
					</form>
				</div>
			</div>
			<?php
		}
	}
	$eddDev7DiscountCodeGenerator = new eddDev7DiscountCodeGenerator();
}
