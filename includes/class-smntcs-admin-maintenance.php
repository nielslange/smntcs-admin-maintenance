<?php
/**
 * Customizer class
 *
 * @package SMNTCS_Admin_Maintenance
 */

declare( strict_types=1 );
defined( 'ABSPATH' ) || exit;

/**
 * Class SMNTCS_Admin_Maintenance
 */
class SMNTCS_Admin_Maintenance {

	/**
	 * SMNTCS_Admin_Maintenance constructor.
	 */
	public function __construct() {
		add_action( 'customize_register', [ $this, 'register_customize' ] );
		add_filter( 'option_smntcs_admin_maintenance_uid', [ $this, 'force_int' ] );
		add_filter( 'option_smntcs_admin_maintenance_enable', [ $this, 'force_bool' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'settings_link' ] );
		add_filter( 'authenticate', [ $this, 'enqueue' ], 30, 1 );
	}

	/**
	 * Enhance customizer
	 *
	 * @param WP_Customize_Manager $wp_customize The instance of the WP_Customize_Manager class.
	 */
	public function register_customize( $wp_customize ) {
		$users = get_users( [ 'role' => 'administrator' ] );
		foreach ( $users as $user ) {
			$choices[ $user->ID ] = $user->user_nicename;
		}

		$wp_customize->add_section(
			'smntcs_admin_maintenance_section',
			[
				'priority' => 500,
				'title'    => __( 'Admin Maintenance', 'smntcs-admin-maintenance' ),
			]
		);

		$wp_customize->add_setting(
			'smntcs_admin_maintenance_enable',
			[
				'default'           => '',
				'type'              => 'option',
				'sanitize_callback' => [ $this, 'sanitize_checkbox' ],
			]
		);

		$wp_customize->add_control(
			'smntcs_admin_maintenance_enable',
			[
				'label'   => __( 'Enable Admin Maintenance', 'smntcs-admin-maintenance' ),
				'section' => 'smntcs_admin_maintenance_section',
				'type'    => 'checkbox',
			]
		);

		$wp_customize->add_setting(
			'smntcs_admin_maintenance_uid',
			[
				'default'           => '',
				'type'              => 'option',
				'sanitize_callback' => [ $this, 'sanitize_integer' ],
			]
		);

		$wp_customize->add_control(
			'smntcs_admin_maintenance_uid',
			[
				'label'   => __( 'Grant access to', 'smntcs-admin-maintenance' ),
				'section' => 'smntcs_admin_maintenance_section',
				'type'    => 'select',
				'choices' => $choices,
			]
		);
	}

	/**
	 * Sanitize customizer integer input
	 *
	 * @param int $input The number to sanitize.
	 * @return int|object The sanitized number or the WP_Error() object.
	 */
	public function sanitize_integer( $input ) {
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
	 * @return bool The sanitized boolean.
	 */
	public function sanitize_checkbox( bool $input ): bool {
		return (bool) $input;
	}

	/**
	 * Convert numeric character to integer
	 *
	 * @param mixed $value The mixed input.
	 * @return int The numeric output.
	 */
	public function force_int( $value ) {
		return is_numeric( $value ) ? (int) $value : 0;
	}

	/**
	 * Convert numeric character to boolean
	 *
	 * @param mixed $value The mixed input.
	 * @return bool The boolean output.
	 */
	public function force_bool( $value ) {
		return is_numeric( $value ) ? (bool) $value : false;
	}

	/**
	 * Add settings link on plugin page
	 *
	 * @param array $links The original array with customizer links.
	 * @return array $links The updated array with customizer links.
	 */
	public function settings_link( $links ) {
		$admin_url     = admin_url( 'customize.php?autofocus[control]=smntcs_admin_maintenance_enable' );
		$settings_link = sprintf( '<a href="%s">%s</a>', $admin_url, __( 'Settings', 'smntcs-admin-maintenance' ) );
		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Handle authentication
	 *
	 * @param WP_User|WP_Error|null $user The original WP_User() or WP_Error() object.
	 * @return WP_User|WP_Error|null The updated WP_User() or WP_Error() object.
	 */
	public function enqueue( $user ) {
		$maintenance_enabled = get_option( 'smntcs_admin_maintenance_enable' );
		$allowed_user_id     = get_option( 'smntcs_admin_maintenance_uid' );

		if ( $maintenance_enabled && isset( $user->ID ) && $user->ID !== $allowed_user_id ) {
			return new WP_Error( 'admin-maintenance', __( 'The <strong>Administration Screens</strong> are currently in maintenance mode. Please try again later.', 'smntcs-admin-maintenance' ) );
		}

		return $user;
	}
}
