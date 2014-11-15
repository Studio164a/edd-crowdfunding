<?php
global $post;

$campaign = eddcf_get_campaign( $post );
?>
<p>
	<label for="campaign_norewards">
		<input type="checkbox" name="campaign_norewards" id="campaign_norewards" value="1" <?php checked( $campaign->is_donations_only() ); ?>>
		<?php printf( __( 'This %s is donations only (no rewards)', 'eddcf' ), strtolower( edd_get_label_singular() ) ) ?>
	</label>
</p>