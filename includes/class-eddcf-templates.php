<?php
/**
 * The class that manages frontend templates for EDD Crowdfunding. 
 *
 * @class 		EDDCF_Templates
 * @version		1.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Templates
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Templates' ) ) : 

/**
 * EDDCF_Templates
 *
 * @since 		1.0.0
 */
class EDDCF_Templates {

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
	 * @param 	EDD_Crowdfunding 	$eddcf
	 * @return 	void
	 * @access 	private
	 * @since	1.0.0
	 */
	private function __construct( EDD_Crowdfunding $eddcf ) {
		$this->eddcf = $eddcf;
		$this->base_templates = $this->eddcf->public_dir . 'templates/';
		$this->theme_templates = apply_filters( 'eddcf_theme_template_path', 'eddcf/' );

		// Set up base templates for campaign widget, single campaign and archive.
		add_filter( 'template_include', array( $this, 'template_loader' ) );

		// Use our own template for displaying campaign content.
		add_action( 'edd_before_download_content', array( $this, 'campaign_details' ) );

		// Set up pledge options form.
		remove_action( 'edd_purchase_link_top', 'edd_purchase_variable_pricing', 10 );
		add_action( 'edd_purchase_link_top', array( $this, 'pledge_options' ), 10 );
		add_action( 'eddcf_campaign_pledge_options', array( $this, 'pledge_options_list' ), 10 );
		add_action( 'edd_after_price_options', array( $this, 'custom_pledge' ), 5 );
	}

	/**
	 * Create the class object during plugin startup.
	 *
	 * @param 	EDD_Crowdfunding 	$eddcf
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public static function start( EDD_Crowdfunding $eddcf ) {
		if ( false === $eddcf->is_start() ) {
			return;
		}

		new EDDCF_Templates( $eddcf );
	}

	/**
	 * Load a template. 
	 *  
	 * @global 	WP_Query 	$wp_query
	 * @param  	string 		$template
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function template_loader( $template ) {
		global $wp_query;

		$find    = array();
		$files   = array();

		// Viewing a campaign widget.
		if ( $this->is_widget( $wp_query ) ) {
			do_action( 'eddcf_found_widget' );

			$files = apply_filters( 'eddcf_crowdfunding_templates_widget', array( 'campaign-widget.php' ) );
		}
		// Viewing a single campaign.
		else if ( $this->is_campaign( $wp_query ) ) {
			do_action( 'eddcf_found_single' );

			$files = apply_filters( 'eddcf_crowdfunding_templates_campaign', array( 'single-campaign.php', 'single-download.php', 'single.php' ) );
		}
		// Viewing a campaign archive.
		else if ( $this->is_campaigns_archive( $wp_query ) ) {
			do_action( 'eddcf_found_archive' );

			$files = apply_filters( 'eddcf_crowdfunding_templates_archive', array( 'archive-campaigns.php', 'archive-download.php', 'archive.php' ) );
		}
		// Not one of our templates.
		else {
			return $template;
		}

		$files = apply_filters( 'eddcf_template_loader', $files );

		$template = new EDDCF_Template( $files );
		return $template->locate_template();
	}

	/**
	 * Returns whether we are currently viewing the campaign widget.  
	 *
	 * @param 	WP_Query 	$wp_query
	 * @return 	boolean
	 * @access  private
	 * @since 	1.0.0
	 */
	private function is_widget( $wp_query ) {
		return ( isset ( $wp_query->query_vars[ 'widget' ] ) &&
			! eddcf_crowdfunding_disabled( $wp_query->queried_object ) &&
			is_singular( 'download' ) &&
		 	eddcf_theme_supports( 'campaign-widget' ) );
	}

	/**
	 * Returns whether we are currently viewing a single campaign. 
	 *
	 * @param 	WP_Query 	$wp_query
	 * @return 	boolean
	 * @access  private
	 * @since 	1.0.0
	 */
	private function is_campaign( $wp_query ) {
		return is_singular( 'download' ) && 
			! eddcf_crowdfunding_disabled( $wp_query->queried_object ) && 
			( ! eddcf_theme_supports( 'campaign-widget' ) || ! isset( $wp_query->query_vars['widget'] ) );
	}

	/**
	 * Returns whether we are currently viewing the campaigns archive. 
	 *
	 * @param 	WP_Query 	$wp_query
	 * @return 	boolean
	 * @access  private
	 * @since 	1.0.0
	 */
	private function is_campaigns_archive( $wp_query ) {
		return is_post_type_archive( 'download' ) || is_tax( array( 'download_category', 'download_tag' ) );
	}

	/**
	 * Display details about campaign.  
	 *
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function campaign_details() {
		if ( ! eddcf_crowdfunding_disabled() ) {
			eddcf_get_template( 'campaign-details.php' );
		}
	}

	/**
	 * Set up the main pledge options template. 
	 *
	 * @param 	int 		$donation_id
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function pledge_options(  $download_id ) {
		$variable_pricing = edd_has_variable_prices( $download_id );

		if ( ! $variable_pricing ) {
			return;
		}

		// If crowdfunding is disabled for this download, use 
		// EDD's standard variable pricing.
		if ( eddcf_crowdfunding_disabled( get_post( $download_id ) ) ) {
			return edd_purchase_variable_pricing( $download_id );
		}

		eddcf_get_template( 'campaign-pledge-options.php' );
	}

	/**
	 * Display the list of pledge options with checkboxes/radio elements.
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function pledge_options_list() {
		if ( ! eddcf_crowdfunding_disabled() ) {
			eddcf_get_template( 'campaign-pledge-options/pledge-options.php' );
		}
	}

	/**
	 * Display the custom pledge field. 
	 * 
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function custom_pledge() {
		global $edd_options;

		if ( eddcf_crowdfunding_disabled() ) {
			return;
		}

		//if ( isset( $edd_options[ 'atcf_settings_custom_pledge' ] ) ) {
			eddcf_get_template( 'campaign-pledge-options/custom-pledge.php' );
		//}
	}
}

endif; // End class_exists check