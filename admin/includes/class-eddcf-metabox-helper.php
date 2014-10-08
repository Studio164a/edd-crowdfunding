<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'EDDCF_Metabox_Helper' ) ) : 

/**
 * EDDCF Meta Box Helper
 *
 * @class 		EDDCF_Metabox_Helper
 * @author 		Studio164a
 * @category 	Admin
 * @package 	EDD Crowdfunding/Admin/Meta Boxes
 * @version     0.1
 */
class EDDCF_Metabox_Helper {

	/**
	 * @var 	string Nonce action.
	 * @access 	protected
	 */
	protected $nonce_action;

	/**
	 * @var 	string Nonce name. 
	 * @access 	protected
	 */
	protected $nonce_name = '_eddcf_nonce';

	/**
	 * @var 	boolean Whether nonce has been added. 
	 * @access 	protected
	 */
	protected $nonce_added = false;

	/**
	 * Create a helper instance. 
	 *
	 * @param 	string $nonce_action 
	 * @return 	void
	 * @since 	0.1
	 */
	public function __construct( $nonce_action = 'eddcf' ) {
		$this->nonce_action = $nonce_action;
	} 

	/**
	 * Metabox callback wrapper. 
	 *
	 * Every meta box is registered with this method as its callback, 
	 * and then delegates to the appropriate view. 
	 * 
	 * @param 	WP_Post 	$post 	The post object.
	 * @param 	array 		$args 	The arguments passed to the meta box, including the view to render. 
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function metabox_display( WP_Post $post, array $args ) {	
		if ( ! isset( $args['args']['view'] ) ) {
			return;
		}	

		$this->display( $args['args']['view'] );
	}

	/**
	 * Display a metabox with the given view.
	 *
	 * @param 	string 		The view to render.
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function display( $view ) {		
		/**
		 * Set the nonce.
		 */
		if ( $this->nonce_added === false ) {

			wp_nonce_field( $this->nonce_action, $this->nonce_name );

			$this->nonce_added = true;
		}

		do_action( 'eddcf_metabox_before', $view );

		eddcf_admin_view( $view );

		do_action( 'eddcf_metabox_after', $view );
	}

	/**
	 * Verifies that the user who is currently logged in has permission to save the data
	 * from the meta box to the database.
	 *
	 * Hat tip Tom McFarlin: http://tommcfarlin.com/wordpress-meta-boxes-each-component/
	 *
	 * @param 	integer 	$post_id 	The current post being saved.
	 * @return 	boolean 				True if the user can save the information
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function user_can_save( $post_id ) {
	    $is_autosave = wp_is_post_autosave( $post_id );
	    $is_revision = wp_is_post_revision( $post_id );
	    $is_valid_nonce = ( isset( $_POST[ $this->nonce_name ] ) && wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action ) );
 
	    return ! ( $is_autosave || $is_revision ) && $is_valid_nonce;
	}
}

endif; // End class_exists check