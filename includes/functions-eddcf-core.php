<?php

/**
 * Core EDD Crowdfunding functions.
 *
 * @version		1.0.0
 * @package		EDD Crowdfunding/Functions/Core
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Functions
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the current campaign's EDDCF_Campaign object. 
 *
 * @param 	int 	$campaign_id 	The Post ID of the campaign. If not set, uses the current $post object.
 * @return 	EDDCF_Campaign
 * @since 	1.0.0
 */
function eddcf_get_campaign( $campaign_id = "" ) {
	if ( ! isset( $campaign_id ) ) {
		$campaign_id = get_the_ID();
	}

	if ( false === $campaign_id ) {
		return;
	}

	$campaign = new EDDCF_Campaign( $campaign_id );

	return apply_filters( 'eddcf_get_campaign', $campaign, $campaign_id );
} 

/**
 * Returns whether the current theme supports a specific aspect of the functionality. 
 *
 * @param 	string 		$feature 	The name of the feature to check.
 * @return 	boolean
 * @since 	1.0.0
 */
function eddcf_theme_supports( $feature ) {
	$supports = get_theme_support( 'edd-crowdfunding' );
	$supports = $supports[0];

	return isset( $supports[ $feature ] );
}

/**
 * Returns the one instance of EDDCF_Gateways. 
 *
 * @return 	EDDCF_Gateways	
 * @since 	1.0.0
 */
function eddcf_gateways() {
	return EDDCF_Gateways::get_instance();
}

/**
 * Returns the one instance of EDDCF_Campaign_Types. 
 *
 * @return 	EDDCF_Campaign_Types
 * @since 	1.0.0
 */
function eddcf_campaign_types() {
	return EDDCF_Campaign_Types::get_instance();
}