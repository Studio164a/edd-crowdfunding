<?php
/**
 * The class that provides the Paypal Adaptive Payments integration.
 *
 * @class 		EDDCF_Gateway_Paypal_Adaptive_Payments
 * @version		1.0.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Gateway_Paypal_Adaptive_Payments
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Gateway_Paypal_Adaptive_Payments' ) ) : 

/**
 * EDDCF_Gateway_Paypal_Adaptive_Payments
 *
 * @since 		1.0.0
 */
class EDDCF_Gateway_Paypal_Adaptive_Payments implements EDDCF_Preapproval_Gateway {
	
	/**
	 * Process a payment for the campaign.
	 *
	 * @param 	int $payment_id
	 * @param 	EDDCF_Campaign $campaign
	 * @return 	mixed
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function process_payment( $payment_id, EDDCF_Campaign $campaign ) {
		
	}
}

endif; // End class_exists check