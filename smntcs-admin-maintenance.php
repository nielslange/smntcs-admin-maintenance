<?php
/**
 * Plugin Name: SMNTCS Admin Maintenance
 * Plugin URI: https://github.com/nielslange/smntcs-admin-maintenance
 * Description: Enables admins to put the <a href="https://codex.wordpress.org/Administration_Screens" target="_blank">Administration Screens</a> into maintenance mode
 * Author: Niels Lange
 * Author URI: https://nielslange.de
 * Text Domain: smntcs-admin-maintenance
 * Version: 1.9
 * Stable tag: 1.9
 * Tested up to: 5.9
 * Requires PHP: 7.4
 * Requires at least: 3.4
 * License: GPLv2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage SMNTCS Admin Maintenance
 * @author     Niels Lange <info@nielslange.de>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 */

// Avoid direct plugin access.
defined( 'ABSPATH' ) || exit;

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
add_action( 'customize_register', 'smntcs_admin_maintenance_register_customize' );

/**
 * Sanitize customizer integer input
 *
 * @param int $input The number to sanitize.
 * @return int|object The sanitized number or the WP_Error() object.
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
 * @param bool $input The boolean to sanitize.
 * @return bool|object The sanitized boolean or the WP_Error() object.
 */
function smntcs_admin_maintenance_sanitize_checkbox( $input ) {
	return ( isset( $input ) ? true : false );
}

/**
 * Convert numeric character to integer
 *
 * @param mixed $value The mixed input.
 * @return int The numeric output.
 */
function force_int( $value ) {
	if ( is_numeric( $value ) ) {
		return (int) $value;
	}
}
add_filter( 'option_smntcs_admin_maintenance_uid', 'force_int' );

/**
 * Convert numeric character to boolean
 *
 * @param mixed $value The mixed input.
 * @return bool The boolean output.
 */
function force_bool( $value ) {
	if ( is_numeric( $value ) ) {
		return (bool) $value;
	}
}
add_filter( 'option_smntcs_admin_maintenance_enable', 'force_bool' );

/**
 * Add settings link on plugin page
 *
 * @param array $links The original array with customizer links.
 * @return array $links The updated array with customizer links.
 */
function smntcs_admin_maintenance_settings_link( $links ) {
	$admin_url     = admin_url( 'customize.php?autofocus[control]=smntcs_admin_maintenance_enable' );
	$settings_link = sprintf( '<a href="%s">%s</a>', $admin_url, __( 'Settings', 'smntcs-admin-maintenance' ) );
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'smntcs_admin_maintenance_settings_link' );

/**
 * Handle authentication
 *
 * @param object $user The original WP_User() or WP_Error() object.
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @return object $user The updated WP_User() or WP_Error() object.
 */
function smntcs_admin_maintenance_enqueue( $user, $username, $password ) {
	if ( true === get_option( 'smntcs_admin_maintenance_enable' ) ) {
		if ( isset( $user->ID ) && get_option( 'smntcs_admin_maintenance_uid' ) !== (int) $user->ID ) {
			$user = new WP_Error( 'admin-maintenance', __( 'The <strong>Administration Screens</strong> are currently in maintenance mode. Please try again later.', 'smntcs-admin-maintenance' ) );
		}
	}

	return $user;
}
add_action( 'authenticate', 'smntcs_admin_maintenance_enqueue', 20, 3 );
