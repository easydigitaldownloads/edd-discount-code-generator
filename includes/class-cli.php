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
	 * [--number=<number>]
	 * : Number of codes to generate.
	 * ---
	 * default: 1
	 * ---
	 *
	 * [--name=<name>]
	 * : The name of this discount. This will have a number appended to it ( e.g. Name-1).
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

	}

}
