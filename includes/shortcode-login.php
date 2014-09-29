<?php
/**
 * Login Shortcode.
 *
 * [appthemer_crowdfunding_login] creates a log in form for users to log in with.
 *
 * @since Astoundify Crowdfunding 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Login Shortcode
 *
 * @since Astoundify Crowdfunding 1.0
 *
 * @return $form
 */
function atcf_shortcode_login() {
	global $post;

	$user = wp_get_current_user();

	ob_start();

	echo '<div class="atcf-login">';
	do_action( 'atcf_shortcode_login', $user, $post );
	echo '</div>';

	$form = ob_get_clean();

	return $form;
}
add_shortcode( 'appthemer_crowdfunding_login', 'atcf_shortcode_login' );

/**
 * Login form
 *
 * @since Astoundify Crowdfunding 1.0
 *
 * @return $form
 */
function atcf_shortcode_login_form() {
	global $edd_options;

	$redirect = isset ( $_GET[ 'redirect_to' ] ) ? $_GET[ 'redirect_to' ] : atcf_get_current_url();

	wp_login_form( apply_filters( 'atcf_shortcode_login_form_args', array(
		'redirect' => esc_url( $redirect )
	) ) );
}
add_action( 'atcf_shortcode_login', 'atcf_shortcode_login_form' );

/**
 * Forgot Password/Register links
 *
 * Append helpful links to the bottom of the login form.
 *
 * @since Astoundify Crowdfunding 1.0
 *
 * @return $form
 */
function atcf_shortcode_login_form_bottom() {
	global $edd_options;

	$add = '<p>
		<a href="' . wp_lostpassword_url() . '">' . __( 'Forgot Password', 'atcf' ) . '</a> ';

	if ( isset( $edd_options[ 'register_page' ] ) ) {
		$add .= _x( 'or', 'login form action divider', 'atcf' );
		$add .= ' <a href="' . esc_url( get_permalink( $edd_options[ 'register_page' ] ) ) . '">' . __( 'Register', 'atcf' ) . '</a>';
	}

	$add .= '</p>';

	return $add;
}
add_action( 'login_form_bottom', 'atcf_shortcode_login_form_bottom' );

function atcf_get_current_url() { 
	return set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ); 
}