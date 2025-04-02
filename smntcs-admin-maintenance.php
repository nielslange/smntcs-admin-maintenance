<?php
/**
 * Plugin Name:         SMNTCS Admin Maintenance
 * Plugin URI:          https://github.com/nielslange/smntcs-admin-maintenance
 * Description:         Enables admins to put the <a href="https://codex.wordpress.org/Administration_Screens" target="_blank">Administration Screens</a> into maintenance mode.
 * Author:              Niels Lange
 * Author URI:          https://nielslange.de
 * Text Domain:         smntcs-admin-maintenance
 * Version:             2.4
 * Requires PHP:        7.4
 * Requires at least:   3.4
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package SMNTCS_Admin_Maintenance
 */

// Declare strict types.
declare( strict_types=1 );

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define constants.
define( 'SMNTCS_ADMIN_MAINTENANCE_PLUGIN_FILE', __FILE__ );

// Load the main class.
require_once __DIR__ . '/includes/class-smntcs-admin-maintenance.php';

// Initialize the plugin.
new SMNTCS_Admin_Maintenance();
