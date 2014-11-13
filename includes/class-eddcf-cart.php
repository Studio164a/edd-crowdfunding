<?php
/**
 * The class that handles actions relating to add or removing items from the cart. 
 *
 * @version		1.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Cart
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Cart' ) ) : 

/**
 * EDDCF_Cart
 *
 * @since 		1.0.0
 */
class EDDCF_Cart {

	/**
	 * The main EDD_Crowdfunding object. 
	 * @var 	EDD_Crowdfunding
	 * @access  private
	 */
	private $eddcf;

	/**
	 * Create class object. 
	 *
	 * Since this is a private constructor, there is only one way to create 
	 * a class object, which is through the start() method below.
	 * 
	 * @param 	EDD_Crowdfunding $eddcf
	 * @return 	void
	 * @access 	private
	 * @since	1.0.0
	 */
	private function __construct( EDD_Crowdfunding $eddcf ) {
		$this->eddcf = $eddcf;

		add_filter( 'edd_add_to_cart_item', array( $this, 'add_to_cart' ) );
		add_filter( 'edd_ajax_pre_cart_item_template', array( $this, 'add_to_cart' ) );
		add_filter( 'edd_cart_item_price', array( $this, 'cart_campaign_donation_amount' ), 10, 3 );
	}

	/**
	 * Create the class object during plugin startup.
	 *
	 * @param 	EDD_Crowdfunding $eddcf
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public static function start( EDD_Crowdfunding $eddcf ) {
		if ( false === $eddcf->is_start() ) {
			return;
		}

		new EDDCF_Cart( $eddcf );
	}

	/**
	 * When an item is added to cart, check if a custom price has been set.  
	 *
	 * @param 	array 	$cart_item
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function add_to_cart( $cart_item ) {
		$donation_amount = false;

		if ( isset ( $_POST['post_data'] ) ) {
			$post_data = array();

			parse_str( $_POST['post_data'], $post_data );

			$donation_amount = $post_data['eddcf_donation'];
		} 
		elseif ( isset( $_POST['eddcf_donation'] ) ) {
			$donation_amount = $_POST['eddcf_donation'];
		}

		if ( ! $donation_amount ) {
			return $cart_item;
		}

		$cart_item['options']['eddcf_donation'] = edd_sanitize_amount( $donation_amount );

		return $cart_item;
	}

	/**
	 * Set the price of the campaign donation amount in the cart.  
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function cart_campaign_donation_amount( $price, $item_id, $options = array() ) {
		if ( isset ( $options['eddcf_donation'] ) ) {
			$price = $options['eddcf_donation'];
		}

		return $price;
	}
}

endif; // End class_exists check