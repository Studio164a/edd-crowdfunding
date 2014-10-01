<?php
/**
 * Manages roles & caps related to EDD Crowdfunding.
 *
 * @class 		EDD_Crowdfunding_Roles
 * @version		1.0
 * @package		EDD_Crowdfunding/Classes/EDD_Crowdfunding_Roles
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Crowdfunding_Roles' ) ) : 

/**
 * EDD_Crowdfunding_Roles
 *
 * @since 		1.0.0
 */
class EDD_Crowdfunding_Roles {

	/**
	 * Add the Campaign Contributor role.
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function add_roles() {
		remove_role( 'campaign_contributor' );

		$campaign_contributor_caps = apply_filters( 'eddcf_campaign_contributor_role', array(
			'read'                   => true,
			'upload_files'           => true,
			'edit_others_pages'      => true,
			'edit_published_pages'   => true,
			'edit_posts'             => true,
			'publish_posts'          => true,
			'delete_posts'           => true,
			'delete_published_posts' => true,
			'edit_published_posts'   => true
		) );

		add_role( 'campaign_contributor', __( 'Campaign Contributor', 'eddcf' ), $campaign_contributor_caps );
	}

	/**
	 * Add the contributor-specific caps
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function add_caps() {
		global $wp_roles;

		if ( class_exists('WP_Roles') ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'campaign_contributor', 'level_1' );
			$wp_roles->add_cap( 'campaign_contributor', 'submit_campaigns' );
			$wp_roles->add_cap( 'campaign_contributor', 'edit_product' );
			$wp_roles->add_cap( 'campaign_contributor', 'edit_products' );
			$wp_roles->add_cap( 'campaign_contributor', 'delete_product' );
			$wp_roles->add_cap( 'campaign_contributor', 'delete_products' );
			$wp_roles->add_cap( 'campaign_contributor', 'publish_products' );
			$wp_roles->add_cap( 'campaign_contributor', 'edit_published_products' );
			$wp_roles->add_cap( 'campaign_contributor', 'assign_product_terms' );
		}
	}
}

endif; // End class_exists check