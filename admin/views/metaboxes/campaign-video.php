<?php 
global $post;

$campaign = eddcf_get_campaign( $post );

do_action( 'eddcf_metabox_campaign_video_before', $campaign );
?>
<input type="text" name="campaign_video" id="campaign_video" class="widefat" value="<?php echo esc_url( $campaign->video() ); ?>" />
<p class="description"><?php _e( 'oEmbed supported video links.', 'eddcf' ); ?></p>
<?php
do_action( 'eddcf_metabox_campaign_video_after', $campaign );