<?php
/**
 * Manage plugin installation.
 *
 * @class 		EDD_Crowdfunding_Install
 * @version		1.0
 * @package		EDD_Crowdfunding/Classes/EDD_Crowdfunding_Install
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Crowdfunding_Install' ) ) : 

/**
 * EDD_Crowdfunding_Install
 *
 * @since 		1.0.0
 */
class EDD_Crowdfunding_Install {

	/**
	 * Includes directory. 
	 * @var 	string
	 * @access 	private
	 */
	private $includes_dir;

	/**
	 * Set up includes directory, then run installation routines. 
	 * 
	 * @return 	void
	 * @access 	public
	 * @since	1.0.0
	 */
	public function __construct() {
		$this->includes_dir = EDD_Crowdfunding::includes_dir();
		
		$this->setup_roles();

		$this->setup_cron_jobs();

		flush_rewrite_rules();
	}

	/**
	 * Add roles & caps for EDD Crowdfunding. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function setup_roles() {
		require_once( $this->includes_dir . 'class-crowdfunding-roles.php' );

		$roles = new EDD_Crowdfunding_Roles;
		$roles->add_roles();
		$roles->add_caps();
	}

	/**
	 * Add cron jobs that need to be run for EDD Crowdfunding.
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function setup_cron_jobs() {
		wp_clear_scheduled_hook( 'eddcf_check_for_completed_campaigns' );
		wp_schedule_event( time(), 'hourly', 'eddcf_check_for_completed_campaigns' );

		wp_clear_scheduled_hook( 'eddcf_process_payments' );
		wp_schedule_event( time(), 'hourly', 'eddcf_process_payments' );
	}
}

endif; // End class_exists check