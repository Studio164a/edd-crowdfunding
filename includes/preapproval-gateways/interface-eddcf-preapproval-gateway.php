<?php
/**
 * Payment gateway interface.
 *
 * This defines a strict interface that donation forms must implement.
 *
 * @interface 	EDDCF_Preapproval_Gateway
 * @version		1.0.0
 * @package		EDD Crowdfunding/Interfaces/EDDCF_Preapproval_Gateway
 * @category	Interfaces
 * @author 		Studio164a
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! interface_exists( 'EDDCF_Preapproval_Gateway' ) ) : 

/**
 * EDDCF_Preapproval_Gateway
 * 
 * @since 		1.0.0
 */
interface EDDCF_Preapproval_Gateway {

	/**
	 * Process a payment for the campaign.
	 *
	 * @param 	int $payment_id
	 * @param 	EDDCF_Campaign $campaign
	 * @return 	mixed
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function process_payment( $payment_id, EDDCF_Campaign $campaign );

	/**
	 * Whether preapproval support is enabled.
	 * 
	 * This method is called on the eddcf_has_preapproval_support hook. It 
	 * allows the helper class to declare if preapproval support is activated. 
	 *
	 * @see 	EDDCF_Gateways::load_gateway_support()
	 *
	 * @param 	boolean 	$has_support
	 * @return 	boolean
	 * @access  public
	 * @static
	 * @since 	1.0.0
	 */
	public static function has_preapproval_support( $has_support );
}

endif; // End interface_exists check.