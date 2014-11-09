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
	 * The EDDCF_Public object. 
	 * @var 	EDDCF_Public
	 * @access  private
	 */
	private $eddcf_public;

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
	 * @param 	EDDCF_Public 		$eddcf_public
	 * @param 	EDD_Crowdfunding 	$eddcf
	 * @return 	void
	 * @access 	private
	 * @since	1.0.0
	 */
	private function __construct( EDDCF_Public $eddcf_public, EDD_Crowdfunding $eddcf ) {
		$this->eddcf_public = $eddcf_public;
		$this->eddcf = $eddcf;
		$this->base_templates = $this->eddcf_public->public_dir . 'templates/';
		$this->theme_templates = apply_filters( 'eddcf_theme_template_path', 'eddcf/' );

		add_filter( 'template_include', array( $this, 'template_loader' ) );
	}

	/**
	 * Create the class object during plugin startup.
	 *
	 * @param 	EDD_Crowdfunding 	$eddcf
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public static function start( EDDCF_Public $eddcf_public, EDD_Crowdfunding $eddcf ) {
		if ( false === $eddcf_public->is_start() ) {
			return;
		}

		new EDDCF_Templates( $eddcf_public, $eddcf );
	}

	/**
	 * Load a template. 
	 *  
	 * @globa 	WP_Query 	$wp_query
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

		$files = apply_filters( 'eddcf_template_loader', $files );

		foreach ( $files as $file ) {
			$find[] = $file;
			$find[] = $this->theme_templates . $file;
		}

		if ( ! empty( $files ) ) {
			$template = locate_template( $find );

			if ( ! $template ) {
				$template = $this->base_templates . $file;
			}
		}

		return $template;
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
}

endif; // End class_exists check