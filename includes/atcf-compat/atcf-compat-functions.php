<?php

/**
 * Functions that add partial compatibility for Crowdfunding by Astoundify
 *
 * @version		1.0.0
 * @package		EDD Crowdfunding/Functions/ATCF Compatibility
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Functions
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdfunding() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'eddcf()' );
	return eddcf();
}

function atcf_theme_supports( $feature ) {
	_deprecated_function( __FUNCTION__, '1.0.0', 'eddcf_theme_supports()' );
	return eddcf_theme_supports( $feature );
}

function atcf_get_campaign( $campaign_id ) {
	_deprecated_function( __FUNCTION__, '1.0.0', 'eddcf_get_campaign()' );
	return eddcf_get_campaign( $campaign_id );
}

function atcf_purchase_variable_pricing() {
	/**
	 * @todo
	 */
}