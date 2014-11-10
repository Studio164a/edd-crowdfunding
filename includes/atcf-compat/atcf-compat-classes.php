<?php

/**
 * Stubs of classes to map Crowdfunding by Astoundify's classes to EDD Crowdfunding ones.
 *
 * @version		1.0.0
 * @package		EDD Crowdfunding/Classes/ATCF Compatibility
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Functions
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ATCF_Crowdfunding
 *
 * @deprecated
 */
class ATCF_CrowdFunding extends EDD_Crowdfunding {
	private function __construct() {
		_deprecated_function( 'ATCF_Crowdfunding class', '1.0.0', 'EDD_Crowdfunding' );
		parent::__construct();
	}
}

/**
 * ATCF_Campaign_Query
 *
 * @deprecated
 */
class ATCF_Campaign_Query extends WP_Query {
	public function __construct( $args = array() ) {
		$defaults = array(
			'post_type'      => array( 'download' ),
			'posts_per_page' => get_option( 'posts_per_page' )
		);

		$args = wp_parse_args( $args, $defaults );

		parent::__construct( $args );
	}
}

/**
 * ATCF_Campaign
 *
 * @deprecated
 */
class ATCF_Campaign extends EDDCF_Campaign {
	
}