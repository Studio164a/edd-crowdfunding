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
		// add_filter( 'edd_price_options_heading', 'eddcf_edd_price_options_heading' );
		// add_filter( 'edd_variable_pricing_toggle_text', 'eddcf_edd_variable_pricing_toggle_text' );

		add_filter( 'manage_edit-download_columns', array( $this, 'dashboard_columns' ), 11, 1 );
		add_filter( 'manage_download_posts_custom_column', array( $this, 'dashboard_column_item' ), 11, 2 );

		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 11 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 2 );
		// add_action( 'edit_form_after_title', array( $this, 'goal_meta_box' ) );

		add_filter( 'edd_metabox_fields_save', array( $this, 'meta_boxes_save' ) );
		// add_filter( 'edd_metabox_save_campaign_end_date', 'eddcf_campaign_save_end_date' );

		// remove_action( 'edd_meta_box_fields', 'edd_render_product_type_field', 10 );

		// add_action( 'edd_download_price_table_head', 'eddcf_pledge_limit_head', 9 );
		// add_action( 'edd_download_price_table_row', 'eddcf_pledge_limit_column', 9, 3 );

		// add_action( 'edd_after_price_field', 'eddcf_after_price_field' );

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
	public function dashboard_columns( $columns ) {
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

		switch ( $column ) {
			case 'funded' :
				printf( _x( '%s of %s', 'funded of goal', 'eddcf' ), $campaign->current_amount( true ), $campaign->goal( true ) );
				break;

			case 'expires' :
				echo $campaign->is_endless() ? '&mdash;' : $campaign->days_remaining();
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

		// add_action( 'edd_meta_box_fields', '_eddcf_metabox_campaign_info', 5 );
	}

	/**
	 * Display the goal meta box after the campaign title.
	 *
	 * @see 	edit_form_after_title
	 * 	
	 * @param 	WP_Post 	$post
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function goal_meta_box( $post ) {
		// $screen = (object) get_current_screen();

		// if ( 'download' !== $screen->post_type ) {
		// 	return;
		// }

		// $this->metabox_helper->display( 'metaboxes/campaign-goal' );		
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
	 * When a campaign is published, reset the campaign end date based
	 * on the original number of days set when submitting.
	 *
	 * @since Astoundify Crowdfunding 1.6
	 *
	 * @return void
	 */
	public function update_post_date_on_publish() {
		global $post;

		if ( ! isset ( $post ) )
			return;

		if ( 'pending' != $post->post_status )
			return $post;

		$length = $post->campaign_length;

		$end_date = strtotime( sprintf( '+%d days', $length ) );
		$end_date = get_gmt_from_date( date( 'Y-m-d H:i:s', $end_date ) );

		update_post_meta( $post->ID, 'campaign_end_date', sanitize_text_field( $end_date ) );
	}	
}

endif; // End class_exists check