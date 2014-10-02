<?php
/**
 * Manage plugin installation.
 *
 * @class 		EDDCF_Install
 * @version		1.0
 * @package		EDDCF/Classes/EDDCF_Install
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Install' ) ) : 

/**
 * EDDCF_Install
 *
 * @since 		1.0.0
 */
class EDDCF_Install {

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

		$this->setup_cron_jobs();

		flush_rewrite_rules();
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