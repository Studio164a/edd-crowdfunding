<?php
/**
 * EDDCF_Campaign represents the model for campaigns.
 *
 * @class 		EDDCF_Campaign
 * @version		1.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Campaign
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Campaign' ) ) : 

/**
 * EDDCF_Campaign
 *
 * @since 		1.0.0
 */
class EDDCF_Campaign {

	/**
	 * Campaign ID. 
	 * @var 	int
	 * @access 	public
	 */
	public $ID;

	/**
	 * Campaign raw data object. 
	 * @var 	WP_Post
	 * @access  public
	 */
	public $data;
	
	/**
	 * Create class object.
	 * 
	 * @return 	void
	 * @access 	public
	 * @since	1.0.0
	 */
	public function __construct() {
		$this->data = get_post( $post );
		$this->ID   = $this->data->ID;
	}

	/**
	 * Retrieve campaign meta data by key. 
	 *
	 * @see 	WP_Post::__get()
	 * @uses 	eddcf_campaign_meta_$key 
	 * 
	 * @param 	string $key
	 * @return 	mixed
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function __get( $key ) {
		$meta = apply_filters( 'eddcf_campaign_meta_' . $key, $this->data->__get( $key ) );
		return $meta;
	}

	/**
	 * Get the campaign's crowdfunding goal.  
	 *
	 * @param  	bool $formatted 	Default is true. Whether to return the goal as a monetary amount.
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function goal( $formatted = true ) {
		$goal = $this->__get( 'campaign_goal' );

		if ( ! $goal ) {
			return 0;
		}

		if ( $formatted ) {
			return edd_currency_filter( edd_format_amount( $goal ) );
		}

		return $goal;
	}
}

endif; // End class_exists check