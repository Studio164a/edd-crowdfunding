<?php
/**
 * Displays the campaign's custom pledge field.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

global $edd_options;

$campaign = eddcf_get_campaign();

if ( false === $campaign->is_crowdfunding_campaign() ) {
	return;
}

$symbol_left = ! isset( $edd_options['currency_position'] ) || $edd_options['currency_position'] == 'before';
$wrapper_class = $symbol_left ? 'currency-left' : 'currency-right'; 
?>
<!-- Text field with pledge button -->
<div class="campaign-price-input">
	<h4 class="pledge-title"><?php _e( 'Enter your own pledge amount', 'eddcf' ) ?></h4>
	<div class="price-wrapper <?php echo $wrapper_class ?>">
		<?php if ( $symbol_left ) : ?>
			<span class="currency"><?php echo eddcf_get_currency_symbol() ?></span>
			<input type="text" name="eddcf_custom_price" id="eddcf_custom_price" value="" />
		<?php else : ?>			
			<input type="text" name="eddcf_custom_price" id="eddcf_custom_price" value="" />
			<span class="currency"><?php echo eddcf_get_currency_symbol() ?></span>
		<?php endif ?>
	</div>
</div>
