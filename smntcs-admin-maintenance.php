<?php
/*
Plugin Name: SMNTCS Admin Maintenance
Plugin URI: https://github.com/nielslange/smntcs-admin-maintenance
Description: Enables admins to put the <a href="https://codex.wordpress.org/Administration_Screens" target="_blank">Administration Screens</a> into maintenance mode
Author: Niels Lange
Author URI: https://nielslange.com
Text Domain: smntcs-admin-maintenance
Domain Path: /languages/
Version: 1.0
*/

/*  Copyright 2014-2017	Niels Lange (email : info@nielslange.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//* Avoid direct plugin access
if ( ! defined( 'ABSPATH' ) ) die('¯\_(⊙︿⊙)_/¯');

//* Load text domain
add_action('plugins_loaded', 'smntcs_admin_maintenance_load_textdomain');
function smntcs_admin_maintenance_load_textdomain() {
	load_plugin_textdomain( 'smntcs-admin-maintenance', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}

//* Enhance customizer
add_action( 'customize_register', 'smntcs_admin_maintenance_register_customize' );
function smntcs_admin_maintenance_register_customize( $wp_customize ) {
	$users = get_users( array( 'roles' => 'administrator' ) );
	foreach ($users as $user) {
		$choices[$user->ID] = $user->user_nicename;
	}

	$wp_customize->add_section( 'smntcs_admin_maintenance_section', array(
		'priority' 	=> 500,
		'title' 	=> __( 'Admin Maintenance ', 'smntcs-admin-maintenance' ),
	));

	$wp_customize->add_setting( 'smntcs_admin_maintenance_enable', array(
		'default' 	=> false,
		'type'		=> 'option',
	));

	$wp_customize->add_control( 'smntcs_admin_maintenance_enable', array(
		'label' 	=> __( 'Enable Admin Maintenance', 'smntcs-admin-maintenance' ),
		'section' 	=> 'smntcs_admin_maintenance_section',
		'type' 		=> 'checkbox',
	));

	$wp_customize->add_setting( 'smntcs_admin_maintenance_uid', array(
		'default' 	=> '',
		'type' 		=> 'option' 
	));
	
	$wp_customize->add_control( 'smntcs_admin_maintenance_uid', array(
		'label' 	=> __('Grant access to', 'smntcs-admin-maintenance'),
		'section' 	=> 'smntcs_admin_maintenance_section', 
		'type' 		=> 'select', 
		'choices' 	=> $choices,
	));
}

//* Add settings link on plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'smntcs_admin_maintenance_settings_link' );
function smntcs_admin_maintenance_settings_link($links) {
	$admin_url 		= admin_url( 'customize.php?autofocus[control]=smntcs_admin_maintenance_enable' );
	$settings_link 	= sprintf( '<a href="%s">%s</a>', $admin_url, __( 'Settings', 'smntcs-admin-maintenance' ) );
	array_unshift( $links, $settings_link );
	
	return $links;
}

//* Handle authentication
add_action( 'authenticate', 'smntcs_admin_maintenance_enqueue', 20, 3 );
function smntcs_admin_maintenance_enqueue( $user, $username, $password ) {
	if ( get_option( 'smntcs_admin_maintenance_enable' ) ) {
		if ( isset($user->ID) && $user->ID != get_option( 'smntcs_admin_maintenance_uid' ) ) {
			$user = new WP_Error( 'admin-maintenance', __( 'The <strong>Administration Screens</strong> are currently in maintenance mode. Please try again later.', 'smntcs-admin-maintenance' ) );
		} 
	}

	return $user;
}