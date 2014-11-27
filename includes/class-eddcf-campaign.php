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
	 * @access  private
	 */
	private $campaign_data;

	/**
	 * The type of campaign. 
	 * @var 	string
	 * @access 	private
	 */
	private $campaign_type;
	
	/**
	 * The pledges made to this campaign.
	 * @var 	array
	 * @access 	private
	 */
	private $pledges;

	/**
	 * The unique pledges made to this campaign.
	 * @var 	array
	 * @access 	private
	 */
	private $unique_pledges;

	/**
	 * The backers for this campaign.
	 * @var 	array
	 * @access 	private
	 */
	private $backers;

	/**
	 * Create class object.
	 * 
	 * @param 	int|WP_Post $post 	The post ID or full WP_Post object for the post associated with this campaign.
	 * @return 	void
	 * @access 	public
	 * @since	1.0.0
	 */
	public function __construct( $post ) {
		$this->campaign_data = get_post( $post );
		$this->ID = $this->campaign_data->ID;
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
		$meta = apply_filters( 'eddcf_campaign_meta_' . $key, $this->campaign_data->__get( $key ) );
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

	/**
	 * Returns the type of campaign this is. 
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function type() {
		if ( ! isset( $this->campaign_type ) ) {
			$this->campaign_type = $this->__get( 'campaign_type' );

			if ( ! $this->campaign_type ) {
				$this->campaign_type = eddcf_campaign_types()->default_type();
			}
		}
		
		return $this->campaign_type;
	}

	/**
	 * The location of the campaign owners.
	 *
	 * @return 	string
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function location() {
		return $this->__get( 'campaign_location' );
	}

	/**
	 * The creator of the campaign. 
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function author() {
		return $this->__get( 'campaign_author' );
	}

	/**
	 * The contact email provided for this campaign. 
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function contact_email() {
		return antispambot( $this->__get( 'campaign_contact_email' ) );
	}

	/**
	 * The date this campaign ends. 
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function end_date() {
		return $this->__get( 'campaign_end_date' );
	}
	
	/**
	 * Whether this is an endless campaign.
	 *
	 * @return 	boolean
	 * @access  public
	 * @since 	1.0.0
	 */
	public function is_endless() {
		return $this->__get( 'campaign_endless' );
	}
	
	/**
	 * Whether this campaign is only for donations (no rewards are provided). 
	 *
	 * @return 	boolean
	 * @access  public
	 * @since 	1.0.0
	 */
	public function is_donations_only() {
		return $this->__get( 'campaign_norewards' );
	}

	/**
	 * Whether this campaign offers multiple rewards options.
	 *
	 * @return 	boolean
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function has_reward_options() {
		return $this->__get( '_variable_pricing' ) || count( edd_get_variable_prices( $this->ID ) ); 
	}
	
	/**
	 * The link to the video for this campaign.
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function video() {
		return $this->__get( 'campaign_video' );
	}

	/**
	 * Returns all the pledges or donations made to this campaign.
	 *
	 * @global 	EDD_Logging $edd_logs
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function pledges() {	
		if ( ! isset( $this->pledges ) ) {
			global $edd_logs; 

			$pledges_args = apply_filters( 'eddcf_campaign_pledges_args', array(
				'post_parent'    => $this->ID,
				'log_type'       => eddcf_gateways()->has_preapproval_gateway() ? 'preapproval' : 'sale',
				'post_status'    => array( 'publish' ),
				'posts_per_page' => -1
			) );

			// EDD_Logging fetches the logs (i.e. pledges) connected to this campaign.
			$this->pledges = $edd_logs->get_connected_logs( $pledges_args );

			if ( ! $this->pledges ) {
				$this->pledges = false;
			}
		}	

		return $this->pledges;
	}

	/**
	 * Returns all the unique transactions with pledges for this campaign. 
	 * 
	 * If a single transaction included multiple pledges to the same campaign, 
	 * the transaction is only counted once.
	 *
	 * @return 	array
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function unique_pledges() {
		if ( ! isset( $this->unique_pledges ) ) {
			$this->unique_pledges = array();
			$pledges = $this->pledges();

			if ( is_array( $pledges ) && count( $pledges ) ) {
				foreach ( $pledges as $pledge ) {
					$payment_id = get_post_meta( $pledge->ID, '_edd_log_payment_id', true );

					if ( in_array( $payment_id, $this->unique_pledges ) ) {
						continue;
					}
					else {
						$this->unique_pledges[] = $payment_id;
					}
				}	
			}			
		}

		return $this->unique_pledges;
	}

	/**
	 * Returns the number of unique campaign pledges. 
	 *
	 * If someone pledges to a campaign with multiple separate reward choices 
	 * (i.e. multiple variations of the same campaign), each variation is counted. 
	 * If you want the exact number of transactions that included a pledge to 
	 * this campaign, use unique_pledges() instead.
	 * 
	 * @return 	int
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function pledge_count() {
		$prices = edd_get_variable_prices( $this->ID );
		$count  = 0;

		if ( empty( $prices ) ) {
			return $count;
		}

		foreach ( $prices as $price ) {
			$count += isset( $price[ 'bought' ] ) ? $price[ 'bought' ] : 0;
		}

		return $count;
	}

	/**
	 * Returns an array containing the user ID and email address of all unique 
	 * campaign backers. 
	 *
	 * A single user may have backed the campaign multiple times, so this 
	 * returns only the unique backers. A single user is not counted twice.
	 *
	 * Note that users may have made the transaction without being logged in, 
	 * in which case the user ID is 0. 
	 * 
	 * @global 	WPDB 	$wpdb
	 * @return 	int
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function backers() {
		global $wpdb; 

		if ( ! isset( $this->backers ) ) {

			$unique_pledges = $this->unique_pledges();

			if ( 0 == count( $unique_pledges ) ) {
				$this->backers = array();
			}
			else {
				// Escape data for SQL. 
				$unique_pledges = esc_sql( $unique_pledges );

				$pledge_ids = implode( ', ', $unique_pledges );

				$sql   	 = "SELECT DISTINCT m.meta_value, p.post_author
							FROM $wpdb->postmeta m
							LEFT JOIN $wpdb->posts p
							ON m.post_id = p.ID
							WHERE m.meta_key = '_edd_payment_user_email'
							AND m.post_id IN ( $pledge_ids )";

				$this->backers = $wpdb->get_results( $sql );
			}
		}

		return $this->backers;
	}

	/**
	 * Returns the number of unique campaign backers. 
	 * 
	 * @uses 	$this->backers()
	 * @return 	int
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function backers_count() {
		return count( $this->backers() );
	}

	/**
	 * Returns the amount of time remaining in the campaign.
	 *
	 * @param 	string $format 		Either 'seconds', 'hours', 'minutes' or 'days'.
	 * @return 	int 
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_time_remaining( $format = 'seconds' ) {
		$now     = current_time( 'timestamp' );
		$expires = strtotime( $this->end_date(), $now );

		if ( $now > $expires ) {
			return 0;
		}

		$seconds = $expires - $now;

		switch ( $format ) {			
			case 'days' :
				$day_in_seconds = 60 * 60 * 24;
				$remaining = $seconds / $day_in_seconds;
				break;

			case 'hours' : 
				$hour_in_seconds = 60 * 60;
				$remaining = $seconds / $hour_in_seconds;
				break;

			case 'minutes' : 
				$minutes_in_seconds = 60;
				$remaining = $seconds / $minutes_in_seconds;
				break;

			default: 
				$remaining = $seconds;
		}

		$remaining = floor( $remaining );

		return apply_filters( 'eddcf_campaign_time_remaining', $remaining, $format, $this );
	}

	/**
	 * Returns the amount of time left in the campaign as a formatted string. 
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function time_remaining() {
		$unit = false;
		$count = 0;

		// Endless campaign.
		if ( $this->is_endless() ) {
			$time_left = apply_filters( 'eddcf_endless_campaign_time_left', __( 'Campaign has no end', 'eddcf' ) );
		}

		// Campaign is no longer active.
		if ( ! $this->is_active() ) {
			$time_left = apply_filters( 'eddcf_inactive_campaign_time_left', __( 'Campaign has finished', 'eddcf' ) );
		}

		// Try to display as days left.
		$days = $this->get_time_remaining( 'days' );
		if ( 0 < $days ) {
			$unit = 'days';
			$count = $days;
			$time_left = sprintf( __( '%s days left', 'eddcf' ), '<span class="time-left">' . $days . '</span>' );
		}
		else {
			// Less than a day left, so show hours left.
			$hours = $this->get_time_remaining( 'hours' );

			if ( 0 < $hours ) {
				$unit = 'hours';
				$count = $hours;
				$time_left = sprintf( __( '%s hours left', 'eddcf' ), '<span class="time-left">' . $hours . '</span>' );
			}
			else {
				// Less than an hour left, so show minutes left.
				$unit = 'minutes';
				$count = $this->get_time_remaining( 'minutes' );
				$time_left = sprintf( __( '%s minutes left', 'eddcf' ), '<span class="time-left">' . $count . '</span>' );
			}
		}		
		
		return apply_filters( 'eddcf_campaign_time_left', $time_left, $count, $unit, $this );
	}

	/**
	 * Return the percentage of the goal that has been completed (i.e. funded). 
	 *
	 * @param 	boolean $formatted 		Whether to return formatted as a percentage, or as an integer.
	 * @return 	int|string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function percent_completed( $formatted = true ) {
		$goal = $this->goal( false );
		$current = $this->current_amount( false );

		if ( 0 == $goal ) {
			return $formatted ? 0 . '%' : 0;
		}

		$percent = ( $current / $goal ) * 100;
		$percent = round( $percent );

		if ( $formatted ) {
			return $percent . '%';
		}

		return $percent;
	}

	/**
	 * Current amount funded.
	 *
	 * @global 	$wpdb
	 * @param 	boolean $formatted 		Whether to return formatted as a currency or not. 
	 * @return  string|int
	 * @since 	1.0.0
	 */
	public function current_amount( $formatted = true ) {

		// Don't do this more than once
		if ( ! isset( $this->current_amount ) ) {

			global $wpdb;

			// Allow plugins/themes to filter which IDs are matched in post_parent.
			$campaign_ids = apply_filters( 'atcf_campaign_pledged_amount_id', array( $this->ID ), $this );
			$campaign_ids = array_filter( $campaign_ids, 'is_int' );
			$campaign_ids = implode( ',', $campaign_ids );

			// Fetches the SUM of all payments made to this campaign.
			$query = apply_filters( 'atcf_campaign_pledged_query', 
				"SELECT SUM(m.meta_value) 
				FROM ( 
					SELECT DISTINCT m1.post_id, m1.meta_value 
					FROM $wpdb->postmeta m1 
					INNER JOIN $wpdb->posts p1
					ON p1.ID = m1.post_id
					INNER JOIN $wpdb->postmeta m2 
					ON m2.meta_value = m1.post_id 
					INNER JOIN $wpdb->posts p2 
					ON p2.ID = m2.post_id
					WHERE p1.post_status IN ('publish', 'preapproval')
					AND p2.post_parent IN ( $campaign_ids )
					AND m1.meta_key = '_edd_payment_total' 
				) m", 
			$campaign_ids, $this );

			$this->current_amount = $wpdb->get_var( $query );
		}
	
		if ( $formatted ) {
			return edd_currency_filter( edd_format_amount( $this->current_amount ) );
		}

		return (float) $this->current_amount;
	}

	/**
	 * Returns payments that have failed. 
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function failed_payments() {
		return $this->__get( '_campaign_failed_payments' );
	}

	/**
	 * Returns whether the campaign is still active. 
	 *
	 * A campaign is active if it has not expired and funds have not been collected. 
	 *
	 * @return 	boolean
	 * @access  public
	 * @since 	1.0.0
	 */		
	public function is_active() {
		$active  = true;

		// If any of the following conditions is true, the campaign is no longer active.
		if ( 0 === $this->get_time_remaining() ) {
			$active = false;
		}
		elseif ( $this->__get( '_campaign_expired' ) ) {
			$active = false;
		}
		elseif ( $this->is_collected() ) {
			$active = false;
		}
		
		// If the campaign is endless, always return true.
		if ( $this->is_endless() ) {
			$active = true;
		}

		return apply_filters( 'eddcf_campaign_active', $active, $this );
	}

	/**
	 * Returns whether the pledges have been collected for this campaign. 
	 *
	 * @return 	boolean
	 * @access  public
	 * @since 	1.0.0
	 */
	public function is_collected() {
		return $this->__get( '_campaign_bulk_collected' );
	}

	/**
	 * Returns whether the campaign is fully funded. 
	 *
	 * @return 	boolean
	 * @access  public
	 * @since 	1.0.0
	 */
	public function is_funded() {
		if ( $this->current_amount( false ) >= $this->goal( false ) ) {
			return true;
		}

		return false;
	}
}

endif; // End class_exists check