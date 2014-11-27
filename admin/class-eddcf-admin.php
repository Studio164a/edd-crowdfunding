<?php
/**
 * Bootstraps the admin side functionality of the plugin. 
 *
 * @class 		EDDCF_Admin
 * @version		1.0.0
 * @package		EDDCF/Classes/EDDCF_Admin
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Admin' ) ) : 

/**
 * EDDCF_Admin
 *
 * @since 		1.0.0
 * @final
 */
final class EDDCF_Admin {

	/**
	 * Stores a reference to the core EDD_Crowdfunding object. 
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
		if ( false === $eddcf->is_start() ) {
			return;
		}

		new EDDCF_Admin( $eddcf );
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
	}	

	/**
	 * Set the paths for the admin directory paths. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function setup_paths() {
		$this->admin_dir = $this->eddcf->admin_dir;
		$this->admin_url = $this->eddcf->admin_url;
		$this->admin_includes = $this->admin_dir . 'includes/';
	}

	/**
	 * Load files that we need. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function load_dependencies() {
		require_once( $this->admin_includes . 'functions-eddcf-admin.php' );
		require_once( $this->admin_includes . 'class-eddcf-metabox-helper.php' );
		require_once( $this->admin_includes . 'class-eddcf-admin-campaign-post-type.php' );
		require_once( $this->admin_includes . 'class-eddcf-settings.php' );
	}

	/**
	 * Set up hooks and filters. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function attach_hooks_and_filters() {
		add_action( 'eddcf_start', array( 'EDDCF_Admin_Campaign_Post_Type', 'start' ), 5 );
		add_action( 'eddcf_start', array( 'EDDCF_Settings', 'start' ), 5 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );		
	}

	/**
	 * Load frontend scripts & stylesheets. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function admin_scripts() {		
		$screen = get_current_screen();

		if ( in_array( $screen->id, $this->get_eddcf_screens() ) ) {
			$assets_path = $this->admin_url . 'assets';

			wp_register_style( 'eddcf-admin', $this->admin_url . 'assets/css/eddcf-admin.css', array(), EDD_Crowdfunding::VERSION );
			wp_enqueue_style( 'eddcf-admin' );
		}
	}

	/**
	 * Returns an array of screen IDs where the Charitable scripts should be loaded. 
	 *
	 * @uses 	eddcf_admin_screens
	 * 
	 * @return 	array
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function get_eddcf_screens() {
		return apply_filters( 'eddcf_admin_screens', array(
			'download'
		) );
	}
}

endif; // End class_exists check