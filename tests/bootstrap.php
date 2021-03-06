<?php

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugins() {
	require dirname( __FILE__ ) . '/../../easy-digital-downloads/easy-digital-downloads.php';
	require dirname( __FILE__ ) . '/../edd-crowdfunding.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugins' );

require $_tests_dir . '/includes/bootstrap.php';

/**
 * Activate Easy Digital Downloads.
 */
activate_plugin( 'easy-digital-downloads/easy-digital-downloads.php' );
edd_install();

/**
 * Activate EDD Crowdfunding
 */
activate_plugin( 'edd-crowdfunding/edd-crowdfunding.php' );
EDD_Crowdfunding::activate();