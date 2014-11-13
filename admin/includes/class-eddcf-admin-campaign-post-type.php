<?php
/**
 * The class that changes how the campaign post type is handled in the admin area. 
 *
 * @class 		EDDCF_Admin_Campaign_Post_Type
 * @version		1.0.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Admin_Campaign_Post_Type
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Admin_Campaign_Post_Type' ) ) : 

/**
 * EDDCF_Admin_Campaign_Post_Type
 *
 * @since 		1.0.0
 */
class EDDCF_Admin_Campaign_Post_Type {

	/**
	 * The main EDD_Crowdfunding object. 
	 * @var 	EDD_Crowdfunding
	 * @access  private
	 */
	private $eddcf;

	/**
	 * The metabox helper object. 
	 * @var 	EDDCF_Metabox_Helper
	 * @access 	private
	 */
	private $metabox_helper;

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
		$this->edd_crowdfunding = $eddcf;

		$this->metabox_helper = new EDDCF_Metabox_Helper( 'eddcf-campaign' );

		add_action( 'init', array( $this, 'setup' ), -1 );
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

		new EDDCF_Admin_Campaign_Post_Type( $eddcf );
	}

	/**
	 * Setup modifications with hooks & filters on EDD's download post type. 
	 *
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function setup() {		
		// Add fields to the dashboard listing of campaigns.
		add_filter( 'manage_edit-download_columns', array( $this, 'dashboard_columns' ), 11, 1 );
		add_filter( 'manage_download_posts_custom_column', array( $this, 'dashboard_column_item' ), 11, 2 );

		// Add and remove metaboxes from the campaign editing page.
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 11 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 2 );
		add_filter( 'edd_metabox_fields_save', array( $this, 'meta_boxes_save' ) );
		add_filter( 'edd_metabox_save_campaign_end_date', array( $this, 'save_end_date' ) );
		add_filter( 'edd_metabox_save_campaign_goal', array( $this, 'save_campaign_goal' ) );
		remove_filter( 'edd_metabox_save_edd_variable_prices', 'edd_sanitize_variable_prices_save' );
		add_filter( 'edd_metabox_save_edd_variable_prices', array( $this, 'save_rewards' ) );
		remove_action( 'edd_meta_box_fields', 'edd_render_product_type_field', 10 );		

		// Change the price options metabox, adding a norewards field and 
		add_filter( 'edd_price_options_heading', array( $this, 'price_options_heading' ) );
		add_filter( 'edd_variable_pricing_toggle_text', array( $this, 'variable_pricing_toggle_text' ) );
		add_action( 'edd_after_price_field', array( $this, 'norewards_field' ) );
		add_action( 'edd_download_price_table_head', array( $this, 'reward_limit_head' ), 9 );
		add_action( 'edd_download_price_table_row', array( $this, 'reward_limit_column' ), 9, 3 );
		add_filter( 'edd_price_row_args', array( $this, 'reward_row_args' ), 10, 2 );

		// add_action( 'wp_insert_post', array( $this, 'update_post_date_on_publish' ) );
	}

	/**
	 * Add "Amount Funded" and "Expires" to the main campaign table listing.
	 *
	 * @see 	get_column_headers
	 *
	 * @param 	array 	$supports
	 * @return 	array
	 * @access 	public
	 * @since  	1.0.0
	 */
	public function dashboard_columns( $column_names ) {
		$columns = apply_filters( 'eddcf_dashboard_columns', array(
			'cb'                => '<input type="checkbox"/>',
			'title'             => __( 'Name', 'eddcf' ),
			'type'              => __( 'Type', 'eddcf' ),
			'backers'           => __( 'Backers', 'eddcf' ),
			'funded'            => __( 'Amount Funded', 'eddcf' ),
			'expires'           => __( 'Days Remaining', 'eddcf' )
		) );

		return $columns;
	}

	/**
	 * Add extra information for campaigns to the dashboard campaign table listing.
	 *
	 * @see 	WP_Posts_List_Table::single_row()
	 * 
	 * @param 	string 	$column_name 	The name of the column to display.
	 * @param 	int 	$post_id     	The current post ID.
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function dashboard_column_item( $column_name, $post_id ) {
		$campaign = eddcf_get_campaign( $post_id );

		switch ( $column_name ) {
			case 'funded' :
				printf( _x( '%s of %s', 'funded of goal', 'eddcf' ), $campaign->current_amount( true ), $campaign->goal( true ) );
				break;

			case 'expires' :
				echo $campaign->is_endless() ? '&mdash;' : $campaign->time_remaining( 'days' );
				break;

			case 'type' :
				echo ucfirst( $campaign->type() );
				break;

			case 'backers' :
				echo $campaign->backers_count();
				break;

			default :
				break;
		}
	}

	/**
	 * Remove some of the default EDD metaboxes which are not relevant 
	 * to crowdfunding, or which we're going to replace with our own version.
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0	
	 */
	public function remove_meta_boxes() {
		$boxes = array(
			'edd_file_download_log' => 'normal',
			'edd_purchase_log'      => 'normal',
			'edd_product_stats'     => 'side'
		);

		foreach ( $boxes as $box => $context ) {
			remove_meta_box( $box, 'download', $context );
		}
	}

	/**
	 * Add custom metaboxes to the campaign post type.
	 *
	 * - Collect Funds
	 * - Campaign Stats
	 * - Campaign Video
	 *
	 * As well as some other information plugged into EDD in the Download Configuration
	 * metabox that already exists.
 	 * 
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0	 
	 */
	public function add_meta_boxes() {
		add_meta_box( 'eddcf_campaign_details', __( 'Campaign Details', 'eddcf' ), array( $this->metabox_helper, 'metabox_display' ), 'download', 'normal', 'high', array( 'view' => 'metaboxes/campaign-details' ) );
		add_meta_box( 'eddcf_campaign_stats', __( 'Campaign Stats', 'eddcf' ), array( $this->metabox_helper, 'metabox_display' ), 'download', 'side', 'high', array( 'view' => 'metaboxes/campaign-stats' ) );
		
		if ( eddcf_theme_supports( 'campaign-video' ) ) {
			add_meta_box( 'eddcf_campaign_video', __( 'Campaign Video', 'eddcf' ), array( $this->metabox_helper, 'metabox_display' ), 'download', 'normal', 'high', array( 'view' => 'metaboxes/campaign-video' ) );
		}
	}	

	/**
	 * Save campaign information. 
	 *
	 * This hooks into EDD's post saving script. 
	 *
	 * @param 	array 	$fields 	An array of fields to save.
	 * @return 	array 	$fields
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function meta_boxes_save( $fields ) {
		$fields[] = 'campaign_goal';
		$fields[] = 'campaign_contact_email';
		$fields[] = 'campaign_end_date';
		$fields[] = 'campaign_endless';
		$fields[] = 'campaign_norewards';
		$fields[] = 'campaign_video';
		$fields[] = 'campaign_location';
		$fields[] = 'campaign_author';
		$fields[] = 'campaign_type';
		return $fields;
	}

	/**
	 * Validate and save the expiry date of the campaign. 
	 *
	 * @global 	WP_POST 	$post
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function save_end_date() {
		global $post; 

		if ( ! isset( $_POST[ 'campaign_end_aa' ] ) ) {
			if ( 0 == $_POST[ 'campaign_endless' ] ) {
				delete_post_meta( $post->ID, 'campaign_endless' );
			}

			return;
		}

		$aa = $_POST['campaign_end_aa'];
		$mm = $_POST['campaign_end_mm'];
		$jj = $_POST['campaign_end_jj'];
		$hh = $_POST['campaign_end_hh'];
		$mn = $_POST['campaign_end_mn'];
		$ss = $_POST['campaign_end_ss'];

		$aa = ($aa <= 0 ) ? date('Y') : $aa;
		$mm = ($mm <= 0 ) ? date('n') : $mm;
		$jj = ($jj > 31 ) ? 31 : $jj;
		$jj = ($jj <= 0 ) ? date('j') : $jj;

		$hh = ($hh > 23 ) ? $hh -24 : $hh;
		$mn = ($mn > 59 ) ? $mn -60 : $mn;
		$ss = ($ss > 59 ) ? $ss -60 : $ss;

		$end_date = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $aa, $mm, $jj, $hh, $mn, $ss );

		$valid_date = wp_checkdate( $mm, $jj, $aa, $end_date );

		if ( ! $valid_date ) {
			return new WP_Error( 'invalid_date', __( 'Whoops, the provided date is invalid.', 'atcf' ) );
		}

		if ( mysql2date( 'G', $end_date ) > current_time( 'timestamp' ) ) {
			delete_post_meta( $post->ID, '_campaign_expired' );
		}

		return $end_date;
	}

	/**
	 * Sanitize the goal amount. Strips out thousands separators. 
	 *
	 * @return 	number
	 * @access  public
	 * @since 	1.0.0
	 */
	public function save_campaign_goal( $amount ) {
		return edd_sanitize_amount( $amount );
	}

	/**
	 * Save campaign rewards/pledge options. 
	 *
	 * @param 	array 	$prices
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function save_rewards( $prices ) {
		$norewards = isset ( $_POST[ 'campaign_norewards' ] ) ? true : false;

		if ( $norewards ) {
			// $prices = array();
			// $prices[0] = array(
			// );

			// return $prices;
		}

		return $prices;
	}

	/**
	 * Updates Save
	 *
	 * EDD trys to escape this data, and we don't want that.
	 *
	 * @since Astoundify Crowdfunding 0.9
	 */
	function atcf_save_variable_prices_norewards( $prices ) {
		$norewards = isset ( $_POST[ 'campaign_norewards' ] ) ? true : false;

		if ( ! $norewards )
			return $prices;

		if ( isset( $prices[0][ 'name' ] ) )
			return $prices;

		$prices = array();

		$prices[0] = array(
			'name'   => apply_filters( 'atcf_default_no_rewards_name', __( 'Donation', 'atcf' ) ),
			'amount' => apply_filters( 'atcf_default_no_rewards_price', 0 ),
			'limit'  => null,
			'bought' => 0
		);

		return $prices;
	}

	/**
	 * Change the heading of the price options metabox. 
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function price_options_heading() {
		return __( 'Reward Options:', 'eddcf' );
	}

	/**
	 * Change the description of the variable pricing field.
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function variable_pricing_toggle_text() {
		return __( 'Enable multiple reward options', 'eddcf' );
	}

	/**
	 * Insert no-rewards checkbox at the end of the price metabox. 
	 *
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function norewards_field() {
		eddcf_admin_view('metaboxes/campaign-rewards/norewards');
	}

	/**
	 * Adds columns to the head of the reward options table.
	 *
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function reward_limit_head() {
		?>
		<th id="campaign_reward_limit"><?php _e( 'Limit', 'eddcf' ); ?></th>
		<th id="campaign_reward_backers"><?php _e( 'Backers', 'eddcf' ); ?></th>
		<?php
	}

	/**
	 * Adds columns to the body of the reward options table.
	 *
	 * @param 	int 		$post_id
	 * @param 	string 		$key
	 * @param 	array 		$args 
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function reward_limit_column( $post_id, $key, $args ) {
		?>
		<td>
			<input type="text" class="edd_repeatable_name_field" name="edd_variable_prices[<?php echo $key; ?>][limit]" id="edd_variable_prices[<?php echo $key; ?>][limit]" value="<?php echo isset ( $args[ 'limit' ] ) ? $args[ 'limit' ] : null; ?>" style="width:100%" placeholder="0" />
		</td>
		<td>
			<input type="text" class="edd_repeatable_name_field" name="edd_variable_prices[<?php echo $key; ?>][bought]" id="edd_variable_prices[<?php echo $key; ?>][bought]" value="<?php echo isset ( $args[ 'bought' ] ) ? $args[ 'bought' ] : null; ?>" style="width:100%" placeholder="0" />
		</td>
		<?php
	}

	/**
	 * Add additional arguments to reward row.  
	 *
	 * @param 	array 		$args
	 * @param 	array 		$value
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function reward_row_args( $args, $value ) {
		if ( isset( $value['limit'] ) ) {
			$args['limit'] = $value['limit'];
		}

		if ( isset( $value['bought'] ) ) {
			$args['bought'] = $value['bought'];
		}

		return $args;
	}
}

endif; // End class_exists check