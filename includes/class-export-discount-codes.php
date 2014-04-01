<?php
/**
 * Discount Code Export Class
 *
 * This class handles discount codes export
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


class EDD_Discount_Codes_Export extends EDD_Export {

	private $recent;
	public $export_type = 'discount_codes';

	function __construct($recent = false) {
		$this->recent = $recent;
	}

	public function csv_cols() {
		$cols = array(
			'name'  	=> __( 'Name', 'edd_dcg' ),
			'code'  	=> __( 'Code', 'edd_dcg' ),
			'amount'  	=> __( 'Amount', 'edd_dcg' ),
			'uses'  	=> __( 'Uses', 'edd_dcg' ),
			'max_uses' 	=> __( 'Max Uses', 'edd_dcg' ),
			'start_date'=> __( 'Start Date', 'edd_dcg' ),
			'expiration'=> __( 'Expiration', 'edd_dcg' ),
			'status'  	=> __( 'Status', 'edd_dcg' ),
		);

		return $cols;
	}

	public function get_data() {

		$discount_codes_data = array();
		$number = -1;
		$args = array( 	'orderby'          => 'ID',
						'order'            => 'DESC',
						'posts_per_page' => $number
					);
		$discounts = edd_get_discounts( $args );
		if ($this->recent) {
			$number = 1;
			$last = edd_get_discounts( $args );
			$code_name = $last[0]->post_name;
			$code_name = substr($code_name, 0, strpos($code_name, '-') + 1);
			$last_discounts = array();
			foreach ( $discounts as $discount ) {
				if ( strpos($discount->post_name, $code_name) !== false ) {
					$last_discounts[] = $discount;
				}
			}
			$discounts = $last_discounts;
		}

		if ( $discounts ) {
			foreach ( $discounts as $discount ) {
				if ( edd_get_discount_max_uses( $discount->ID ) ) {
					$uses =  edd_get_discount_uses( $discount->ID ) . '/' . edd_get_discount_max_uses( $discount->ID );
				} else {
					$uses = edd_get_discount_uses( $discount->ID );
				}

				if ( edd_get_discount_max_uses( $discount->ID ) ) {
					$max_uses = edd_get_discount_max_uses( $discount->ID ) ? edd_get_discount_max_uses( $discount->ID ) : __( 'unlimited', 'edd_dcg' );
				} else {
					$max_uses = __( 'Unlimited', 'edd_dcg' );
				}

				$start_date = edd_get_discount_start_date( $discount->ID );

				if ( ! empty( $start_date ) ) {
					$discount_start_date =  date_i18n( get_option( 'date_format' ), strtotime( $start_date ) );
				} else {
					$discount_start_date = __( 'No start date', 'edd_dcg' );
				}

				if ( edd_get_discount_expiration( $discount->ID ) ) {
					$expiration = edd_is_discount_expired( $discount->ID ) ? __( 'Expired', 'edd_dcg' ) : date_i18n( get_option( 'date_format' ), strtotime( edd_get_discount_expiration( $discount->ID ) ) );
				} else {
					$expiration = __( 'No expiration', 'edd_dcg' );
				}

				$discount_codes_data[] = array(
					'ID' 			=> $discount->ID,
					'name' 			=> get_the_title( $discount->ID ),
					'code' 			=> edd_get_discount_code( $discount->ID ),
					'amount' 		=> edd_format_discount_rate( edd_get_discount_type( $discount->ID ), edd_get_discount_amount( $discount->ID ) ),
					'uses' 			=> $uses,
					'max_uses' 		=> $max_uses,
					'start_date' 	=> $discount_start_date,
					'expiration'	=> $expiration,
					'status'		=> ucwords( $discount->post_status ),
				);
			}
		}

		$data = apply_filters( 'edd_export_get_data', $discount_codes_data );
		$data = apply_filters( 'edd_export_get_data_' . $this->export_type, $discount_codes_data );


		return $discount_codes_data;
	}

}
