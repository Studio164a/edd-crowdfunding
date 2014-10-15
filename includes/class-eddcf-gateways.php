<?php
/**
 * The class responsible for providing awareness of the enabled payment gateways
 * and the presence of any preapproval gateways. 
 *
 * @class 		EDDCF_Gateways
 * @version		1.0.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Gateways
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Gateways' ) ) : 

/**
 * EDDCF_Gateways
 *
 * @since 		1.0.0
 */
class EDDCF_Gateways {

	/**
	 * The main EDD_Crowdfunding object. 
	 * @var 	EDD_Crowdfunding
	 * @access  private
	 */
	private $eddcf;

	/**
	 * The static, publicly accessible instance of this class.
	 * @var 	EDD_Gateways
	 * @access  private
	 * @static
	 */
	private static $instance;	

	/**
	 * Whether a payment gateway with support preapproval payments is active.	
	 * @var 	boolean
	 * @access  private
	 */
	private $has_preapproval_gateway = false;

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

		add_action('init', array( $this, 'load_gateway_support' ), 1);
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

		self::$instance = new EDDCF_Gateways( $eddcf );
	}

	/**
	 * Return the instance of EDDCF_Gateways.
	 *
	 * Note that this is NOT a classic singleton pattern, in that this method does not *create* 
	 * an instance. The instance can only be created with the start() method above, which in 
	 * turn can only be executed on the eddcf_start hook.
	 *
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {			
			trigger_error( sprintf( _x( '%s incorrectly called before %s hook.', 'function called incorrectly', 'eddcf'), 'EDDCF_Gateways::get_instance', 'eddcf_start' ), E_USER_WARNING );
		}

		return self::$instance;
	}	

	/**
	 * Set up gateway support for gateways with preapproval support. 
	 *
	 * If one of the active gateways has preapproval support, we 
	 * load up the relevant helper class. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function load_gateway_support() {
		$preapproval_gateways = $this->preapproval_gateways();
		$active_gateways = edd_get_enabled_payment_gateways();

		foreach ( $active_gateways as $gateway => $gateway_args ) {

			if ( array_key_exists( $gateway, $preapproval_gateways ) ) {

				// Load the interface (only once) and gateway file
				require_once( $this->eddcf->includes_dir . 'preapproval-gateways/interface-eddcf-preapproval-gateway.php' );
				require_once( $preapproval_gateways[$gateway]['file'] );

				// Set up a check for preapproval support
				add_filter( 'eddcf_has_preapproval_support', array( $preapproval_gateways[$gateway]['class'], 'has_preapproval_support' ) );
			}
		}	

		$this->has_preapproval_gateway = apply_filters( 'eddcf_has_preapproval_support', false );	
	}

	/**
	 * Returns an array of all preapproval gateways.
	 *
	 * This includes gateways that are not active or installed.  
	 *
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function preapproval_gateways() {
		return apply_filters( 'eddcf_preapproval_gateways', array(
			'paypal_adaptive_payments' 	=> array(
				'file'					=> $this->eddcf->includes_dir . 'preapproval-gateways/class-eddcf-gateway-paypal_adaptive_payments.php',
				'class' 				=> 'EDDCF_Gateway_Paypal_Adaptive_Payments'
			), 
			'stripe'					=> array(
				'file'					=> $this->eddcf->includes_dir . 'preapproval-gateways/class-eddcf-gateway-stripe.php', 
				'class'					=> 'EDDCF_Gateway_Stripe'
			), 
			'wepay'						=> array(
				'file'					=> $this->eddcf->includes_dir . 'preapproval-gateways/class-eddcf-gateway-wepay.php', 
				'class'					=> 'EDDCF_Gateway_WePay'
			)
		) );
	}

	/**
	 * Returns whether there is a gateway activated with support for preapproval payments.
	 *	
	 * @return 	boolean
	 * @access  public
	 * @since 	1.0.0
	 */
	public function has_preapproval_gateway() {
		return $this->has_preapproval_gateway;
	}
}

endif; // End class_exists check