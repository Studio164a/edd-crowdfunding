<?php
/**
 * The class that manages the functionality of the various campaign types.
 *
 * @class 		EDDCF_Campaign_Types
 * @version		1.0.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Campaign_Types
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Campaign_Types' ) ) : 

/**
 * EDDCF_Campaign_Types
 *
 * @since 		1.0.0
 */
class EDDCF_Campaign_Types {

	/**
	 * Holds the class instance. 
	 * @var 	EDDCF_Campaign_Types
	 * @access  private
	 * @static
	 */
	private static $instance;

	/**
	 * Private constructor. The class instance is created with get_instance(). 
	 *
	 * @return 	void
	 * @access  private
	 * @since 	1.0.0
	 */
	private function __construct() {}

	/**
	 * Return the single class instance. If no instance exists yet, create one. 
	 *
	 * @return 	EDDCF_Campaign_Types
	 * @access  public
	 * @static
	 * @since 	1.0.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new EDDCF_Campaign_Types();
		}

		return self::$instance;
	}

	/**
	 * Return all the campaign types.  
	 *
	 * @return 	array 
	 * @access  public
	 * @since 	1.0.0
	 */
	public function types() {
		return apply_filters( 'eddcf_campaign_types', array(
			'fixed' 	=> array(
				'title'       	=> __( 'All-or-nothing', 'eddcf' ),
				'description' 	=> __( 'Only collect pledged funds when the campaign ends if the set goal is met.', 'eddcf' )
			),
			'flexible' 	=> array(
				'title'       	=> __( 'Flexible', 'eddcf' ),
				'description' 	=> __( 'Collect funds pledged at the end of the campaign no matter what.', 'eddcf' )
			), 
			'donation'	=> array(
				'title'			=> __( 'Donations', 'eddcf' ), 
				'description'	=> __( 'Funds are collected automatically when the pledge is made.', 'eddcf' )
			)
		) );
	}

	/**
	 * Returns the active campaign types. This depends on the gateway enabled. 	
	 *
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function active_types() {
		$types = $this->types();
		$active_types = array();

		if ( eddcf_gateways()->has_preapproval_gateway() ) {
			$active_types['fixed'] = $types['fixed'];
			$active_types['flexible'] = $types['flexible'];
		}
		else {
			$active_types['donation'] = $types['donation'];
		}

		return apply_filters( 'eddcf_active_campaign_types', $active_types );
	}

	/**
	 * Default campaign type. 
	 *
	 * @return 	string
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function default_type() {
		$type = apply_filters( 'eddcf_campaign_type_default', eddcf_gateways()->has_preapproval_gateway() ? 'fixed' : 'donation' );
		return $type;
	}
}

endif; // End class_exists check