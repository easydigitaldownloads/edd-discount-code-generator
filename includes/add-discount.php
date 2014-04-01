<?php
/**
 * Add Bulk Discount Page
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<form id="edd-add-discount" action="" method="POST">
	<?php do_action( 'edd_dcg_add_discount_form_top' ); ?>
	<table class="form-table">
		<tbody>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-number-codes"><?php _e( 'Number of Codes', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="number" id="edd-number-codes" name="number-codes" value="" style="width: 40px;"/>
					<p class="description"><?php _e( 'The number of codes to generate', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-name"><?php _e( 'Name', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input name="name" id="edd-name" type="text" value="" style="width: 300px;"/>
					<p class="description"><?php _e( 'The name of this discount. This will have a number appended to it, e.g. Name-1', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-code-limit"><?php _e( 'Code', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<select name="code-type" id="edd-code-type">
						<option value="hash"><?php _e( 'Hash', 'edd_dcg' ); ?></option>
						<option value="letters"><?php _e( 'Letters', 'edd_dcg' ); ?></option>
						<option value="number"><?php _e( 'Numbers', 'edd_dcg' ); ?></option>
					</select>
					<input type="number" id="edd-code-limit" name="code-limit" value="10" style="width: 40px;"/>
					<p class="description"><?php _e( 'Enter a type of code and code length limit', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-type"><?php _e( 'Type', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<select name="type" id="edd-type">
						<option value="percent"><?php _e( 'Percentage', 'edd_dcg' ); ?></option>
						<option value="flat"><?php _e( 'Flat amount', 'edd_dcg' ); ?></option>
					</select>
					<p class="description"><?php _e( 'The kind of discount to apply for this discount.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-amount"><?php _e( 'Amount', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="text" id="edd-amount" name="amount" value="" style="width: 40px;"/>
					<p class="description"><?php _e( 'The amount of this discount code.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-products"><?php printf( __( '%s Requirements', 'edd_dcg' ), edd_get_label_plural() ); ?></label>
				</th>
				<td>
					<p>
						<select id="edd-product-condition" name="product_condition">
							<option value="all"><?php printf( __( 'All Selected %s', 'edd_dcg' ), edd_get_label_plural() ); ?></option>
							<option value="any"><?php printf( __( 'Any Selected %s', 'edd_dcg' ), edd_get_label_singular() ); ?></option>
						</select>
						<label for="edd-product-condition"><?php _e( 'Condition', 'edd_dcg' ); ?></label>
					</p>
					<select multiple id="edd-products" name="products[]" class="edd-select-chosen" data-placeholder="<?php printf( __( 'Choose one or more %s', 'edd_dcg' ), edd_get_label_plural() ); ?>">
						<?php
						$downloads = get_posts( array( 'post_type' => 'download', 'nopaging' => true ) );
						if( $downloads ) :
							foreach( $downloads as $download ) :
								echo '<option value="' . esc_attr( $download->ID ) . '">' . esc_html( get_the_title( $download->ID ) ) . '</option>';
							endforeach;
						endif;
						?>
					</select>
					<p class="description"><?php printf( __( '%s required to be purchased for this discount.', 'edd_dcg' ), edd_get_label_plural() ); ?></p>

					<p>
						<label for="edd-non-global-discount">
							<input type="checkbox" id="edd-non-global-discount" name="not_global" value="1"/>
							<?php printf( __( 'Apply discount only to selected %s?', 'edd_dcg' ), edd_get_label_plural() ); ?>
						</label>
					</p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-start"><?php _e( 'Start date', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input name="start" id="edd-start" type="text" value="" style="width: 120px;" class="edd_datepicker"/>
					<p class="description"><?php _e( 'Enter the start date for this discount code in the format of mm/dd/yyyy. For no start date, leave blank. If entered, the discount can only be used after or on this date.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-expiration"><?php _e( 'Expiration date', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input name="expiration" id="edd-expiration" type="text" style="width: 120px;" class="edd_datepicker"/>
					<p class="description"><?php _e( 'Enter the expiration date for this discount code in the format of mm/dd/yyyy. For no expiration, leave blank', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-min-cart-amount"><?php _e( 'Minimum Amount', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="text" id="edd-min-cart-amount" name="min_price" value="" style="width: 40px;"/>
					<p class="description"><?php _e( 'The minimum amount that must be purchased before this discount can be used. Leave blank for no minimum.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-max-uses"><?php _e( 'Max Uses', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="text" id="edd-max-uses" name="max" value="" style="width: 40px;"/>
					<p class="description"><?php _e( 'The maximum number of times this discount can be used. Leave blank for unlimited.', 'edd_dcg' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="edd-use-once"><?php _e( 'Use Once Per Customer', 'edd_dcg' ); ?></label>
				</th>
				<td>
					<input type="checkbox" id="edd-use-once" name="use_once" value="1"/>
					<span class="description"><?php _e( 'Limit this discount to a single-use per customer?', 'edd_dcg' ); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<?php do_action( 'edd_dcg_add_discount_form_bottom' ); ?>
	<p class="submit">
		<input type="hidden" name="edd-action" value="add_discount"/>
		<input type="hidden" name="edd-redirect" value="<?php echo esc_url( admin_url( 'edit.php?post_type=download&page=edd-discounts' ) ); ?>"/>
		<input type="hidden" name="edd-dcg-discount-nonce" value="<?php echo wp_create_nonce( 'edd_dcg_discount_nonce' ); ?>"/>
		<input type="submit" value="<?php _e( 'Create Codes', 'edd_dcg' ); ?>" class="button-primary"/>
	</p>
</form>
