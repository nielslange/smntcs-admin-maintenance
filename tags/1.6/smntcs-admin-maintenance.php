<?php
/**
 * Plugin Name: SMNTCS Admin Maintenance
 * Plugin URI: https://github.com/nielslange/smntcs-admin-maintenance
 * Description: Enables admins to put the <a href="https://codex.wordpress.org/Administration_Screens" target="_blank">Administration Screens</a> into maintenance mode
 * Author: Niels Lange
 * Author URI: https://nielslange.com
 * Text Domain: smntcs-wapuu-widget
 * Domain Path: /languages/
 * Version: 1.6
 * Requires at least: 3.4
 * Requires PHP: 5.6
 * Tested up to: 5.3
 * License: GPL3+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage SMNTCS Admin Maintenance
 * @author     Niels Lange <info@nielslange.de>
 * @license    GPL3+ https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Avoid direct plugin access
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '¯\_(ツ)_/¯' );
}

add_action( 'plugins_loaded', 'smntcs_admin_maintenance_load_textdomain' );
/**
 * Load text domain
 */
function smntcs_admin_maintenance_load_textdomain() {
	load_plugin_textdomain( 'smntcs-admin-maintenance', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'customize_register', 'smntcs_admin_maintenance_register_customize' );
/**
 * Enhance customizer
 *
 * @param WP_Customize_Manager $wp_customize The instance of the WP_Customize_Manager class.
 */
function smntcs_admin_maintenance_register_customize( $wp_customize ) {
	$users = get_users( array( 'roles' => 'administrator' ) );
	foreach ( $users as $user ) {
		$choices[ $user->ID ] = $user->user_nicename;
	}

	$wp_customize->add_section(
		'smntcs_admin_maintenance_section',
		array(
			'priority' => 500,
			'title'    => __( 'Admin Maintenance ', 'smntcs-admin-maintenance' ),
		)
	);

	$wp_customize->add_setting(
		'smntcs_admin_maintenance_enable',
		array(
			'default'           => false,
			'type'              => 'option',
			'callback_function' => 'smntcs_admin_maintenance_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'smntcs_admin_maintenance_enable',
		array(
			'label'   => __( 'Enable Admin Maintenance', 'smntcs-admin-maintenance' ),
			'section' => 'smntcs_admin_maintenance_section',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'smntcs_admin_maintenance_uid',
		array(
			'default'           => '',
			'type'              => 'option',
			'callback_function' => 'smntcs_admin_maintenance_sanitize_integer',
		)
	);

	$wp_customize->add_control(
		'smntcs_admin_maintenance_uid',
		array(
			'label'   => __( 'Grant access to', 'smntcs-admin-maintenance' ),
			'section' => 'smntcs_admin_maintenance_section',
			'type'    => 'select',
			'choices' => $choices,
		)
	);
}

/**
 * Sanitize customizer integer input
 *
 * @param bool $input The input to check.
 *
 * @return null|integer
 */
function smntcs_admin_maintenance_sanitize_integer( $input ) {
	if ( is_numeric( $input ) ) {
		return absint( $input );
	} else {
		return new WP_Error( 'admin-maintenance', '¯\_(ツ)_/¯' );
	}
}

/**
 * Sanitize customizer checkbox input
 *
 * @param bool $input The input to check.
 *
 * @return bool
 */
function smntcs_admin_maintenance_sanitize_checkbox( $input ) {
	return ( isset( $input ) ? true : false );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'smntcs_admin_maintenance_settings_link' );
/**
 * Add settings link on plugin page
 *
 * @param string $links The settings link on the plugin page.
 *
 * @return mixed
 */
function smntcs_admin_maintenance_settings_link( $links ) {
	$admin_url     = admin_url( 'customize.php?autofocus[control]=smntcs_admin_maintenance_enable' );
	$settings_link = sprintf( '<a href="%s">%s</a>', $admin_url, __( 'Settings', 'smntcs-admin-maintenance' ) );
	array_unshift( $links, $settings_link );

	return $links;
}

add_action( 'authenticate', 'smntcs_admin_maintenance_enqueue', 20, 3 );
/**
 * Handle authentication
 *
 * @param Null|WP_User|WP_Error $user The user object.
 * @param string                $username The user's username.
 * @param string                $password The user's password.
 *
 * @return Null|WP_User|WP_Error
 */
function smntcs_admin_maintenance_enqueue( $user, $username, $password ) {
	if ( true === get_option( 'smntcs_admin_maintenance_enable' ) ) {
		if ( isset( $user->ID ) && get_option( 'smntcs_admin_maintenance_uid' ) !== (int) $user->ID ) {
			$user = new WP_Error( 'admin-maintenance', __( 'The <strong>Administration Screens</strong> are currently in maintenance mode. Please try again later.', 'smntcs-admin-maintenance' ) );
		}
	}

	return $user;
}

add_filter( 'option_smntcs_admin_maintenance_enable', 'force_bool' );
/**
 * Convert numeric character to boolean
 *
 * @param string $value The value to be casted.
 *
 * @return boolean
 */
function force_bool( $value ) {
	if ( is_numeric( $value ) ) {
		return (bool) $value;
	}
}

add_filter( 'option_smntcs_admin_maintenance_uid', 'force_int' );
/**
 * Convert numeric character to integer
 *
 * @param string $value The value to be casted.
 *
 * @return int
 */
function force_int( $value ) {
	if ( is_numeric( $value ) ) {
		return (int) $value;
	}
}
