<?php

/**
 * EDD Crowdfunding functions used in templates.
 *
 * The functions in this template can be easily overridden. Simply create a function with the 
 * same name in your theme and the one in this file will be ignored.
 *
 * @version		1.0.0
 * @package		EDD Crowdfunding/Functions/Templates
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Functions
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Returns the text used for making a pledge / supporting a campaign. 
 *
 * @global 	array 	$edd_options
 * @return 	string
 * @since 	1.0.0
 */
if ( ! function_exists( 'eddcf_get_pledge_text' ) ) : 

	function eddcf_get_pledge_text() {
		global $edd_options;
		return ! empty( $edd_options['add_to_cart_text'] ) ? $edd_options['add_to_cart_text'] : __( 'Pledge', 'eddcf' );
	}

endif;

/**
 * Returns the text used when displaying a statement like "Pledge $10.00". i.e. Pledge amount
 *
 * @uses 	eddcf_pledge_amount_text
 * @param 	amount
 * @return 	string
 * @since 	Franklin 1.5.12
 */
if ( ! function_exists( 'eddcf_get_pledge_amount_text' ) ) : 

	function eddcf_get_pledge_amount_text( $amount ) {
		$pledge_text = eddcf_get_pledge_text();

		return apply_filters( 'eddcf_pledge_amount_text', 
			sprintf( '%s %s', $pledge_text, '<strong>'.edd_currency_filter( edd_format_amount( $amount ) ) . '</strong>',
			$amount, 
			$pledge_text 
		) );
	} 

endif;