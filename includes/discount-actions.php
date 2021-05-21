<?php
/**
 * Discount Code Bulk Actions
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets up and stores a new discount code
 *
 * @since 1.0
 * @param array $data Discount code data
 * @uses edd_dcg_create_discount_codes()
 * @return void
 */
function edd_dcg_add_discount( $data ) {
	if ( empty( $data['edd_dcg_discount_nonce'] ) || ! wp_verify_nonce( $data['edd_dcg_discount_nonce'], 'edd_dcg_discount_nonce' ) ) {
		return;
	}

	$result = edd_dcg_create_discount_codes( $data );

	if ( true === $result ) {
		$args = array(
			'edd-message' => 'discounts_added',
			'edd-number'  => isset( $data['number-codes'] ) ? urlencode( $data['number-codes'] ) : 0,
		);
	} else {
		$args = array(
			'edd-message' => 'discount_add_failed',
		);
	}

	$redirect_base = ! empty( $data['edd-redirect'] ) ? $data['edd-redirect'] : admin_url();

	$url = add_query_arg( $args, $redirect_base );
	wp_safe_redirect( esc_url_raw( $url ) );
	edd_die();
}
add_action( 'edd_add_discount', 'edd_dcg_add_discount' );

/**
 * Creates discount codes from the supplied array of settings.
 *
 * @since 1.1.1
 *
 * @param array $data
 *
 * @return true|WP_Error
 */
function edd_dcg_create_discount_codes( $data ) {
	// Setup the discount code details
	$posted = array();

	foreach ( $data as $key => $value ) {
		if ( in_array( $key, array( 'edd_dcg_discount_nonce', 'edd-action', 'edd-redirect' ), true ) ) {
			continue;
		}
		if ( is_string( $value ) || is_int( $value ) ) {
			$posted[ $key ] = strip_tags( addslashes( $value ) );
		} elseif ( is_array( $value ) ) {
			$posted[ $key ] = array_map( 'absint', $value );
		}
	}

	if ( ! isset( $posted['number-codes'] ) ) {
		return new WP_Error( 'missing_number_codes', __( 'Please specify a number of codes to generate.', 'edd_dcg' ) );
	}
	if ( empty( $posted['name'] ) ) {
		return new WP_Error( 'missing_name', __( 'A discount name is required.', 'edd_dcg' ) );
	}

	// Check number of codes is number and greater than 0
	if ( floor( $posted['number-codes'] ) != $posted['number-codes'] || $posted['number-codes'] <= 0 ) {
		return new WP_Error( 'invalid_number_codes', __( 'The supplied number of codes is invalid. Please enter an integer greater than zero.', 'edd_dcg' ) );
	}

	$code = $posted;
	unset( $code['number-codes'] );
	unset( $code['code-type'] );
	unset( $code['code-limit'] );

	$fields_to_convert = array(
		'products'   => 'product_reqs',
		'min_price'  => 'min_charge_amount',
		'max'        => 'max_uses',
		'use_once'   => 'once_per_customer',
		'start'      => 'start_date',
		'expiration' => 'end_date',
	);
	if ( function_exists( 'edd_add_adjustment' ) ) {
		$code['scope'] = ! empty( $data['not_global'] ) ? 'not_global' : 'global';
		foreach ( $fields_to_convert as $edd2x => $edd30 ) {
			$code[ $edd30 ] = ! empty( $code[ $edd2x ] ) ? $code[ $edd2x ] : '';
			unset( $code[ $edd2x ] );
		}
	}
	$code_limit = ! empty( $posted['code-limit'] ) ? intval( $posted['code-limit'] ) : 10;

	$result = true;

	// Loop through and generate code, check code doesnt exist _edd_discount_code
	for ( $i = 1; $i <= $posted['number-codes']; $i++ ) {
		$code['name']   = $posted['name'] . '-' . $i;
		$code['code']   = edd_dcg_create_code( $posted['code-type'], $code_limit );
		$code['status'] = 'active';
		if ( function_exists( 'edd_add_adjustment' ) ) {
			$result = edd_add_discount( $code );
		} else {
			$result = edd_store_discount( $code );
		}
		if ( ! $result ) {
			$result = new WP_Error( 'discount_insert_failed', __( 'Failed to create discount code.', 'edd_dcg' ) );
			break;
		}
	}

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return true;
}

function edd_dcg_create_code( $type, $limit ) {
	do {

		if ( $type == 'hash' ) {
			$salt = md5( time() . mt_rand() );
			$code = substr( $salt, 0, $limit );
		} else {
			if ( $type == 'letters' ) {
				$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			} else {
				$characters = '0123456789';
			}
			$code = '';
			for ( $i = 0; $i < $limit; $i++ ) {
				$code .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
			}
		}
	} while ( edd_dcg_code_exists( $code ) );

	return $code;
}

/**
 * Checks if the generated code already exists in the database.
 *
 * @since unknown
 * @param string $code
 * @return bool
 */
function edd_dcg_code_exists( $code ) {
	global $wpdb;
	if ( function_exists( 'edd_add_adjustment' ) ) {
		$wpdb->get_results( $wpdb->prepare( "SELECT id FROM $wpdb->edd_adjustments where code=%s", $code ) );
	} else {
		$wpdb->get_results( $wpdb->prepare( "SELECT meta_key FROM $wpdb->postmeta where meta_key='_edd_discount_code' and meta_value=%s", $code ) );
	}
	if ( ( $wpdb->num_rows ) > 0 ) {
		return true;
	}
	return false;
}
