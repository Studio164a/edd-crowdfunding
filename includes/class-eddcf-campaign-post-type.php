<?php
/**
 * The class that modifies Easy Digital Download's 'download' post type and turns it into a 'campaign' post type. 
 *
 * Under the hood, campaigns are still stored in the database as 'download'.
 *
 * @class 		EDDCF_Campaign_Post_Type
 * @version		1.0.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Campaign_Post_Type
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Campaign_Post_Type' ) ) : 

/**
 * EDDCF_Campaign_Post_Type
 *
 * @since 		1.0.0
 */
class EDDCF_Campaign_Post_Type {

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
		$this->edd_crowdfunding = $eddcf;

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

		new EDDCF_Campaign_Post_Type( $eddcf );
	}

	/**
	 * Setup modifications with hooks & filters on EDD's download post type. 
	 *
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function setup() {
		add_filter( 'edd_download_labels', 			array( $this, 'download_labels' ) );
		add_filter( 'edd_default_downloads_name', 	array( $this, 'download_names' ) );
		add_filter( 'edd_download_supports', 		array( $this, 'download_supports' ) );
	}

	/**
	 * Change download labels to refer to campaigns intead. 
	 *
	 * @see 	edd_download_labels
	 *
	 * @param 	array $labels
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function download_labels( $labels ) {
		$labels =  apply_filters( 'eddcf_campaign_labels', array(
			'name' 				=> __( 'Campaigns', 'eddcf' ),
			'singular_name' 	=> __( 'Campaign', 'eddcf' ),
			'add_new' 			=> __( 'Add New', 'eddcf' ),
			'add_new_item' 		=> __( 'Add New Campaign', 'eddcf' ),
			'edit_item' 		=> __( 'Edit Campaign', 'eddcf' ),
			'new_item' 			=> __( 'New Campaign', 'eddcf' ),
			'all_items' 		=> __( 'All Campaigns', 'eddcf' ),
			'view_item' 		=> __( 'View Campaign', 'eddcf' ),
			'search_items' 		=> __( 'Search Campaigns', 'eddcf' ),
			'not_found' 		=> __( 'No Campaigns found', 'eddcf' ),
			'not_found_in_trash'=> __( 'No Campaigns found in Trash', 'eddcf' ),
			'parent_item_colon' => '',
			'menu_name' 		=> __( 'Campaigns', 'eddcf' )
		) );

		return $labels;
	}

	/**
	 * Change "Download" to "Campaign" and "Downloads" to "Campaigns". 
	 *
	 * @see 	edd_default_downloads_name
	 *
	 * @param 	array $labels
	 * @return 	array
	 * @access  public
	 * @since 	1.0.0
	 */
	public function download_names( $labels ) {
		$cpt_labels = $this->download_labels( array() );

		$labels = array(
			'singular' => $cpt_labels[ 'singular_name' ],
			'plural'   => $cpt_labels[ 'name' ]
		);

		return $labels;
	}

	/**
	 * Add excerpt, comments and author support for downloads/campaigns. 
	 *	
	 * @see 	edd_download_supports
	 * 
	 * @param 	array $supports
	 * @return 	array 
	 * @access  public
	 * @since 	1.0.0
	 */
	public function download_supports( $supports ) {
		$supports[] = 'excerpt';
		$supports[] = 'comments';
		$supports[] = 'author';
		return $supports;
	}
}

endif; // End class_exists check