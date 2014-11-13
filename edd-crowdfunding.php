<?php
/**
 * Plugin Name:			EDD Crowdfunding
 * Plugin URI:			https://github.com/Studio164a/edd-crowdfunding/
 * Description:			Crowdfund with Easy Digital Downloads.
 * Author:				Studio 164a
 * Author URI:			http://164a.com
 * Version:     		1.0.0
 * Text Domain: 		eddcf
 * GitHub Plugin URI: 	https://github.com/Studio164a/edd-crowdfunding
 * GitHub Branch:    	master
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDD_Crowdfunding' ) ) : 

/**
 * Main Crowd Funding Class
 *
 * @since 		1.0.0
 */
class EDD_Crowdfunding {

	/**
	 * @var 	EDD_Crowdfunding
	 * @static
	 * @access 	private
	 */
	private static $instance;

	/**
	 * @var 	string
	 */
	const VERSION = '1.0.0';

	/**
	 * @var 	boolean
	 * @access 	private
	 */
	private $atcf_compatibility; 

	/**
	 * Main EDD_Crowdfunding instance.
	 *
	 * Ensures that only one instance of EDD_Crowdfunding exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @return 	EDD_Crowdfunding	 
	 * @since 	1.0.0
	 */
	public static function get_instance() {
		if ( ! isset ( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Start your engines.
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function __construct() {
		$this->setup_globals();
		
		$this->load_dependencies();

		$this->maybe_upgrade();
		
		$this->attach_hooks_and_filters();

		$this->maybe_load_admin();

		$this->maybe_load_public();

		do_action('eddcf_start', $this);
	}

	/**
	 * Set some smart defaults to class variables.
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function setup_globals() {
		$this->file         = __FILE__;
		$this->basename     = plugin_basename( $this->file );
		$this->plugin_dir   = plugin_dir_path( $this->file );
		$this->plugin_url   = plugin_dir_url( $this->file );
		$this->includes_dir = $this->plugin_dir . 'includes/';
		$this->admin_dir	= $this->plugin_dir . 'admin/';
		$this->admin_url	= $this->plugin_url . 'admin/';
		$this->public_dir	= $this->plugin_dir . 'public/';
		$this->public_url	= $this->plugin_url . 'public/';

		$this->domain       = 'eddcf';
	}

	/**
	 * Include required files.
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function load_dependencies() {
		require_once( $this->includes_dir . 'class-eddcf-campaign-post-type.php' );
		require_once( $this->includes_dir . 'class-eddcf-campaign-types.php' );
		require_once( $this->includes_dir . 'class-eddcf-campaign.php' );
		require_once( $this->includes_dir . 'class-eddcf-cart.php' );
		require_once( $this->includes_dir . 'class-eddcf-checkout.php' );
		require_once( $this->includes_dir . 'class-eddcf-gateways.php' );
		require_once( $this->includes_dir . 'class-eddcf-templates.php' );
		require_once( $this->includes_dir . 'class-eddcf-template.php' );
		require_once( $this->includes_dir . 'functions-eddcf-core.php' );
		require_once( $this->includes_dir . 'functions-eddcf-template.php' );

		$this->maybe_load_atcf_compat();
	}

	/**
	 * Load compatibility functions/classes for Crowdfunding by Astoundify
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function maybe_load_atcf_compat() {
		$compatibility_mode = true;

		// You can remove compatibility mode if you didn't previously 
		// use Crowdfunding by Astoundify or if you've updated your codebase.
		if ( defined( 'ATCF_COMPATIBILITY' ) ) {
			$compatibility_mode = ATCF_COMPATIBILITY;
		}

		require_once( $this->includes_dir . 'atcf-compat/atcf-compat-functions.php' );
		require_once( $this->includes_dir . 'atcf-compat/atcf-compat-classes.php' );
		require_once( $this->includes_dir . 'atcf-compat/atcf-compat-hooks.php' );
	}

	/**
	 * Setup the default hooks and actions
	 *
	 * @return 	void
	 * @access 	private
	 * @since  	1.0.0
	 */
	private function attach_hooks_and_filters() {
		// Various classes that need to be loaded at the start
		add_action( 'eddcf_start', array( 'EDDCF_Campaign_Post_Type', 'start' ), 1 );
		add_action( 'eddcf_start', array( 'EDDCF_Gateways', 'start' ), 1 );
		add_action( 'eddcf_start', array( 'EDDCF_Templates', 'start' ), 1 );
		add_action( 'eddcf_start', array( 'EDDCF_Checkout', 'start' ), 1 );
		add_action( 'eddcf_start', array( 'EDDCF_Cart', 'start' ), 1 );
		
		// Upgrade Routine
		add_action( 'admin_init', array( $this, 'maybe_upgrade' ) );

		// Textdomain
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Perform upgrade routine if necessary. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function maybe_upgrade() {
		$version_db = get_option( 'eddcf_version' );

		if ( $version_db !== self::VERSION ) {	

			require_once( $this->includes_dir . 'class-eddcf-upgrade.php' );
			
			EDDCF_Upgrade::upgrade_from( $version_db, self::VERSION );
		}
	}

	/**
	 * Load admin functionality if we're in the admin area. 
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function maybe_load_admin() {
		if ( ! is_admin() ) {
			return;
		}

		require_once( $this->admin_dir . 'class-eddcf-admin.php' );

		add_action('eddcf_start', array( 'EDDCF_Admin', 'start' ) );
	}

	/**
	 * Load public functionality when we're on the front-facing side of the site.
	 *
	 * @return 	void
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function maybe_load_public() {
		if ( is_admin() ) {
			return;
		}

		require_once( $this->public_dir . 'class-eddcf-public.php' );	

		add_action('eddcf_start', array( 'EDDCF_Public', 'start' ) );
	}

	/**
	 * Tells us whether we're on the eddcf_start action. 
	 *
	 * @return 	bool
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function is_start() {
		return 'eddcf_start' == current_filter();
	}

	/**
	 * Set up multilingual support. 
	 *
	 * @return  void
     * @access 	public
     * @since   1.0.0     
     */
    public function load_textdomain() {
        // Set filter for language directory
        $lang_dir = $this->plugin_dir . 'languages/';
        $lang_dir = apply_filters( 'eddcf_languages_directory', $this->plugin_dir . 'languages/' );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'eddcf' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'eddcf', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/eddcf/' . $mofile;

        // Look in global /wp-content/languages/eddcf/ folder
        if ( file_exists( $mofile_global ) ) { 
            load_textdomain( 'eddcf', $mofile_global );
        } 
        // Look in local /wp-content/plugins/eddcf/languages/ folder
        elseif ( file_exists( $mofile_local ) ) {            
            load_textdomain( 'eddcf', $mofile_local );
        } 
        // Load the default language files
        else {            
            load_plugin_textdomain( 'eddcf', false, $lang_dir );
        }
    }

	/**
	 * Return plugin includes directory. 
	 *
	 * @return 	string
	 * @access 	public
	 * @static
	 * @since 	1.0.0
	 */
	public static function includes_dir() {
		return plugin_dir_path( __FILE__ ) . 'includes/';
	}

	/**
	 * Runs on plugin activation. 
	 *
	 * @see register_activation_hook
	 *
	 * @return 	void
	 * @access 	public
	 * @static
	 * @since	1.0.0
	 */
	public static function activate() {
		require_once( self::includes_dir() . 'class-eddcf-install.php' );
		new EDDCF_Install();
	}

	/**
	 * Runs on plugin deactivation. 
	 *
	 * @see 	register_deactivation_hook
	 *
	 * @return 	void
	 * @access 	public
	 * @static
	 * @since 	1.0.0
	 */
	public static function deactivate() {
		require_once( self::includes_dir() . 'class-eddcf-uninstall.php' );
		new EDDCF_Uninstall();
	}

	/**
	 * Throw error on object clone. 
	 *
	 * This class is specifically designed to be instantiated once. You can retrieve the instance using get_charitable()
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @return 	void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'eddcf' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class. 
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @return 	void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'eddcf' ), '0.1' );
	}
}

/**
 * The main function responsible for returning the one true Crowd Funding Instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $crowdfunding = eddcf(); ?>
 *
 * @return 	EDD_Crowdfunding
 * @since  	1.0.0
 */
function eddcf() {
	// If Easy Digital Downloads isn't installed, we're going to short circuit and just display a warning. 
	if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		if ( ! class_exists( 'EDD_Extension_Activation') ) {
			require_once( 'includes/class-extension-activation.php' );

			$activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
			$activation->run();	
		}
	} 
	else {
		return EDD_Crowdfunding::get_instance();
	}	
}

add_action( 'plugins_loaded', 'eddcf' );

/**
 * Define the EDD slug
 * 
 * @uses 	eddcf_edd_slug
 */
define( 'EDD_SLUG', apply_filters( 'eddcf_edd_slug', 'campaigns' ) );

/**
 * Register the activation hook. 
 */
register_activation_hook( __FILE__, array( 'EDD_Crowdfunding', 'activate' ) );

/**
 * Ditto deactivation.
 */
register_deactivation_hook( __FILE__, array( 'EDD_Crowdfunding', 'deactivate' ) );

endif; // End class_exists check