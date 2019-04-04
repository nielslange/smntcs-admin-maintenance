<?php
/**
 * Template to display the blog category pages.
 *
 * Plugin Name: SMNTCS Admin Maintenance
 * Plugin URI: https://github.com/nielslange/smntcs-admin-maintenance
 * Description: Enables admins to put the <a href="https://codex.wordpress.org/Administration_Screens" target="_blank">Administration Screens</a> into maintenance mode
 * Author: Niels Lange
 * Author URI: https://nielslange.com
 * Text Domain: smntcs-wapuu-widget
 * Domain Path: /languages/
 * Version: 1.4
 * Requires at least: 3.4
 * Requires PHP: 5.6
 * Tested up to: 5.1
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @category   Plugin
 * @package    WordPress
 * @subpackage SMNTCS Admin Maintenance
 * @author     Niels Lange <info@nielslange.de>
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
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
	if ( get_option( 'smntcs_admin_maintenance_enable' ) ) {
		if ( isset( $user->ID ) && get_option( 'smntcs_admin_maintenance_uid' ) !== $user->ID ) {
			$user = new WP_Error( 'admin-maintenance', __( 'The <strong>Administration Screens</strong> are currently in maintenance mode. Please try again later.', 'smntcs-admin-maintenance' ) );
		}
	}

	return $user;
}
