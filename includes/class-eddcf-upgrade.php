<?php
/**
 * This file contains the class in charge of handling version upgrades. 
 *
 * @class 		EDDCF_Upgrade
 * @version		1.0
 * @package		Crowdfunding EDD/Classes/EDDCF_Upgrade
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Upgrade' ) ) : 

/**
 * EDDCF_Upgrade
 *
 * @since 		1.0
 */
class EDDCF_Upgrade {

	/**
	 * Current database version. 
	 * @var 	false|string
	 * @access 	private
	 */
	private $db_version;

	/**
	 * Edge version.
	 * @var 	string
	 * @access 	private
	 */
	private $edge_version;

	/**
	 * Array of methods to perform when upgrading to specific versions. 	 
	 * @var 	array
	 * @access 	private
	 */
	private $upgrade_actions = array();

	/**
	 * Option key for upgrade log. 
	 * @var 	string
	 * @access 	private
	 */
	private $upgrade_log_key = 'eddcf_upgrade_log';
	
	/**
	 * Option key for plugin version.
	 * @var 	string
	 * @access 	private
	 */
	private $version_key = 'eddcf_version';

	/**
	 * Upgrade from the current version stored in the database to the live version. 
	 *
	 * @param 	false|string $db_version	
	 * @param 	string $edge_version	
 	 * @return 	void
	 * @static
	 * @access 	public
	 * @since 	1.0
	 */
	public static function upgrade_from( $db_version, $edge_version ) {
		if ( self::requires_upgrade( $db_version, $edge_version ) ) {
			new EDDCF_Upgrade( $db_version, $edge_version );
		}
	}

	/**
	 * Manages the upgrade process. 
	 *
	 * @param 	false|string $db_version
	 * @param 	string $edge_version
	 * @return 	void
	 * @access 	private
	 * @since 	1.0
	 */
	private function __construct( $db_version, $edge_version ) {
		$this->db_version = $db_version;
		$this->edge_version = $edge_version;

		/**
		 * Perform version upgrades.
		 */
		$this->do_upgrades();

		/**
		 * Log the upgrade and update the database version.
		 */
		$this->save_upgrade_log();
		$this->update_db_version();
	}

	/**
	 * Perform version upgrades. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0
	 */
	private function do_upgrades() {
		if ( empty( $this->upgrade_actions ) || ! is_array( $this->upgrade_actions ) ) {
			return;
		}

		foreach ( $this->upgrade_actions as $version => $method ) {
			if ( self::requires_upgrade( $this->db_version, $version ) ) {
				call_user_func( $method );
			}
		}
	}

	/**
	 * Evaluates two version numbers and determines whether an upgrade is 
	 * required for version A to get to version B. 
	 *
	 * @param 	false|string $version_a
	 * @param 	string $version_b
	 * @return 	bool
	 * @static
	 * @access 	public
	 * @since 	1.0
	 */
	public static function requires_upgrade( $version_a, $version_b ) {
		return $version_a === false || version_compare( $version_a, $version_b, '<' );
	}	

	/**
	 * Saves a log of the version to version upgrades made. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0
	 */
	private function save_upgrade_log() {
		$log = get_option( $this->upgrade_log_key );

		if ( false === $log || ! is_array( $log ) ) {
			$log = array();
		}

		$log[] = array(
			'timestamp'		=> time(), 
			'from'			=> $this->db_version, 
			'to'			=> $this->edge_version
		);

		update_option( $this->upgrade_log_key, $log );
	}

	/**
	 * Upgrade complete. This saves the new version to the database. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0
	 */
	private function update_db_version() {
		update_option( $this->version_key, $this->edge_version );
	}
}

endif; // End class_exists check