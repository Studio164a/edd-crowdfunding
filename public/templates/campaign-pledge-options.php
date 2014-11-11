<?php
/**
 * Displays the campaign's pledge form. 
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

$campaign = eddcf_get_campaign();
?>
<div class="campaign-pledge-options">
<?php
	/**
	 * @hook edd_before_price_options
	 */
	do_action( 'edd_before_price_options', $campaign->ID ); 

	/**
	 * @hook eddcf_campaign_pledge_options
	 */
	do_action( 'eddcf_campaign_pledge_options' );

	/**
	 * @hook edd_after_price_options
	 */
	do_action( 'edd_after_price_options', $campaign->ID );
?>
</div>