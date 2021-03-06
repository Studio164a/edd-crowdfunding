<?php
/**
 * Displays the campaign content.
 *
 * @author 	Studio 164a
 * @since 	1.0.0
 */

global $post;

$campaign = eddcf_get_campaign();

/**
 * @hook edd_before_download_content
 */
do_action( 'edd_before_download_content', $post->ID );

/**
 * @hook eddcf_campaign_details
 */
do_action( 'eddcf_campaign_details' );

the_content();

/**
 * @hook edd_after_download_content
 */
do_action( 'edd_before_download_content', $post->ID );

