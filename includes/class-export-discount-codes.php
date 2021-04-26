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

		$data = array();
		$args = array(
			'order'  => 'DESC',
			'offset' => ( $this->step * 30 ) - 30,
		);
		/**
		 * If exporting just the recent codes, check the current offset
		 * and return early if we are past the number of recent codes.
		 */
		if ( $this->recent && $this->step > 1 && $args['offset'] > $this->recent ) {
			return false;
		}
		if ( function_exists( 'edd_get_adjustments' ) ) {
			$args['type']    = 'discount';
			$args['orderby'] = 'id';
			$discounts       = edd_get_adjustments( $args );
		} else {
			$args['orderby']        = 'ID';
			$args['posts_per_page'] = 30;
			$discounts              = edd_get_discounts( $args );
			if ( ! $discounts ) {
				return false;
			}
		}

		if ( ! $discounts ) {
			return false;
		}
		$i = $args['offset'];
		foreach ( $discounts as $discount ) {
			$i++;
			if ( $this->recent && $i > $this->recent ) {
				break;
			}
			$uses     = edd_get_discount_uses( $discount->ID );
			$max_uses = edd_get_discount_max_uses( $discount->ID );
			if ( $max_uses ) {
				$uses = $uses . '/' . $max_uses;
			} else {
				$max_uses = __( 'Unlimited', 'edd_dcg' );
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
				'name'       => ! empty( $discount->name ) ? $discount->name : get_the_title( $discount->ID ),
				'code'       => edd_get_discount_code( $discount->ID ),
				'amount'     => edd_format_discount_rate( edd_get_discount_type( $discount->ID ), edd_get_discount_amount( $discount->ID ) ),
				'uses'       => $uses,
				'max_uses'   => $max_uses,
				'start_date' => $discount_start_date,
				'expiration' => $expiration,
				'status'     => ucwords( ! empty( $discount->status ) ? $discount->status : $discount->post_status ),
			);
		}

		$data = apply_filters( 'edd_export_get_data', $data );
		$data = apply_filters( 'edd_export_get_data_' . $this->export_type, $data );

		return $data;
	}

	/**
	 * Gets the percentage of the completed job.
	 *
	 * @since 1.1.1
	 * @return void
	 */
	public function get_percentage_complete() {

		$percentage = 100;
		if ( $this->recent ) {
			return $percentage;
		}
		if ( function_exists( 'edd_count_adjustments' ) ) {
			$total = edd_count_adjustments(
				array(
					'type' => 'discount',
				)
			);
		} else {
			$discounts = edd_get_discounts(
				array(
					'posts_per_page' => -1,
					'fields'         => 'ids',
				)
			);
			$total     = is_array( $discounts ) ? count( $discounts ) : 0;
		}

		if ( $total > 0 ) {
			$percentage = ( ( 100 * $this->step ) / $total ) * 100;
		}

		if ( $percentage > 100 ) {
			$percentage = 100;
		}

		return $percentage;
	}

	/**
	 * Sets the properties for the exporter class.
	 *
	 * @since 1.1.1
	 * @param array $request
	 * @return void
	 */
	public function set_properties( $request ) {
		if ( ! empty( $request['edd-dcg-recent'] ) ) {
			$this->recent = (int) $request['edd-dcg-recent'];
		}
	}
}
