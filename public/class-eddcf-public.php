<?php
/**
 * Bootstraps the front-facing functionality of the plugin. 
 *
 * @class 		EDDCF_Public
 * @version		1.0.0
 * @package		EDDCF/Classes/EDDCF_Public
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Public' ) ) : 

/**
 * EDDCF_Public
 *
 * @since 		1.0.0
 * @final
 */
final class EDDCF_Public {

	/**
	 * Stores a reference to the core EDDCF object. 
	 * @var 	EDD_Crowdfunding
	 * @access 	private
	 */
	private $eddcf;

	/**
	 * Instantiate object, but first verify that this is the eddcf_start action.
	 *
	 * @param 	EDD_Crowdfunding $eddcf
	 * @return 	void
	 * @access 	public
	 * @static
	 * @since 	1.0.0
	 */
	public static function start( EDD_Crowdfunding $eddcf ) {
		if ( ! $eddcf->is_start() ) {
			return;
		}

		new EDDCF_Public( $eddcf );
	}

	/**
	 * Create class object.
	 * 
	 * @param 	EDD_Crowdfunding $eddcf
	 * @return 	void
	 * @access 	private
	 * @since	1.0.0
	 */
	private function __construct( EDD_Crowdfunding $eddcf ) {
		$this->eddcf = $eddcf;

		$this->setup_paths();

		$this->load_dependencies();		

		$this->attach_hooks_and_filters();

		do_action( 'eddcf_public_start', $this, $eddcf );
	}	

	/**
	 * Set the paths for the public directory paths. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function setup_paths() {
		$this->public_dir = $this->eddcf->public_dir;
		$this->public_url = $this->eddcf->public_url;
		$this->public_includes = $this->public_dir . 'includes/';
	}

	/**
	 * Load files that we need. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function load_dependencies() {
		require_once( $this->public_includes . 'class-eddcf-templates.php' );
	}	

	/**
	 * Set up hooks and filters. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function attach_hooks_and_filters() {
		add_action( 'eddcf_public_start', array( 'EDDCF_Templates', 'start' ), 10, 2 );
		// add_action( 'wp_enqueue_script', array( $this, 'frontend_scripts' ) );
	}

	/**
	 * Checks whether we're currently on the public start hook.  
	 *
	 * @return 	booolean
	 * @access  public
	 * @since 	1.0.0
	 */
	public function is_start() {
		return 'eddcf_public_start' == current_filter();
	}

	/**
	 * Load frontend scripts & stylesheets. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function frontend_scripts() {
		// $is_campaign   = is_singular( 'download' ) || did_action( 'atcf_found_single' ) || apply_filters( 'atcf_is_campaign_page', false );

		// if ( ! $is_campaign )
		// 	return;
	}
}

endif; // End class_exists check