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
	 * @param 	EDDCF $eddcf
	 * @return 	void
	 * @access 	public
	 * @static
	 * @since 	1.0.0
	 */
	public static function start( EDDCF $eddcf ) {
		if ( ! $eddcf->is_start() ) {
			return;
		}

		new EDDCF_Admin;
	}

	/**
	 * Create class object.
	 * 
	 * @param 	EDDCF $eddcf
	 * @return 	void
	 * @access 	private
	 * @since	1.0.0
	 */
	private function __construct( EDDCF $eddcf ) {
		$this->EDDCF = $eddcf;
		
		$this->setup_admin_paths();

		$this->attach_hooks_and_filters();
	}	

	/**
	 * Set the paths for the admin directory paths. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function setup_admin_paths() {
		$this->admin_dir = $this->eddcf->admin_dir;
		$this->admin_url = $this->eddcf->admin_url;
	}

	/**
	 * Set up hooks and filters. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function attach_hooks_and_filters() {
		add_action( 'admin_enqueue_script', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Load frontend scripts & stylesheets. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function admin_scripts() {		
	}
}

endif; // End class_exists check