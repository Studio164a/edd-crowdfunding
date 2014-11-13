<?php
/**
 * Displays the campaign's custom pledge field.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

global $edd_options;

$symbol_left = ! isset( $edd_options['currency_position'] ) || $edd_options['currency_position'] == 'before';
$wrapper_class = $symbol_left ? 'currency-left' : 'currency-right'; 
?>
<h2 class="pledge-section-header"><?php _e( 'Make a Donation', 'eddcf' ) ?></h2>
<div class="campaign-price-input <?php echo $wrapper_class ?>">	
	<?php if ( $symbol_left ) : ?>
		<span class="currency"><?php echo eddcf_get_currency_symbol() ?></span>
		<input type="text" name="eddcf_donation" id="eddcf_donation" value="" />
	<?php else : ?>			
		<input type="text" name="eddcf_donation" id="eddcf_donation" value="" />
		<span class="currency"><?php echo eddcf_get_currency_symbol() ?></span>
	<?php endif ?>
</div>
<input type="radio" name="edd_options[price_id][]" id="edd_price_option_<?php echo $campaign->ID ?>_0" class="edd_price_option_<?php echo $campaign->ID ?>" value="0" checked style="display: none;" />