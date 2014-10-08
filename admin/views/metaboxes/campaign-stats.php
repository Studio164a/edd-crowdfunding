<?php
global $post;

$campaign = eddcf_get_campaign( $post );

do_action( 'eddcf_metabox_campaign_stats_before', $campaign );
?>
<p>
	<strong><?php _e( 'Current Amount:', 'eddcf' ); ?></strong>
	<?php echo $campaign->current_amount(); ?> &mdash; <?php echo $campaign->percent_completed(); ?>
</p>

<p>
	<strong><?php _e( 'Backers:' ,'eddcf' ); ?></strong>
	<?php echo $campaign->backers_count(); ?>
</p>


<?php if ( ! $campaign->is_endless() ) : ?>
<p>
	<strong><?php _e( 'Days Remaining:', 'eddcf' ); ?></strong>
	<?php //echo $campaign->days_remaining(); ?>
</p>
<?php endif; ?>
<?php
do_action( 'eddcf_metabox_campaign_stats_after', $campaign );