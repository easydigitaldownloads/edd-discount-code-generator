<?php
/**
 * CLI Integration
 *
 * @package   edd-discount-code-generator
 * @copyright Copyright (c) 2021, Sandhills Development, LLC
 * @license   GPL2+
 * @since     1.1.1
 */

WP_CLI::add_command( 'edd generate:discounts', 'EDD_Discount_Code_Generator_CLI' );

class EDD_Discount_Code_Generator_CLI extends WP_CLI_Command {

	/**
	 * Generates discount codes.
	 *
	 * ## OPTIONS
	 *
	 * <name>
	 * : The name of this discount. This will have a number appended to it ( e.g. Name-1).
	 *
	 * [--number=<number>]
	 * : Number of codes to generate.
	 * ---
	 * default: 1
	 * ---
	 *
	 * [--code-type=<code_type>]
	 * : Type of code to generate.
	 * ---
	 * default: hash
	 * options:
	 *   - hash
	 *   - letters
	 *   - number
	 * ---
	 *
	 * [--code-limit=<code_limit>]
	 * : Number of characters to use for the code.
	 * ---
	 * default: 10
	 *
	 * [--amount=<amount>]
	 * : Amount for the discount. This is combined with amount-type.
	 *
	 * [--amount-type=<amount_type>]
	 * : Amount type for the discount.
	 * ---
	 * default: percent
	 * options:
	 *   - percent
	 *   - flat
	 * ---
	 *
	 * [--products=<product_ids>]
	 * : Comma-separated list of product IDs that the discounts will apply to.
	 *
	 * [--product-condition=<condition>]
	 * : Condition for discount to apply to the products.
	 * ---
	 * default: all
	 * options:
	 *    - all
	 *    - any
	 *
	 * [--scope=<scope>]
	 * : A global scope applies the discount to the entire cart. A not_global scope only applies the discount
	 * to the specified products.
	 * ---
	 * default: global
	 * options:
	 *     - global
	 *     - not_global
	 * ---
	 *
	 * [--start=<start>]
	 * : The start date for the discount code. If omitted, the discount can be used on or after today.
	 *
	 * [--expiration=<expiration>]
	 * : Expiration date of the discounts. If omitted, discounts never expire.
	 *
	 * [--min-price=<min_price>]
	 * : Minimum charge amount before the discount can apply.
	 *
	 * [--max-uses=<max_uses>]
	 * : The maximum number of times this discount can be used. Omit for unlimited.
	 *
	 * [--once]
	 * : Limits the discount to a single-use per customer.
	 *
	 * @param array $args
	 * @param array $assoc_args
	 */
	public function __invoke( $args, $assoc_args ) {
		$final_args = $assoc_args;

		if ( ! isset( $args[0] ) ) {
			WP_CLI::error( esc_html__( 'A discount name is required.', 'edd_dcg' ) );
		}

		$final_args['name'] = $args[0];

		// We need to convert the format of a few args.
		$fields_to_convert = array(
			'number'            => 'number-codes',
			'min-price'         => 'min_price',
			'max-uses'          => 'max',
			'product-condition' => 'product_condition',
			'amount-type'       => 'type',
		);
		foreach ( $fields_to_convert as $cli_key => $function_key ) {
			if ( array_key_exists( $cli_key, $assoc_args ) ) {
				$final_args[ $function_key ] = $assoc_args[ $cli_key ];
				unset( $final_args[ $cli_key ] );
			}
		}

		$final_args['use_once'] = ! empty( $assoc_args['once'] ) ? 1 : 0;

		// Convert `products` to an array.
		if ( ! empty( $final_args['products'] ) ) {
			$final_args['products']   = array_map( 'intval', explode( ',', $final_args['products'] ) );
			$final_args['not_global'] = ! empty( $final_args['scope'] ) && 'not_global' === $final_args['scope'] ? 1 : 0;

			unset( $final_args['scope'] );
		}

		WP_CLI::line( esc_html__( 'Creating discount codes...', 'edd_dcg' ) );

		$result = edd_dcg_create_discount_codes( $final_args );

		if ( is_wp_error( $result ) ) {
			WP_CLI::error( esc_html( $result->get_error_message() ) );
		} elseif ( is_numeric( $result ) ) {
			WP_CLI::success( sprintf(
				_n( '%d discount code successfully created.', '%d discount codes successfully created.', $result, 'edd_dcg' ),
				$result
			) );
		} else {
			// We should never end up here.
			WP_CLI::error( esc_html__( 'An unexpected error has occurred.', 'edd_dcg' ) );
		}
	}

}
