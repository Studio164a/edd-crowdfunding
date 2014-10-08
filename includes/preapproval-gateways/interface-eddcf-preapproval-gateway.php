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
}

endif; // End interface_exists check.