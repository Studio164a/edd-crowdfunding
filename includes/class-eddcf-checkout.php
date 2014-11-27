<?php
/**
 * The class that handles actions taken during the checkout process.
 *
 * @version		1.0.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Checkout
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Checkout' ) ) : 

/**
 * EDDCF_Checkout
 *
 * @since 		1.0.0
 */
class EDDCF_Checkout {

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

		add_action( 'edd_update_payment_status', array( $this, 'maybe_update_backer_count' ), 100, 3 );
		add_action( 'edd_purchase_form_after_cc_form', array( $this, 'anonymous_backer_field' ) );
		add_filter( 'edd_payment_meta', array( $this, 'save_anonymous_backer_meta' ) );
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

		new EDDCF_Checkout( $eddcf );
	}

	/**
	 * Updates the backer count if certain conditions are met. 
	 *
	 * @see 	edd_update_payment_status()
	 * @param 	int 	$payment_id
	 * @param 	string 	$new_status	
	 * @param 	string 	$old_status
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function maybe_update_backer_count( $payment_id, $new_status, $old_status ) {	

		// Make sure that payments are only completed once
		if ( 'pending' != $old_status ) {
			return;
		}

		// Make sure the payment completion is only processed when new status is complete
		if ( in_array( $new_status, array( 'refunded', 'failed', 'revoked', 'cancelled',  'abandoned' ) ) ) {
			return;
		}

		if ( edd_is_test_mode() && ! apply_filters( 'edd_log_test_payment_stats', false ) ) {
			return;
		}

		$this->update_backer_count( $payment_id, 'increase' );
	}

	/**
	 * This actually updates the backer count. 
	 *
	 * @param 	int 	$payment_id
	 * @param 	string 	$direction
	 * @return 	void
	 * @access  private
	 * @since 	1.0.0
	 */
	private function update_backer_count( $payment_id, $direction ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		$downloads = maybe_unserialize( $payment_data['downloads'] );

		if ( ! is_array( $downloads ) ) {
			return;
		}

		// For every campaign or variation purchased, update the backer count.
		foreach ( $downloads as $download ) {	
			$variable_pricing = edd_get_variable_prices( $download['id'] );		
			$variation_idx = $download['options']['price_id'];

			if ( ! isset ( $variable_pricing[ $variation_idx ]['bought'] ) ) {
				$variable_pricing[ $variation_idx ]['bought'] = 0;
			}

			// Get the current number of purchases.
			$current = $variable_pricing[ $variation_idx ]['bought'];
			
			foreach ( $variable_pricing as $key => $value ) {				

				if ( $key == $variation_idx ) {
					if ( 'increase' == $direction ) {
						$variable_pricing[ $variation_idx ]['bought'] = $current + 1;
					} else {
						$variable_pricing[ $variation_idx ]['bought'] = $current - 1;
					}
				}
			}

			update_post_meta( $download['id'], 'edd_variable_prices', $variable_pricing );
		}
	}

	/**
	 * Add a checkbox to the checkout page to allow users to select to remain anonymous. 
	 *
	 * @see 	edd_show_purchase_form()
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function anonymous_backer_field() {
		?>
		<p id="edd-anon-wrap">
			<label class="edd-label" for="edd-anon">
				<input type="checkbox" name="edd_anon" id="edd-anon" style="vertical-align: middle;" />
				<?php _e( 'Hide your name on the list of backers?', 'eddcf' ); ?>
			</label>
		</p>
		<?php
	}

	/**
	 * Save submitted value for backer anonymity. 
	 *
	 * @see 	edd_insert_payment()
	 * @param 	array 	$payment_meta
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function save_anonymous_backer_meta( $payment_meta ) {
		$payment_meta['anonymous'] = isset ( $_POST['edd_anon'] ) ? 1 : 0;
		return $payment_meta;	
	}
}

endif; // End class_exists check