<?php 
global $post;

$campaign = eddcf_get_campaign( $post );
?>
<label id="campaign-goal-prompt-text" for="campaign_goal">
	<h4><?php echo esc_html( __( 'Campaign Goal', 'eddcf' ), $post ) ?></h4>
</label><!-- #campaign-goal-prompt-text -->
<span class="input-wrapper">$<input type="text" name="campaign_goal" size="30" value="" id="campaign-goal" autocomplete="off" /></span>
