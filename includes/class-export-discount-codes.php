<?php
/**
 * Discount Code Export Class
 *
 * This class handles discount codes export
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDD_Discount_Codes_Export extends EDD_Batch_Export {

	/**
	 * Whether the export should include just the most recently generated discount codes.
	 *
	 * @var boolean
	 */
	private $recent = false;

	/**
	 * The export type.
	 *
	 * @var string
	 */
	public $export_type = 'discount_codes';

	/**
	 * The columns for the CSV.
	 *
	 * @return array
	 */
	public function csv_cols() {
		return array(
			'name'       => __( 'Name', 'edd_dcg' ),
			'code'       => __( 'Code', 'edd_dcg' ),
			'amount'     => __( 'Amount', 'edd_dcg' ),
			'uses'       => __( 'Uses', 'edd_dcg' ),
			'max_uses'   => __( 'Max Uses', 'edd_dcg' ),
			'start_date' => __( 'Start Date', 'edd_dcg' ),
			'expiration' => __( 'Expiration', 'edd_dcg' ),
			'status'     => __( 'Status', 'edd_dcg' ),
		);
	}

	/**
	 * The data for the CSV.
	 *
	 * @return array
	 */
	public function get_data() {

		$data      = array();
		$args      = array(
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'paged'          => $this->step,
			'posts_per_page' => 100,
		);
		$discounts = edd_get_discounts( $args );
		if ( $this->recent ) {
			$args['posts_per_page'] = $this->recent;
			$last                   = edd_get_discounts( $args );
			$code_name              = $last[0]->post_name;
			$code_name              = substr( $code_name, 0, strpos( $code_name, '-' ) + 1 );
			$last_discounts         = array();
			foreach ( $discounts as $discount ) {
				if ( strpos( $discount->post_name, $code_name ) !== false ) {
					$last_discounts[] = $discount;
				}
			}
			$discounts = $last_discounts;
		}

		if ( ! $discounts ) {
			return false;
		}
		foreach ( $discounts as $discount ) {
			if ( edd_get_discount_max_uses( $discount->ID ) ) {
				$uses = edd_get_discount_uses( $discount->ID ) . '/' . edd_get_discount_max_uses( $discount->ID );
			} else {
				$uses = edd_get_discount_uses( $discount->ID );
			}

			$max_uses = __( 'Unlimited', 'edd_dcg' );
			if ( edd_get_discount_max_uses( $discount->ID ) ) {
				$max_uses = edd_get_discount_max_uses( $discount->ID ) ? edd_get_discount_max_uses( $discount->ID ) : __( 'unlimited', 'edd_dcg' );
			}

			$start_date          = edd_get_discount_start_date( $discount->ID );
			$discount_start_date = __( 'No start date', 'edd_dcg' );
			if ( ! empty( $start_date ) ) {
				$discount_start_date = date_i18n( get_option( 'date_format' ), strtotime( $start_date ) );
			}

			$expiration = __( 'No expiration', 'edd_dcg' );
			if ( edd_get_discount_expiration( $discount->ID ) ) {
				$expiration = edd_is_discount_expired( $discount->ID ) ? __( 'Expired', 'edd_dcg' ) : date_i18n( get_option( 'date_format' ), strtotime( edd_get_discount_expiration( $discount->ID ) ) );
			}

			$data[] = array(
				'ID'         => $discount->ID,
				'name'       => get_the_title( $discount->ID ),
				'code'       => edd_get_discount_code( $discount->ID ),
				'amount'     => edd_format_discount_rate( edd_get_discount_type( $discount->ID ), edd_get_discount_amount( $discount->ID ) ),
				'uses'       => $uses,
				'max_uses'   => $max_uses,
				'start_date' => $discount_start_date,
				'expiration' => $expiration,
				'status'     => ucwords( $discount->post_status ),
			);
		}

		$data = apply_filters( 'edd_export_get_data', $data );
		$data = apply_filters( 'edd_export_get_data_' . $this->export_type, $data );

		return $data;
	}

	/**
	 * Gets the percentage of the completed job.
	 *
	 * @return void
	 */
	public function get_percentage_complete() {

		$percentage = 100;
		if ( $this->recent ) {
			return $percentage;
		}
		$discounts = edd_get_discounts(
			array(
				'posts_per_page' => 999999,
				'fields'         => 'ids',
			)
		);
		$total     = count( $discounts );

		if ( $total > 0 ) {
			$percentage = ( ( 100 * $this->step ) / $total ) * 100;
		}

		if ( $percentage > 100 ) {
			$percentage = 100;
		}

		return $percentage;
	}

	public function set_properties( $request ) {
		if ( ! empty( $request['edd-dcg-recent'] ) ) {
			$this->recent = (int) $request['edd-dcg-recent'];
		}
	}
}
