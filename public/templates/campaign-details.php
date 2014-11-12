<?php
/**
 * Displays the campaign details.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

$campaign = eddcf_get_campaign();
?>
<div class="campaign-details">
<?php
	/**
	 * @hook eddcf_before_campaign_details
	 */
	do_action( 'eddcf_before_campaign_details', $campaign->ID ); 
	?>
	<div class="percent-raised"><?php 
		printf( __( '%s raised', 'eddcf' ), 
			'<span class="percent">' . $campaign->percent_completed() . '</span>' );
	?></div>
	<div class="campaign-figures"><?php 
		printf( __( '%s pledged of %s goal', 'eddcf' ), 
			'<span class="amount-raised">' . $campaign->current_amount() . '</span>', 
			'<span class="goal">' . $campaign->goal() . '</span>' );
	?></div>
	<div class="campaign-backers"><?php 
		printf( __( '%s backers', 'eddcf' ), 
			'<span class="backers-count">' . $campaign->backers_count() . '</span>' );
	?></div>
	<div class="campaign-expiry"><?php 
		echo $campaign->time_remaining(); 
	?></div>
	<div class="pledge-button">
		<a href="#pledge" class="button button-primary"><?php echo eddcf_get_pledge_text() ?></a>
	</div>
	<?php
	/**
	 * @hook eddcf_after_campaign_details
	 */
	do_action( 'eddcf_after_campaign_details', $campaign->ID );
?>
</div>