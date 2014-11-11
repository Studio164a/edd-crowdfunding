<?php

/**
 * Send deprecated notices for any ATCF hooks / filters still being used
 *
 * @version		1.0.0
 * @package		EDD Crowdfunding/Other/ATCF Compatibility
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Hooks
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Checks to see if any filters have been registered for the given hook. If so, it sends a deprecated notice. 
 *
 * @param 		string $hook 			The hook to check for.
 * @param 		string $replacement 	Optional. Replacement method to use.
 * @return 		void
 * @since 		1.0.0
 */
function eddcf_deprecated_hook( $hook, $replacement = '' ) {
	if ( has_filter( $hook ) ) {
		_deprecated_function( "$hook hook", '1.0.0', $replacement );
	}
}

/**
 * Check for active filters on all the Astoundify hooks. 
 *
 * @return 		void
 * @since 		1.0.0
 */
function eddcf_check_deprecated_hooks() {
	eddcf_deprecated_hook( 'atcf_include_files' );
	eddcf_deprecated_hook( 'atcf_include_admin_files' );
	eddcf_deprecated_hook( 'atcf_setup_actions' );
	eddcf_deprecated_hook( 'atcf_found_edit' );
	eddcf_deprecated_hook( 'atcf_found_widget', 'eddcf_found_widget' );
	eddcf_deprecated_hook( 'atcf_found_single', 'eddcf_found_single' );
	eddcf_deprecated_hook( 'atcf_found_archive', 'eddcf_found_archive' );
	eddcf_deprecated_hook( 'atcf_campaigns_actions' );
	eddcf_deprecated_hook( 'atcf_campaigns_actions_admin' );
	eddcf_deprecated_hook( 'atcf_metabox_campaign_stats_before' );
	eddcf_deprecated_hook( 'atcf_metabox_campaign_stats_after' );
	eddcf_deprecated_hook( 'atcf_metabox_campaign_video_before' );
	eddcf_deprecated_hook( 'atcf_metabox_campaign_video_after' );
	eddcf_deprecated_hook( 'atcf_metabox_campaign_updates_before' );
	eddcf_deprecated_hook( 'atcf_metabox_campaign_updates_after' );
	eddcf_deprecated_hook( 'atcf_metabox_campaign_info_before' );
	eddcf_deprecated_hook( 'atcf_metabox_campaign_info_after' );
	eddcf_deprecated_hook( 'atcf_campaign_expired' );
	//eddcf_deprecated_hook( 'atcf_process_payment_' . $gateway );
	eddcf_deprecated_hook( 'atcf_failed_payment' );
	eddcf_deprecated_hook( 'atcf_shortcode_login' );
	eddcf_deprecated_hook( 'atcf_shortcode_profile' );
	eddcf_deprecated_hook( 'atcf_shortcode_profile_info_before' );
	eddcf_deprecated_hook( 'atcf_profile_info_fields' );
	eddcf_deprecated_hook( 'atcf_shortcode_profile_bio_after' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_before' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_after_title' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_pending_before' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_pending_after' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_draft_before' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_draft_after' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_published_before' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_actions_all' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_actions_special' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_published_after' );
	eddcf_deprecated_hook( 'atcf_profile_campaign_after' );
	eddcf_deprecated_hook( 'atcf_shortcode_profile_info_process_validate' );
	eddcf_deprecated_hook( 'atcf_shortcode_profile_info_process_after' );
	eddcf_deprecated_hook( 'atcf_shortcode_register' );
	eddcf_deprecated_hook( 'atcf_register_process_after' );
	//eddcf_deprecated_hook( 'atcf_shortcode_submit_save_field_' . $key, $key, $field, $campaign, $fields );
	eddcf_deprecated_hook( 'atcf_shortcode_submit_hidden' );
	eddcf_deprecated_hook( 'atcf_shortcode_submit_before' );
	//eddcf_deprecated_hook( 'atcf_shortcode_submit_field_before_' . $key, $key, $field, $args, $args['campaign'] );
	//eddcf_deprecated_hook( 'atcf_shortcode_submit_field_' . $field[ 'type' ], $key, $field, $args, $args['campaign'] );
	//eddcf_deprecated_hook( 'atcf_shortcode_submit_field_after_' . $key, $key, $field, $args, $args['campaign'] );
	eddcf_deprecated_hook( 'atcf_shortcode_submit_after' );
	eddcf_deprecated_hook( 'atcf_shortcode_submit_field_rewards_list_before' );
	eddcf_deprecated_hook( 'atcf_shortcode_submit_field_rewards_before' );
	eddcf_deprecated_hook( 'atcf_shortcode_submit_field_rewards_after' );
	eddcf_deprecated_hook( 'atcf_campaign_submit_validate' );
	eddcf_deprecated_hook( 'atcf_submit_process_after' );
	eddcf_deprecated_hook( 'atcf_campaign_contribute_options' );
	//eddcf_deprecated_hook( 'atcf_campaign_notes_before_' . $campaign->type() );
}

add_action( 'init', 'eddcf_check_deprecated_hooks', 99999 );