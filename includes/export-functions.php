<?php

/**
 * Registers the discount codes batch exporter.
 *
 * @since 1.1.1
 * @return void
 */
function edd_dcg_register_discount_batch_export() {
	add_action( 'edd_batch_export_class_include', 'edd_dcg_include_discount_batch_processor', 10, 1 );
}
add_action( 'edd_register_batch_exporter', 'edd_dcg_register_discount_batch_export', 10 );

/**
 * Loads the export class file.
 *
 * @since 1.1.1
 * @param string $class
 * @return void
 */
function edd_dcg_include_discount_batch_processor( $class ) {
	if ( 'EDD_Discount_Codes_Export' === $class ) {
		require_once EDD_DCG_PLUGIN_DIR . 'includes/class-export-discount-codes.php';
	}
}

/**
 * Runs the discount code batch exporter.
 *
 * @since 1.1.1
 * @return void
 */
function edd_dcg_batch_exporter() {
	$export = new EDD_Discount_Codes_Export();
	$export->export();
}
add_action( 'edd_discount_codes_batch_export', 'edd_dcg_batch_exporter' );
