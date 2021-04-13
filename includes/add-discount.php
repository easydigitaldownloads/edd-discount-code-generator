<?php
/**
 * Add Bulk Discount Page
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form id="edd-add-discount" action="" method="POST">
	<?php do_action( 'edd_dcg_add_discount_form_top' ); ?>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="edd-number-codes"><?php esc_html_e( 'Number of Codes', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="number" id="edd-number-codes" name="number-codes" value="" class="small-text" min="1" />
					<p class="description"><?php esc_html_e( 'The number of codes to generate', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="edd-name"><?php esc_html_e( 'Name', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input name="name" id="edd-name" type="text" value="" class="regular-text"/>
					<p class="description"><?php esc_html_e( 'The name of this discount. This will have a number appended to it, e.g. Name-1', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="edd-code-limit"><?php esc_html_e( 'Code', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<select name="code-type" id="edd-code-type">
						<option value="hash"><?php esc_html_e( 'Hash', 'edd_dcg' ); ?></option>
						<option value="letters"><?php esc_html_e( 'Letters', 'edd_dcg' ); ?></option>
						<option value="number"><?php esc_html_e( 'Numbers', 'edd_dcg' ); ?></option>
					</select>
					<input type="number" id="edd-code-limit" name="code-limit" value="10" class="small-text"/>
					<p class="description"><?php esc_html_e( 'Enter a type of code and code length limit', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="edd-type"><?php esc_html_e( 'Type', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<select name="type" id="edd-type">
						<option value="percent"><?php esc_html_e( 'Percentage', 'edd_dcg' ); ?></option>
						<option value="flat"><?php esc_html_e( 'Flat amount', 'edd_dcg' ); ?></option>
					</select>
					<p class="description"><?php esc_html_e( 'The kind of discount to apply for this discount.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="edd-amount"><?php esc_html_e( 'Amount', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="text" id="edd-amount" name="amount" value=""/>
					<p class="description"><?php esc_html_e( 'The amount of this discount code.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="products">
						<?php
						/* translators: the singular product name. */
						printf( esc_html__( '%s Requirements', 'edd_dcg' ), edd_get_label_singular() );
						?>
					</label>
				</th>
				<td>
					<?php
					echo EDD()->html->product_dropdown(
						array(
							'name'        => 'products[]',
							'id'          => 'products',
							'selected'    => array(),
							'multiple'    => true,
							'chosen'      => true,
							'placeholder' => sprintf( esc_html__( 'Select %s', 'easy-digital-downloads' ), esc_html( edd_get_label_plural() ) ),
						)
					);
					?>
					<p>
						<label for="edd-product-condition" class="screen-reader-text"><?php esc_html_e( 'Condition', 'edd_dcg' ); ?></label>
						<select id="edd-product-condition" name="product_condition">
							<option value="all"><?php printf( __( 'Cart must contain all selected %s', 'edd_dcg' ), edd_get_label_plural() ); ?></option>
							<option value="any"><?php printf( __( 'Cart needs one or more of the selected %s', 'edd_dcg' ), edd_get_label_singular() ); ?></option>
						</select>
					</p>
					<p class="description"><?php printf( __( '%s required to be purchased for this discount.', 'edd_dcg' ), edd_get_label_plural() ); ?></p>

					<p>
						<input type="checkbox" id="edd-non-global-discount" name="not_global" value="1"/>
						<label for="edd-non-global-discount"><?php printf( __( 'Apply discount only to selected %s?', 'edd_dcg' ), edd_get_label_plural() ); ?></label>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="edd-start"><?php esc_html_e( 'Start date', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input name="start" id="edd-start" type="text" value="" class="edd_datepicker"/>
					<p class="description"><?php esc_html_e( 'Enter the start date for this discount code in the format of mm/dd/yyyy. For no start date, leave blank. If entered, the discount can only be used after or on this date.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="edd-expiration"><?php esc_html_e( 'Expiration date', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input name="expiration" id="edd-expiration" type="text" class="edd_datepicker"/>
					<p class="description"><?php esc_html_e( 'Enter the expiration date for this discount code in the format of mm/dd/yyyy. For no expiration, leave blank', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="edd-min-cart-amount"><?php esc_html_e( 'Minimum Amount', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="text" id="edd-min-cart-amount" name="min_price" value=""/>
					<p class="description"><?php esc_html_e( 'The minimum amount that must be purchased before this discount can be used. Leave blank for no minimum.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="edd-max-uses"><?php esc_html_e( 'Max Uses', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="text" id="edd-max-uses" name="max" value=""/>
					<p class="description"><?php esc_html_e( 'The maximum number of times this discount can be used. Leave blank for unlimited.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="edd-use-once"><?php esc_html_e( 'Use Once Per Customer', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="edd-use-once" name="use_once" value="1"/>
					<label for="edd-use-once"><?php esc_html_e( 'Limit this discount to a single-use per customer?', 'edd_dcg' ); ?></label>
				</td>
			</tr>
		</tbody>
	</table>
	<?php do_action( 'edd_dcg_add_discount_form_bottom' ); ?>
	<p class="submit">
		<input type="hidden" name="edd-action" value="add_discount"/>
		<input type="hidden" name="edd-redirect" value="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=edd-discounts' ) ); ?>"/>
		<?php wp_nonce_field( 'edd_dcg_discount_nonce', 'edd_dcg_discount_nonce' ); ?>
		<input type="submit" value="<?php esc_html_e( 'Create Codes', 'edd_dcg' ); ?>" class="button button-primary"/>
	</p>
</form>
