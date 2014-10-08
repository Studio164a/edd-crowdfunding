<?php
/**
 * The class that manages the processing of campaign payments.
 *
 * @class 		EDDCF_Campaign_Processing
 * @version		1.0.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Campaign_Processing
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Campaign_Processing' ) ) : 

/**
 * EDDCF_Campaign_Processing
 *
 * @since 		1.0.0
 */
class EDDCF_Campaign_Processing {

	/**
	 * Create object instance. 
	 * 
	 * @param 	EDDCF_Campaign $campaign
	 * @return 	void
	 * @access 	public
	 * @since	1.0.0
	 */
	public function __construct( EDDCF_Campaign $campaign ) {
		
	}
}

endif; // End class_exists check
