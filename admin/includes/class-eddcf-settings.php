<?php
/**
 * The class that sets up the settings area for crowdfunding inside EDD's settings.
 *
 * @class 		EDDCF_Settings
 * @version		1.0.0
 * @package		EDD Crowdfunding/Classes/EDDCF_Settings
 * @copyright 	Copyright (c) 2014, Eric Daams	
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @category	Class
 * @author 		Studio164a
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EDDCF_Settings' ) ) : 

/**
 * EDDCF_Settings
 *
 * @since 		1.0.0
 */
class EDDCF_Settings {

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
	 * @param 	EDD_Crowdfunding $eddcf
	 * @return 	void
	 * @access 	private
	 * @since	1.0.0
	 */
	private function __construct( EDD_Crowdfunding $eddcf ) {
		$this->eddcf = $eddcf;

		add_filter( 'edd_settings_tabs', array( $this, 'add_settings_tab' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Create the class object during plugin startup.
	 *
	 * @param 	EDD_Crowdfunding $eddcf
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public static function start( EDD_Crowdfunding $eddcf ) {
		if ( false === $eddcf->is_start() ) {
			return;
		}

		new EDDCF_Settings( $eddcf );
	}

	/**
	 * Add the Crowdfunding tab to the settings area.
	 *
	 * @param 	array 	$tabs
	 * @return 	array
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function add_settings_tab( $tabs ) {
		$tabs['crowdfunding'] = __( 'Crowdfunding', 'eddcf' );
		return $tabs;
	}

	/**
	 * Register crowdfunding settings. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function register_settings() {
		add_settings_section(
			'edd_settings_crowdfunding', 
			__return_null(), 
			'__return_false', 
			'edd_settings_crowdfunding'
		);

		foreach ( $this->get_settings() as $option ) {

			$name = isset( $option['name'] ) ? $option['name'] : '';

			$callback = $option['callback'];

			add_settings_field( 
				$option['id'], 
				$name, 
				$option['callback'], 
				'edd_settings_crowdfunding', 
				'edd_settings_crowdfunding', 
				array( 
					'section'	=> 'crowdfunding', 
					'id'      => isset( $option['id'] )      ? $option['id']      : null,
					'desc'    => ! empty( $option['desc'] )  ? $option['desc']    : '',
					'name'    => isset( $option['name'] )    ? $option['name']    : null,
					'size'    => isset( $option['size'] )    ? $option['size']    : null,
					'options' => isset( $option['options'] ) ? $option['options'] : ''
				)
			);
		}
	}

	/**
	 * Returns the settings. 
	 *
	 * @return 	array
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function get_settings() {
		return apply_filters( 'eddcf_settings', array(
			'campaign_types' => array(
				'id'		=> 'campaign_types',
				'name'		=> __( 'Supported Campaign Types', 'eddcf' ), 
				'desc'		=> __( 'Choose the types of campaigns you want to allow on your site.', 'eddcf' ), 
				'callback'	=> array( $this, 'campaign_types_setting' ), 
				'options'	=> eddcf_campaign_types()->types()
			)
		) );
	}

	/**
	 * Display the campaign type selection field.
	 *
	 * @global 	array 	$edd_options
	 * @param 	array 	$args
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function campaign_types_setting( $args ) {
		global $edd_options, $pagenow;

		echo get_current_screen();

		$active_types = eddcf_campaign_types()->active_types();

		foreach ( $args['options'] as $key => $option ) {
			
			if ( isset( $edd_options[ 'campaign_types' ][ $key ] ) ) {
				$enabled = '1';
			}
			else {
				$enabled = null;
			}

			if ( array_key_exists( $active_types[ $key ] ) ) {
				$disabled = false;
			}
			else {
				$disabled = true;
			}
			?>
			<input name="edd_settings[campaign_types][<?php echo $key ?>]" id="edd_settings_campaign_types_<?php echo $key ?>" type="checkbox" value="1" <?php checked( '1', $enabled, false ) ?> />&nbsp;
			<label for="edd_settings[campaign_types][<?php echo $key ?>]"><strong><?php echo $option['title'] ?></strong> - <?php echo $option['description'] ?></label><br />
			<?php 
		}
	}
}

endif; // End class_exists check