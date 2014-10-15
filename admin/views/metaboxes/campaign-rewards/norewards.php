<?php
global $post;

$campaign = eddcf_get_campaign( $post );
?>
<p>
	<label for="campaign_norewards">
		<input type="checkbox" name="campaign_norewards" id="campaign_norewards" value="1" <?php checked( 0, $campaign->has_rewards() ); ?>> <?php printf( __( 'This %s is donations only (no rewards)', 'atcf' ), strtolower( edd_get_label_singular() ) ); ?>
	</label>
</p>