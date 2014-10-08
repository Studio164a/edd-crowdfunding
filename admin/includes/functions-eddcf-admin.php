<?php

/**
 * EDD Crowdfunding functions in the Admin area.
 *
 * @version		1.0.0
 * @package		EDD Crowdfunding/Functions/Admin
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Functions
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display an admin view. 
 *
 * @param 	string 	$view 	The view to display.
 * @return 	void
 * @since 	1.0.0
 */
function eddcf_admin_view( $view ) {
	$filename = eddcf()->admin_dir . 'views/' . $view . '.php';

	if ( ! is_readable( $filename ) ) {
		echo '<pre>';
		throw new Exception( sprintf( '<strong>%s</strong>: %s (%s)', 
			__( 'Error', 'eddcf' ), 
			__( 'View not found or is not readable.', 'eddcf' ), 
			$filename
		) );
		echo '</pre>';
	}

	include_once( $filename );
}