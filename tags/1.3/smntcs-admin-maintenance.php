<?php
/**
 * Plugin Name: SMNTCS Admin Maintenance
 * Plugin URI: https://github.com/nielslange/smntcs-admin-maintenance
 * Description: Enables admins to put the <a href="https://codex.wordpress.org/Administration_Screens" target="_blank">Administration Screens</a> into maintenance mode
 * Author: Niels Lange
 * Author URI: https://nielslange.com
 * Text Domain: smntcs-admin-maintenance
 * Domain Path: /languages/
 * Version: 1.3
 * Requires at least: 3.4
 * Tested up to: 5.1
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package WordPress
 * @subpackage SMNTCS Admin Maintenance
 * @author Niels Lange <info@nielslange.de>
 */

/**
 * Avoid direct plugin access
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	die( '¯\_(ツ)_/¯' );
}

/**
 * Load text domain
 *
 * @since 1.0.0
 */
function smntcs_admin_maintenance_load_textdomain() {
	load_plugin_textdomain( 'smntcs-admin-maintenance', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'smntcs_admin_maintenance_load_textdomain' );

/**
 * Enhance customizer
 *
 * @since 1.0.0
 * @param object $wp_customize The customizer object.
 * @return void
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
			'default' => false,
			'type'    => 'option',
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
			'default' => '',
			'type'    => 'option',
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
 * Add settings link on plugin page
 *
 * @since 1.0.0
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
 * @since 1.0.0
 * @param object $user The original WP_User() or WP_Error() object.
 * @param string $username The user's username.
 * @param string $password The user's password.
 * @return object $user The updated WP_User() or WP_Error() object.
 */
function smntcs_admin_maintenance_enqueue( $user, $username, $password ) {
	if ( get_option( 'smntcs_admin_maintenance_enable' ) ) {
		if ( isset( $user->ID ) && get_option( 'smntcs_admin_maintenance_uid' ) !== $user->ID ) {
			$user = new WP_Error( 'admin-maintenance', __( 'The <strong>Administration Screens</strong> are currently in maintenance mode. Please try again later.', 'smntcs-admin-maintenance' ) );
		}
	}

	return $user;
}
add_action( 'authenticate', 'smntcs_admin_maintenance_enqueue', 20, 3 );
