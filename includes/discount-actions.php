<?php
/**
 * Discount Code Bulk Actions
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Sets up and stores a new discount code
 *
 * @since 1.0
 * @param array $data Discount code data
 * @uses edd_store_discount()
 * @return void
 */
function edd_dcg_add_discount( $data ) {
	if ( isset( $data['edd-dcg-discount-nonce'] ) && wp_verify_nonce( $data['edd-dcg-discount-nonce'], 'edd_dcg_discount_nonce' ) ) {
		// Setup the discount code details
		$posted = array();

		foreach ( $data as $key => $value ) {
			if ( $key != 'edd-dcg-discount-nonce' && $key != 'edd-action' && $key != 'edd-redirect' ) {
				if ( is_string( $value ) || is_int( $value ) )
					$posted[ $key ] = strip_tags( addslashes( $value ) );
				elseif ( is_array( $value ) )
					$posted[ $key ] = array_map( 'absint', $value );
			}
		}

		if (!isset($posted['number-codes'])) return;

		// Check number of codes is number and greater than 0
		if (floor($posted['number-codes']) == $posted['number-codes'] && $posted['number-codes'] > 0) {
			$code = $posted;
			unset($code['number-codes']);
			unset($code['code-type']);
			unset($code['code-limit']);

			$result = true;
			// Loop through and generate code, check code doesnt exist _edd_discount_code
			for ($i = 1; $i <= $posted['number-codes']; $i++) {
			    $code['name'] = $posted['name'] .'-'. $i;
			    $code['code'] = edd_dcg_create_code($posted['code-type'], $posted['code-limit']);
			    $code['status'] = 'active';
			    $result = edd_store_discount( $code );
			    if (!$result) break;
			}

			if ( $result ) {
				$args = array( 	'edd-message' => 'discounts_added',
								'edd-number' => $posted['number-codes'] );
				wp_redirect( add_query_arg( $args , $data['edd-redirect'] ) ); edd_die();
			} else {
				wp_redirect( add_query_arg( 'edd-message', 'discount_add_failed', $data['edd-redirect'] ) ); edd_die();
			}

		}
	}
}
add_action( 'edd_add_discount', 'edd_dcg_add_discount' );

function edd_dcg_create_code($type, $limit) {
	do {

		if ($type == 'hash') {
			$salt = md5(time().mt_rand());
			$code = substr($salt, 0, $limit);
		} else {
			if ($type == 'letters') $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    else $characters = '0123456789';
		    $code = '';
		    for ($i = 0; $i < $limit; $i++) {
		        $code .= $characters[rand(0, strlen($characters) - 1)];
		    }
		}
	}
	while (edd_dcg_code_exists($code));

	return $code;
}

function edd_dcg_code_exists($code) {
	global $wpdb;
	$wpdb->get_results(  $wpdb->prepare("SELECT meta_key FROM $wpdb->postmeta where meta_key='_edd_discount_code' and meta_value=%s", $code) );
	if ( ($wpdb->num_rows ) >0 ) return true;
	return false;
}