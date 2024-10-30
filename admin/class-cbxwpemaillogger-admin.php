<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXWPEmailLogger
 * @subpackage CBXWPEmailLogger/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    CBXWPEmailLogger
 * @subpackage CBXWPEmailLogger/admin
 * @author     Codeboxr <info@codeboxr.com>
 */
class CBXWPEmailLogger_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * for setting
	 * @since    1.0.0
	 * @access   private
	 * @var      string $settings_api The current version of this plugin.
	 * */
	private $settings_api;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $test_error_msg The current version of this plugin.
	 */
	private $test_error_msg;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param  string  $plugin_name  The name of this plugin.
	 * @param  string  $version  The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		//get instance of setting api
		$this->settings_api   = new CBXWPEmailLoggerSettings();
		$this->test_error_msg = '';
	}//end of constructor method


	/**
	 * Show action links on the plugin screen.
	 *
	 * @param  mixed  $links  Plugin Action links.
	 *
	 * @return  array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = [
			'settings' => '<a style="color: #6648fe !important; font-weight: bold;" href="' . admin_url( 'admin.php?page=cbxwpemailloggersettings' ) . '" aria-label="' . esc_attr__( 'View settings', 'cbxwpemaillogger' ) . '">' . esc_html__( 'Settings', 'cbxwpemaillogger' ) . '</a>',
		];

		return array_merge( $action_links, $links );
	}//end plugin_action_links

	/**
	 * Filters the array of row meta for each/specific plugin in the Plugins list table.
	 * Appends additional links below each/specific plugin on the plugins page.
	 *
	 * @access  public
	 *
	 * @param  array  $links_array  An array of the plugin's metadata
	 * @param  string  $plugin_file_name  Path to the plugin file
	 * @param  array  $plugin_data  An array of plugin data
	 * @param  string  $status  Status of the plugin
	 *
	 * @return  array       $links_array
	 */
	public function plugin_row_meta( $links_array, $plugin_file_name, $plugin_data, $status ) {
		if ( strpos( $plugin_file_name, CBXWPEMAILLOGGER_BASE_NAME ) !== false ) {
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			$links_array[] = '<a target="_blank" style="color:#6044ea !important; font-weight: bold;" href="https://wordpress.org/support/plugin/cbxwpemaillogger/" aria-label="' . esc_attr__( 'Free Support', 'cbxwpemaillogger' ) . '">' . esc_html__( 'Free Support', 'cbxwpemaillogger' ) . '</a>';

			$links_array[] = '<a target="_blank" style="color:#6044ea !important; font-weight: bold;" href="https://wordpress.org/plugins/cbxwpemaillogger/#reviews" aria-label="' . esc_attr__( 'Reviews', 'cbxwpemaillogger' ) . '">' . esc_html__( 'Reviews', 'cbxwpemaillogger' ) . '</a>';


			if ( in_array( 'cbxwpemailloggerproaddon/cbxwpemailloggerproaddon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || defined( 'CBXWPEMAILLOGGERPROADDON_PLUGIN_NAME' ) ) {
				$links_array[] = '<a target="_blank" style="color:#6044ea !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-email-logger-for-wordpress/" aria-label="' . esc_attr__( 'Documentation', 'cbxwpemaillogger' ) . '">' . esc_html__( 'Documentation', 'cbxwpemaillogger' ) . '</a>';
			} else {
				$links_array[] = '<a target="_blank" style="color:#6044ea !important; font-weight: bold;" href="https://codeboxr.com/product/cbx-email-logger-for-wordpress/" aria-label="' . esc_attr__( 'Try Pro Addon', 'cbxwpemaillogger' ) . '">' . esc_html__( 'Try Pro Addon', 'cbxwpemaillogger' ) . '</a>';
			}


		}

		return $links_array;
	}//end plugin_row_meta

	/**
	 * Initialize setting
	 */
	public function admin_init() {
		//set the settings
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );
		//initialize settings
		$this->settings_api->admin_init();
	}//end admin_init

	/**
	 * Global Setting Sections and titles
	 *
	 * @return type
	 */
	public function get_settings_sections() {
		return apply_filters( 'cbxwpemaillogger_setting_sections',
			[
				[
					'id'    => 'cbxwpemaillogger_log',
					'title' => esc_html__( 'Email Log', 'cbxwpemaillogger' ),
				],
				[
					'id'    => 'cbxwpemaillogger_email',
					'title' => esc_html__( 'Email Control', 'cbxwpemaillogger' ),
				],
				[
					'id'    => 'cbxwpemaillogger_smtps',
					'title' => esc_html__( 'Email Sending', 'cbxwpemaillogger' ),
				],
				[
					'id'    => 'cbxwpemaillogger_tools',
					'title' => esc_html__( 'Tools', 'cbxwpemaillogger' ),
				],
			] );
	}//end get_settings_sections

	/**
	 * Global Setting Fields
	 *
	 * @return array
	 */
	public function get_settings_fields() {
		global $wpdb;

		$reset_data_link = add_query_arg( 'cbxwpemaillogger_fullreset', 1, admin_url( 'admin.php?page=cbxwpemailloggersettings' ) );

		$table_data_html = $table_cache_html = '';


		$table_data_html .= '<p id="cbxwpemaillogger_plg_gfig_info"><strong>' . esc_html__( 'Following option values created by this plugin(including addon)', 'cbxwpemaillogger' ) . '</strong></p>';

		$option_values   = CBXWPEmailLoggerHelper::getAllOptionNames();
		$table_data_html .= '<table class="widefat" id="cbxwpemaillogger_table_data">
	<thead>
	<tr>
		<th class="row-title">' . esc_attr__( 'Option Name', 'cbxwpemaillogger' ) . '</th>
		<th>' . esc_attr__( 'Option ID', 'cbxwpemaillogger' ) . '</th>		
	</tr>
	</thead>
	<tbody>';


		$i = 0;
		foreach ( $option_values as $key => $value ) {

			//$table_data_html .= '<p>' .  $value['option_name'] . ' - ' . $value['option_id'] . ' - (<code style="overflow-wrap: break-word; word-break: break-all;">' . $value['option_value'] . '</code>)</p>';

			$alternate_class = ( $i % 2 == 0 ) ? 'alternate' : '';
			$i ++;
			$table_data_html .= '<tr class="' . esc_attr( $alternate_class ) . '">
									<td class="row-title"><label for="tablecell">' . esc_attr( $value['option_name'] ) . '</label></td>
									<td>' . esc_attr( $value['option_id'] ) . '</td>									
								</tr>';

		}

		$table_data_html .= '</tbody>
	<tfoot>
	<tr>
		<th class="row-title">' . esc_attr__( 'Option Name', 'cbxwpemaillogger' ) . '</th>
		<th>' . esc_attr__( 'Option ID', 'cbxwpemaillogger' ) . '</th>		
	</tr>
	</tfoot>
</table>';



		$custom_mailer = CBXWPEmailLoggerHelper::getCustomMailer();


		//$smtp_email_servers_default = CBXWPEmailLoggerHelper::smtp_email_servers_default(  );
		$smtp_email_servers_list = CBXWPEmailLoggerHelper::getSMTPHostServers( true );


		$settings_builtin_fields = [
			'cbxwpemaillogger_log'   => [
				'email_log_enable'        => [
					'name'    => 'email_log_enable',
					'label'   => esc_html__( 'Email Log Control', 'cbxwpemaillogger' ),
					'desc'    => '<p>' . esc_html__( 'Control Email logging, default is enabled on after plugin activated.', 'cbxwpemaillogger' ) . '</p>',
					'type'    => 'radio',
					'options' => [
						1 => esc_html__( 'Enable', 'cbxwpemaillogger' ),
						0 => esc_html__( 'Disable', 'cbxwpemaillogger' ),
					],
					'default' => 1,
				],
				'delete_old_log'          => [
					'name'              => 'delete_old_log',
					'label'             => esc_html__( 'Delete Old email logs', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'If enabled it will check everyday if there is any x days old emails. Number of days(x) is configured in next field. This plugin needs to deactivate and activate again to make this feature work.', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'radio',
					'options'           => [
						'yes' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
						'no'  => esc_html__( 'No', 'cbxwpemaillogger' ),
					],
					'default'           => 'no',
					'sanitize_callback' => 'esc_html',
				],
				'log_old_days'            => [
					'name'              => 'log_old_days',
					'label'             => esc_html__( 'Number of days', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'Number of days email will be deleted as old based on email send date', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'text',
					'default'           => '30',
					'sanitize_callback' => 'absint',
				],
				'enable_store_attachment' => [
					'name'    => 'enable_store_attachment',
					'label'   => esc_html__( 'Save Attachment Files', 'cbxwpemaillogger' ),
					'desc'    => '<p>' . esc_html__( 'If enabled attachments will be stored. If log deleted attachments will be deleted from the stored location. Sometimes attachment are sent from dynamically generated contents which is deleted from memory after email is sent, if not stored separately then email resend feature will not be able to attach email. This feature is default disabled.', 'cbxwpemaillogger' ) . '</p>',
					'type'    => 'radio',
					'options' => [
						1 => esc_html__( 'Enable', 'cbxwpemaillogger' ),
						0 => esc_html__( 'Disable', 'cbxwpemaillogger' ),
					],
					'default' => 0,
				],
			],
			'cbxwpemaillogger_email' => [
				'email_smtp_enable'     => [
					'name'    => 'email_smtp_enable',
					'label'   => esc_html__( 'Control Email Sending', 'cbxwpemaillogger' ),
					'desc'    => '<p>' . __( 'Control email sending, default is disabled on after plugin activated. <strong>If disabled, this plugin will not touch any email sending feature.</strong>', 'cbxwpemaillogger' ) . '</p>',
					'type'    => 'radio',
					'options' => [
						1 => esc_html__( 'Enable', 'cbxwpemaillogger' ),
						0 => esc_html__( 'Disable', 'cbxwpemaillogger' ),
					],
					'default' => 0,
				],
				'smtp_from_email'       => [
					'name'              => 'smtp_from_email',
					'label'             => esc_html__( 'Override From Email', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'Leave blank/empty to use default', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'text',
					'default'           => sanitize_email( get_option( 'admin_email' ) ),
					'sanitize_callback' => 'sanitize_email',
				],
				'smtp_from_name'        => [
					'name'              => 'smtp_from_name',
					'label'             => esc_html__( 'Override From Name', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'Leave blank/empty to use default', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'text',
					'default'           => esc_html( get_option( 'blogname' ) ),
					'sanitize_callback' => 'sanitize_text_field',
				],
				'smtp_email_returnpath' => [
					'name'              => 'smtp_email_returnpath',
					'label'             => esc_html__( 'Email Return path', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'If blank will ignore', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_email',
				],
				'mailer'                => [
					'name'    => 'mailer',
					'label'   => esc_html__( 'Emailer', 'cbxwpemaillogger' ),
					'desc'    => '<p>' . esc_html__( 'Default is wordpress default', 'cbxwpemaillogger' ) . '</p>',
					'type'    => 'select',
					'default' => 'default',
					'options' => [
						'default' => esc_html__( 'WordPress Default', 'cbxwpemaillogger' ),
						'custom'  => esc_html__( 'Custom Mailer(Choose from Email Sending Tab)', 'cbxwpemaillogger' ),
					],
				],

			],
			'cbxwpemaillogger_smtps' => [
				'custom_mailer'      => [
					'name'    => 'custom_mailer',
					'label'   => esc_html__( 'Choose Custom Mailer', 'cbxwpemaillogger' ),
					'type'    => 'select',
					'default' => 'custom_smtp',
					'options' => $custom_mailer
				],
				'smtp_email_servers' => [
					'name'      => 'smtp_email_servers',
					'label'     => esc_html__( 'SMTP Host Servers', 'cbxwpemaillogger' ),
					'type'      => 'repeat',
					'allow_new' => apply_filters( 'cbxwpemaillogger_smtp_email_servers_allow_new', 0 ),
					'default'   => [
						'0' => [
							'smtp_email_enable'   => 1,
							'smtp_email_host'     => 'localhost',
							'smtp_email_port'     => '25',
							'smtp_email_secure'   => 'none',
							'smtp_email_auth'     => 0,
							'smtp_email_username' => '',
							'smtp_email_password' => '',
						]
					],
					'fields'    => [
						'smtp_email_enable'   => [
							'name'    => 'smtp_email_enable',
							'label'   => esc_html__( 'Enable Service', 'cbxwpemaillogger' ),
							'type'    => 'radio',
							'default' => 0,
							'options' => [
								'1' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
								'0' => esc_html__( 'No', 'cbxwpemaillogger' )
							],
						],
						'smtp_email_host'     => [
							'name'    => 'smtp_email_host',
							'label'   => esc_html__( 'SMTP Host', 'cbxwpemaillogger' ),
							'type'    => 'text',
							'default' => 'localhost',
						],
						'smtp_email_port'     => [
							'name'              => 'smtp_email_port',
							'label'             => esc_html__( 'SMTP Port', 'cbxwpemaillogger' ),
							'type'              => 'text',
							'default'           => '25',
							'sanitize_callback' => 'absint',
						],
						'smtp_email_secure'   => [
							'name'    => 'smtp_email_secure',
							'label'   => esc_html__( 'SMTP Secure', 'cbxwpemaillogger' ),
							'type'    => 'select',
							'default' => 'none',
							'options' => [
								'none' => esc_html__( 'None(Port: 25)', 'cbxwpemaillogger' ),
								'ssl'  => esc_html__( 'SSL(Port: 465)', 'cbxwpemaillogger' ),
								'tls'  => esc_html__( 'TLS(Port: 465)', 'cbxwpemaillogger' ),
							],
						],
						'smtp_email_auth'     => [
							'name'    => 'smtp_email_auth',
							'label'   => esc_html__( 'SMTP Authentication', 'cbxwpemaillogger' ),
							'type'    => 'radio',
							'default' => 0,
							'options' => [
								0 => esc_html__( 'No', 'cbxwpemaillogger' ),
								1 => esc_html__( 'Yes', 'cbxwpemaillogger' ),
							],
						],
						'smtp_email_username' => [
							'name'              => 'smtp_email_username',
							'label'             => esc_html__( 'SMTP Username', 'cbxwpemaillogger' ),
							'type'              => 'text',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						],
						'smtp_email_password' => [
							'name'              => 'smtp_email_password',
							'label'             => esc_html__( 'SMTP Password', 'cbxwpemaillogger' ),
							'type'              => 'password',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						],

					]
				],
				'smtp_email_server'  => [
					'name'    => 'smtp_email_server',
					'label'   => esc_html__( 'Choose SMTP Server', 'cbxwpemaillogger' ),
					'desc'    => esc_html__( 'List is showing only enabled servers', 'cbxwpemaillogger' ),
					'type'    => 'select',
					'default' => - 1,
					'options' => $smtp_email_servers_list
				],
				/*'smtp_email_host'     => array(
					'name'    => 'smtp_email_host',
					'label'   => esc_html__( 'SMTP Host', 'cbxwpemaillogger' ),
					'type'    => 'text',
					'default' => 'localhost',
				),
				'smtp_email_port'     => array(
					'name'              => 'smtp_email_port',
					'label'             => esc_html__( 'SMTP Port', 'cbxwpemaillogger' ),
					'type'              => 'text',
					'default'           => '25',
					'sanitize_callback' => 'absint',
				),
				'smtp_email_secure'   => array(
					'name'    => 'smtp_email_secure',
					'label'   => esc_html__( 'SMTP Secure', 'cbxwpemaillogger' ),
					'type'    => 'select',
					'default' => 'none',
					'options' => array(
						'none' => esc_html__( 'None(Port: 25)', 'cbxwpemaillogger' ),
						'ssl'  => esc_html__( 'SSL(Port: 465)', 'cbxwpemaillogger' ),
						'tls'  => esc_html__( 'TLS(Port: 465)', 'cbxwpemaillogger' ),
					),
				),
				'smtp_email_auth'     => array(
					'name'    => 'smtp_email_auth',
					'label'   => esc_html__( 'SMTP Authentication', 'cbxwpemaillogger' ),
					'type'    => 'radio',
					'default' => 0,
					'options' => array(
						0 => esc_html__( 'No', 'cbxwpemaillogger' ),
						1 => esc_html__( 'Yes', 'cbxwpemaillogger' ),
					),
				),
				'smtp_email_username' => array(
					'name'              => 'smtp_email_username',
					'label'             => esc_html__( 'SMTP Username', 'cbxwpemaillogger' ),
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
				'smtp_email_password' => array(
					'name'              => 'smtp_email_password',
					'label'             => esc_html__( 'SMTP Password', 'cbxwpemaillogger' ),
					'type'              => 'password',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),*/
			],
			'cbxwpemaillogger_tools' => [
				'delete_global_config' => [
					'name'              => 'delete_global_config',
					'label'             => esc_html__( 'On Uninstall delete plugin data', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'Delete Global Config data created by this plugin on uninstall.',
							'cbxwpemaillogger' ) . ' ' . __( 'Details <a data-target="cbxwpemaillogger_resetinfo" class="cbxwpemaillogger_jump" href="#">option values</a>',
							'cbxwpemaillogger' ) . '</p>' . '<p>' . __( '<strong>Please note that this process can not be undone and it is recommended to keep full database backup before doing this.</strong>',
							'cbxwpemaillogger' ) . '</p>',
					'type'              => 'radio',
					'options'           => [
						'yes' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
						'no'  => esc_html__( 'No', 'cbxwpemaillogger' ),
					],
					'default'           => 'no',
					'sanitize_callback' => 'esc_html',
				],
				'reset_data'           => [
					'name'    => 'reset_data',
					'label'   => esc_html__( 'Reset all data', 'cbxwpemaillogger' ),
					'desc'    => sprintf( __( 'Reset option values created by this plugin. 
<a class="button button-primary" onclick="return confirm(\'%s\')" href="%s">Reset Data</a>',
							'cbxwpemaillogger' ),
							esc_html__( 'Are you sure to reset all data, this process can not be undone?',
								'cbxwpemaillogger' ),
							$reset_data_link ) . $table_data_html,
					'type'    => 'html',
					'default' => 'off',
				],
			],
		];

		$settings_fields = []; //final setting array that will be passed to different filters

		$sections = $this->get_settings_sections();


		foreach ( $sections as $section ) {
			if ( ! isset( $settings_builtin_fields[ $section['id'] ] ) ) {
				$settings_builtin_fields[ $section['id'] ] = [];
			}
		}

		foreach ( $sections as $section ) {
			$settings_fields[ $section['id'] ] = apply_filters( 'cbxwpemaillogger_global_' . esc_attr( $section['id'] ) . '_fields',
				$settings_builtin_fields[ $section['id'] ] );
		}

		$settings_fields = apply_filters( 'cbxwpemaillogger_global_fields', $settings_fields ); //final filter if need

		return $settings_fields;
	}//end get_settings_fields

	/**
	 * Create admin menu's
	 */
	public function admin_pages() {

		$page = isset( $_GET['page'] ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : '';

		//review listing page
		$email_logger_menu_hook = add_menu_page( esc_html__( 'CBX SMTP and Email Logger Dashboard', 'cbxwpemaillogger' ),
			esc_html__( 'Email SMTP', 'cbxwpemaillogger' ),
			'manage_options',
			'cbxwpemaillogger',
			[ $this, 'display_cbxwpemaillogger_listing_page' ],
			CBXWPEMAILLOGGER_ROOT_URL . 'assets/images/icon_20.png',
			'6' );

		//add screen option save option
		if ( $page == 'cbxwpemaillogger' ) {
			add_action( "load-$email_logger_menu_hook", [ $this, 'cbxwpemaillogger_listing_perpage' ] );
		}

		//add settings for this plugin
		$setting_page_hook = add_submenu_page( 'cbxwpemaillogger',
			esc_html__( 'Global Setting', 'cbxwpemaillogger' ),
			esc_html__( 'Global Setting', 'cbxwpemaillogger' ),
			'manage_options',
			'cbxwpemailloggersettings',
			[ $this, 'display_plugin_admin_settings' ] );

		//add settings for this plugin
		$testmail_page_hook    = add_submenu_page( 'cbxwpemaillogger',
			esc_html__( 'Email Testing', 'cbxwpemaillogger' ),
			esc_html__( 'Email Testing', 'cbxwpemaillogger' ),
			'manage_options',
			'cbxwpemailloggeremailtesting',
			[ $this, 'display_plugin_admin_email_testing' ] );
		$helpsupport_page_hook = add_submenu_page( 'cbxwpemaillogger',
			esc_html__( 'Helps & Updates', 'cbxwpemaillogger' ),
			esc_html__( 'Helps & Updates', 'cbxwpemaillogger' ),
			'manage_options',
			'cbxwpemaillogger-help-support',
			[ $this, 'cbxwpemaillogger_helps_updates_display' ] );

		global $submenu;
		if ( isset( $submenu['cbxwpemaillogger'][0][0] ) ) {
			$submenu['cbxwpemaillogger'][0][0] = esc_html__( 'Email Logs', 'cbxwpemaillogger' );
		}


	}//end admin_pages

	/**
	 * Admin listing menu callback
	 */
	public function display_cbxwpemaillogger_listing_page() {
		$view = isset( $_REQUEST['view'] ) ? esc_attr( wp_unslash( $_REQUEST['view'] ) ) : 'list';


		if ( $view == 'list' ) {
			include( cbxwpemaillogger_locate_template( 'admin/cbxwpemaillogger-logs.php' ) );
		} /*else if($view == 'body'){
				$log_id = isset($_REQUEST['log_id'])? intval($_REQUEST['log_id']) : 0;
				$item = CBXWPEmailLoggerHelper::SingleLog($log_id);
				include( cbxwpemaillogger_locate_template('admin/cbxwpemaillogger-body.php') );
			}*/
		else {
			$log_id = isset( $_REQUEST['log_id'] ) ? intval( $_REQUEST['log_id'] ) : 0;

			$item = CBXWPEmailLoggerHelper::SingleLog( $log_id );

			include( cbxwpemaillogger_locate_template( 'admin/cbxwpemaillogger-log.php' ) );
		}

	}//end display_cbxwpemaillogger_listing_page

	/**
	 * Set options for log listing result
	 *
	 * @param $new_status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 */
	public function cbxscratingreview_listing_per_page( $new_status, $option, $value ) {
		if ( 'cbxwpemaillogger_listing_per_page' == $option ) {
			return $value;
		}

		return $new_status;
	}//end cbxscratingreview_listing_per_page

	/**
	 * Display settings page
	 *
	 * @global type $wpdb
	 */
	public function display_plugin_admin_settings() {
		global $wpdb;

		include( cbxwpemaillogger_locate_template( 'admin/settings-display.php' ) );
	}//end display_plugin_admin_settings

	/**
	 * Display email testing page
	 */
	public function display_plugin_admin_email_testing() {
		include( cbxwpemaillogger_locate_template( 'admin/emailtesting-display.php' ) );
	}//end display_plugin_admin_email_testing

	/**
	 * Render the help & support page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function cbxwpemaillogger_helps_updates_display() {
		include( cbxwpemaillogger_locate_template( 'admin/dashboard.php' ) );
	}//end method cbxwpemaillogger_helps_updates_display

	/**
	 * Add screen option for log listing
	 */
	public function cbxwpemaillogger_listing_perpage() {
		$option = 'per_page';

		$args = [
			'label'   => esc_html__( 'Number of items per page', 'cbxwpemaillogger' ),
			'default' => 50,
			'option'  => 'cbxwpemaillogger_listing_per_page',
		];

		add_screen_option( $option, $args );
	}//end cbxwpemaillogger_listing_perpage

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		$page = isset( $_GET['page'] ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : '';

		wp_register_style( 'ply', plugin_dir_url( __FILE__ ) . '../assets/js/ply/ply.css', [], $this->version, 'all' );

		/*
		wp_register_style( 'flatpickr-min',
			plugin_dir_url( __FILE__ ) . '../assets/js/flatpickr/flatpickr.min.css',
			array(),
			$this->version );*/

		wp_register_style( 'daterangepicker', plugin_dir_url( __FILE__ ) . '../assets/js/daterangepicker/daterangepicker.css',
			[],
			$this->version );

		if ( $page == 'cbxwpemaillogger' || $page == 'cbxwpemailloggeremailtesting' ) {

			wp_register_style( 'cbxwpemaillogger', plugin_dir_url( __FILE__ ) . '../assets/css/cbxwpemaillogger-admin.css', [ 'ply', 'daterangepicker' ], $this->version, 'all' );
			wp_enqueue_style( 'ply' );
			wp_enqueue_style( 'daterangepicker' );
			wp_enqueue_style( 'cbxwpemaillogger' );
		}

		if ( $page == 'cbxwpemailloggersettings' ) {
			wp_register_style( 'hideshowpassword', plugin_dir_url( __FILE__ ) . '../assets/js/hideshowpassword/example.wink.css', [], $this->version );
			wp_register_style( 'select2', plugin_dir_url( __FILE__ ) . '../assets/js/select2/css/select2.min.css', [], $this->version );
			wp_register_style( 'cbxwpemaillogger-setting',
				plugin_dir_url( __FILE__ ) . '../assets/css/cbxwpemaillogger-setting.css',
				[ 'select2', 'ply' ],
				$this->version );

			wp_enqueue_style( 'hideshowpassword' );
			wp_enqueue_style( 'ply' );
			wp_enqueue_style( 'select2' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'cbxwpemaillogger-setting' );
		}
		if ( $page == 'cbxwpemaillogger' || $page == 'cbxwpemailloggersettings' || $page == 'cbxwpemailloggeremailtesting' || $page == 'cbxwpemaillogger-help-support' ) {
			wp_register_style( 'cbxwpemaillogger-branding', plugin_dir_url( __FILE__ ) . '../assets/css/cbxwpemaillogger-branding.css',
				[],
				$this->version );
			wp_enqueue_style( 'cbxwpemaillogger-branding' );
		}
	}//end enqueue_styles

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		$page = isset( $_GET['page'] ) ? esc_attr( wp_unslash( $_GET['page'] ) ) : '';

		wp_register_script( 'ply', plugin_dir_url( __FILE__ ) . '../assets/js/ply/ply.min.js', [ 'jquery' ], $this->version, true );
		/*wp_register_script( 'flatpickr',
			plugin_dir_url( __FILE__ ) . '../assets/js/flatpickr/flatpickr.min.js',
			array( 'jquery' ),
			$this->version,
			true );*/

		wp_register_script( 'moment', plugin_dir_url( __FILE__ ) . '../assets/js/daterangepicker/moment.min.js', [], $this->version, true );
		wp_register_script( 'daterangepicker', plugin_dir_url( __FILE__ ) . '../assets/js/daterangepicker/daterangepicker.js',
			[ 'jquery', 'moment' ],
			$this->version,
			true );

		if ( $page == 'cbxwpemaillogger' || $page == 'cbxwpemailloggeremailtesting' ) {

			wp_register_script( 'cbxwpemaillogger', plugin_dir_url( __FILE__ ) . '../assets/js/cbxwpemaillogger-admin.js', [ 'jquery', 'ply', 'moment', 'daterangepicker' ], $this->version, true );

			//adding translation and other variables from php to js for single post edit screen
			$cbxwpemaillogger_js_vars = [
				'daterangepicker'     => [
					'clear'        => esc_html__( 'Clear', 'cbxwpemaillogger' ),
					'apply'        => esc_html__( 'Apply', 'cbxwpemaillogger' ),
					'custom_range' => esc_html__( 'Custom Range', 'cbxwpemaillogger' ),
					'today'        => esc_html__( 'Today', 'cbxwpemaillogger' ),
					'yesterday'    => esc_html__( 'Yesterday', 'cbxwpemaillogger' ),
					'last_7_days'  => esc_html__( 'Last 7 Days', 'cbxwpemaillogger' ),
					'last_30_days' => esc_html__( 'Last 30 Days', 'cbxwpemaillogger' ),
					'this_month'   => esc_html__( 'This Month', 'cbxwpemaillogger' ),
					'last_month'   => esc_html__( 'Last Month', 'cbxwpemaillogger' ),
				],
				'search_placeholder'  => esc_html__( 'Search Term', 'cbxwpemaillogger' ),
				'upload_btn'          => esc_html__( 'Upload', 'cbxwpemaillogger' ),
				'upload_title'        => esc_html__( 'Select Media', 'cbxwpemaillogger' ),
				'delete'              => esc_html__( 'Delete', 'cbxwpemaillogger' ),
				'deleteconfirm'       => esc_html__( 'Are you sure to delete? On successful delete information will be lost forever.', 'cbxwpemaillogger' ),
				'resendconfirm'       => esc_html__( 'Are you sure to resend? This action can not be reversed.', 'cbxwpemaillogger' ),
				'deleteconfirmok'     => esc_html__( 'Sure', 'cbxwpemaillogger' ),
				'deleteconfirmcancel' => esc_html__( 'Oh! No', 'cbxwpemaillogger' ),
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'nonce'               => wp_create_nonce( 'cbxwpemaillogger' ),
			];

			wp_localize_script( 'cbxwpemaillogger', 'cbxwpemaillogger_dashboard', apply_filters( 'cbxwpemaillogger_js_vars', $cbxwpemaillogger_js_vars ) );

			add_thickbox();

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'ply' );
			wp_enqueue_script( 'moment' );
			wp_enqueue_script( 'daterangepicker' );
			//wp_enqueue_script( 'flatpickr' );
			wp_enqueue_script( 'cbxwpemaillogger' );
		}


		if ( $page == 'cbxwpemailloggersettings' ) {
			wp_register_script( 'hideshowpassword', plugin_dir_url( __FILE__ ) . '../assets/js/hideshowpassword/hideShowPassword.min.js', [ 'jquery' ], $this->version, true );
			wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . '../assets/js/select2/js/select2.min.js', [ 'jquery' ], $this->version, true );
			wp_register_script( 'cbxwpemaillogger-setting',
				plugin_dir_url( __FILE__ ) . '../assets/js/cbxwpemaillogger-setting.js',
				[
					'jquery',
					'hideshowpassword',
					'select2',
					'ply',
					'wp-color-picker',
				],
				$this->version,
				true );

			$cbxwpemaillogger_setting_js_vars = apply_filters( 'cbxwpemaillogger_setting_js_vars',
				[
					'please_select'       => esc_html__( 'Please Select', 'cbxwpemaillogger' ),
					'upload_title'        => esc_html__( 'Select Media File', 'cbxwpemaillogger' ),
					'deleteconfirm'       => esc_html__( 'Are you sure to delete? On successful delete information will be lost forever.', 'cbxwpemaillogger' ),
					'deleteconfirmok'     => esc_html__( 'Sure', 'cbxwpemaillogger' ),
					'deleteconfirmcancel' => esc_html__( 'Oh! No', 'cbxwpemaillogger' ),
					'ajaxurl'             => admin_url( 'admin-ajax.php' ),
					'nonce'               => wp_create_nonce( 'cbxwpemaillogger' ),
				] );
			wp_localize_script( 'cbxwpemaillogger-setting', 'cbxwpemaillogger_setting', $cbxwpemaillogger_setting_js_vars );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_media();

			wp_enqueue_script( 'hideshowpassword' );
			wp_enqueue_script( 'ply' );
			wp_enqueue_script( 'select2' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'cbxwpemaillogger-setting' );
		}
		//header scroll
		wp_register_script( 'cbxwpemaillogger-scroll', plugins_url( '../assets/js/cbxwpemaillogger-scroll.js', __FILE__ ), [ 'jquery' ], CBXWPEMAILLOGGER_PLUGIN_VERSION );
		if ( $page == 'cbxwpemaillogger-setting' || $page == 'cbxwpemailloggeremailtesting' || $page == 'cbxwpemaillogger-help-support' ) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'cbxwpemaillogger-scroll' );
		}
	}//end enqueue_scripts


	/**
	 * Insert email log into database
	 */
	public function insert_log( $atts ) {
		//$to, $subject, $message, $headers, $attachments

		$setting          = $this->settings_api;
		$email_log_enable = intval( $setting->get_option( 'email_log_enable', 'cbxwpemaillogger_log', 1 ) );


		if ( $email_log_enable == 0 ) {
			return $atts;
		}


		global $wpdb;
		$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';


		$to = $atts['to'];
		if ( ! is_array( $to ) ) {
			$to = explode( ',', $to );
		}


		$subject = isset( $atts['subject'] ) ? wp_unslash( sanitize_text_field( $atts['subject'] ) ) : '';
		//$body    = isset( $atts['message'] ) ? wp_unslash(sanitize_textarea_field($atts['message'])) : ( isset( $atts['html'] ) ? wp_unslash(sanitize_textarea_field($atts['html'])) : '' );
		$body = isset( $atts['message'] ) ? wp_unslash( $atts['message'] ) : ( isset( $atts['html'] ) ? wp_unslash( $atts['html'] ) : '' );
		//$htm


		$headers     = isset( $atts['headers'] ) ? $atts['headers'] : [];
		$attachments = isset( $atts['attachments'] ) ? $atts['attachments'] : [];

		if ( ! is_array( $attachments ) ) {
			$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
		}


		if ( ! is_array( $headers ) ) {
			// Explode the headers out, so this function can take both
			// string headers and an array of headers.
			$headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		}


		$attachments_store = [];

		if ( is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
			foreach ( $attachments as $attachment ) {
				$file_name           = basename( $attachment );
				$attachments_store[] = $file_name;

			}
		}


		$email_data = [];

		$email_data['atts']        = $atts; //keep the blueprint
		$email_data['body']        = $body;
		$email_data['headers']     = $headers;           //raw header data
		$email_data['attachments'] = $attachments_store; //raw attachment info data

		//parse header information
		$headers_arr = [];
		$cc          = $bcc = $reply_to = [];

		$email_source = '';


		if ( is_array( $headers ) && sizeof( $headers ) > 0 ) {
			foreach ( (array) $headers as $header ) {
				if ( strpos( $header, ':' ) === false ) {
					if ( false !== stripos( $header, 'boundary=' ) ) {
						$parts    = preg_split( '/boundary=/i', trim( $header ) );
						$boundary = trim( str_replace( [ "'", '"' ], '', $parts[1] ) );
					}
					continue;
				}
				// Explode them out
				[ $name, $content ] = explode( ':', trim( $header ), 2 );

				// Cleanup crew
				$name    = trim( $name );
				$content = trim( $content );

				$email_source = apply_filters( 'cbxwpemaillogger_src_tracking', $email_source, $name, $content );

				switch ( strtolower( $name ) ) {
					case 'x-wpcf7-content-type':
						$email_source = 'contact-form-7';

						break;
					// Mainly for legacy -- process a From: header if it's there
					case 'from':
						$bracket_pos = strpos( $content, '<' );
						if ( $bracket_pos !== false ) {
							// Text before the bracketed email is the "From" name.
							if ( $bracket_pos > 0 ) {
								$from_name = substr( $content, 0, $bracket_pos - 1 );
								$from_name = str_replace( '"', '', $from_name );
								$from_name = trim( $from_name );
							}

							$from_email = substr( $content, $bracket_pos + 1 );
							$from_email = str_replace( '>', '', $from_email );
							$from_email = trim( $from_email );

							// Avoid setting an empty $from_email.
						} elseif ( '' !== trim( $content ) ) {
							$from_email = trim( $content );
						}
						break;
					case 'content-type':
						if ( strpos( $content, ';' ) !== false ) {
							[ $type, $charset_content ] = explode( ';', $content );
							$content_type = trim( $type );
							if ( false !== stripos( $charset_content, 'charset=' ) ) {
								$charset = trim( str_replace( [ 'charset=', '"' ], '', $charset_content ) );
							} elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
								$boundary = trim( str_replace( [ 'BOUNDARY=', 'boundary=', '"' ], '', $charset_content ) );
								$charset  = '';
							}

							// Avoid setting an empty $content_type.
						} elseif ( '' !== trim( $content ) ) {
							$content_type = trim( $content );
						}
						break;
					case 'cc':
						$cc = array_merge( (array) $cc, explode( ',', $content ) );
						break;
					case 'bcc':
						$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
						break;
					case 'reply-to':
						$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
						break;
					default:
						// Add it to our grand headers array
						$headers[ trim( $name ) ] = trim( $content );
						break;
				}
			}
		}

		//$email_data['headers_arr']  = $headers_arr;

		// From email and name
		// If we don't have a name from the input headers
		if ( ! isset( $from_name ) ) {
			$from_name = 'WordPress';
		}

		/* If we don't have an email from the input headers default to wordpress@$sitename
		 * Some hosts will block outgoing mail from this address if it doesn't exist but
		 * there's no easy alternative. Defaulting to admin_email might appear to be another
		 * option but some hosts may refuse to relay mail from an unknown domain. See
		 * https://core.trac.wordpress.org/ticket/5007.
		 */

		if ( ! isset( $from_email ) ) {
			// Get the site domain and get rid of www.
			$sitename = strtolower( $_SERVER['SERVER_NAME'] );
			if ( substr( $sitename, 0, 4 ) == 'www.' ) {
				$sitename = substr( $sitename, 4 );
			}

			$from_email = 'wordpress@' . $sitename;
		}

		/**
		 * Filters the email address to send from.
		 *
		 * @param  string  $from_email  Email address to send from.
		 *
		 * @since 2.2.0
		 *
		 */
		$from_email = apply_filters( 'wp_mail_from', $from_email );

		/**
		 * Filters the name to associate with the "from" email address.
		 *
		 * @param  string  $from_name  Name associated with the "from" email address.
		 *
		 * @since 2.3.0
		 *
		 */
		$from_name = apply_filters( 'wp_mail_from_name', $from_name );


		$address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );

		foreach ( $address_headers as $address_header => $addresses ) {
			if ( empty( $addresses ) ) {
				continue;
			}

			foreach ( (array) $addresses as $address ) {

				// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>"
				$recipient_name = '';

				if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
					if ( count( $matches ) == 3 ) {
						$recipient_name = $matches[1];
						$address        = $matches[2];
					}
				}

				switch ( $address_header ) {
					case 'to':
						$headers_arr['email_to'][] = [ 'recipient_name' => $recipient_name, 'address' => $address ];
						break;
					case 'cc':

						$headers_arr['email_cc'][] = [ 'recipient_name' => $recipient_name, 'address' => $address ];
						break;
					case 'bcc':

						$headers_arr['email_bcc'][] = [ 'recipient_name' => $recipient_name, 'address' => $address ];
						break;
					case 'reply_to':
						$headers_arr['email_reply_to'][] = [ 'recipient_name' => $recipient_name, 'address' => $address ];
						break;
				}

			}
		}


		$headers_arr['email_from'] = [ 'from_name' => $from_name, 'from_email' => $from_email ];
		$email_data['headers_arr'] = $headers_arr;


		$data = [
			'date_created' => current_time( 'mysql' ),
			'subject'      => sanitize_text_field( $subject ),
			'email_data'   => maybe_serialize( $email_data ),
			'ip_address'   => CBXWPEmailLoggerHelper::get_ipaddress(),
			'src_tracked'  => sanitize_text_field( wp_unslash( $email_source ) ),
		];

		$data = apply_filters( 'cbxwpemaillogger_log_entry_data', $data );


		$data_format = [
			'%s', // date_created
			'%s', // subject
			'%s', // email_data
			'%s', // ip_address
			'%s'  // src_tracked
		];

		$data_format = apply_filters( 'cbxwpemaillogger_log_entry_data_format', $data_format );


		$log_insert_status = $wpdb->insert(
			$table_cbxwpemaillogger,
			$data,
			$data_format
		);

		if ( $log_insert_status != false ) {
			$log_id = $wpdb->insert_id;

			//we will set a new email header

			$enable_store_attachment = intval( $setting->get_option( 'enable_store_attachment', 'cbxwpemaillogger_log', 0 ) );
			if ( $enable_store_attachment && is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
				$this->store_email_attachments( $log_id, $attachments );
			}

			$headers_t = isset( $atts['headers'] ) ? $atts['headers'] : [];

			if ( empty( $headers_t ) ) {
				$headers_t = [];
			} else {
				if ( ! is_array( $headers_t ) ) {
					// Explode the headers out, so this function can take both
					// string headers and an array of headers.
					$headers_t = explode( "\n", str_replace( "\r\n", "\n", $headers_t ) );
				}
			}

			$headers_t[] = "x-cbxwpemaillogger-id: $log_id";


			$atts['headers'] = $headers_t;
		}


		return $atts;
	}//end insert_log

	/**
	 * Store email attachment files
	 *
	 * @param  int  $log_id
	 * @param  array  $attachments
	 */
	public function store_email_attachments( $log_id = 0, $attachments = [] ) {
		$log_id = intval( $log_id );
		if ( $log_id > 0 && is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
			$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();

			global $wp_filesystem;
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();

			$log_folder_dir = $dir_info['cbxwpemaillogger_base_dir'] . $log_id;
			if ( ! $wp_filesystem->exists( $log_folder_dir ) ) {
				$created = wp_mkdir_p( $log_folder_dir );
				if ( $created ) {
					$folder_exists = 1;
				} else {
					$folder_exists = 0;
				}
			}

			foreach ( $attachments as $attachment ) {
				$file_name = basename( $attachment );

				$wp_filesystem->copy( $attachment, $log_folder_dir . '/' . $file_name, true );
			}
		}
	}//end store_email_attachments

	/**
	 * Email log resend ajax handle
	 */
	public function email_resend() {
		check_ajax_referer( 'cbxwpemaillogger',
			'security' );

		//only logged in user and user who has option change capability can change this.
		if ( is_user_logged_in() && user_can( get_current_user_id(), 'manage_options' ) ) {

			$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

			if ( $id > 0 ) {

				global $wpdb;

				$item = CBXWPEmailLoggerHelper::SingleLog( $id );

				$email_data = maybe_unserialize( $item['email_data'] );

				$atts = isset( $email_data['atts'] ) ? $email_data['atts'] : [];


				if ( is_array( $atts ) && sizeof( $atts ) > 0 ) {

					[ $to, $subject, $message, $headers, $attachments ] = array_values( $atts );

					$attachments_t = [];
					if ( is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
						$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();

						global $wp_filesystem;
						require_once( ABSPATH . '/wp-admin/includes/file.php' );
						WP_Filesystem();

						$log_folder_dir = $dir_info['cbxwpemaillogger_base_dir'] . $id;

						foreach ( $attachments as $attachment ) {
							$file_name = basename( $attachment );

							if ( $wp_filesystem->exists( $log_folder_dir . '/' . $file_name ) ) {
								$attachments_t[] = $log_folder_dir . '/' . $file_name;
							}
						}

						$attachments = $attachments_t;
					}


					/*if(is_array($headers) && sizeof($headers) > 0){

					}
					else{
						if($headers == '') $headers = '';
					}*/

					$email_type = esc_attr( $item['email_type'] );
					set_transient( 'cbxwpemaillogger_resend_filter_mail_content_type', $email_type );

					add_filter( 'wp_mail_content_type', [ $this, 'resend_filter_mail_content_type' ] );
					$report = wp_mail( $to, $subject, $message, $headers, $attachments );
					remove_filter( 'wp_mail_content_type', [ $this, 'resend_filter_mail_content_type' ] );

					if ( $report ) {
						$return = [
							'message' => esc_html__( 'Email ReSend successfully sent.', 'cbxwpemaillogger' ),
							'success' => 1,

						];
					} else {
						$return = [
							'message' => esc_html__( 'Email ReSend but failed.', 'cbxwpemaillogger' ),
							'success' => 1,

						];
					}


					wp_send_json( $return );
				}

			}
		}//if user allowed

		$return = [
			'message' => esc_html__( 'Failed to send or not enough access to send', 'cbxwpemaillogger' ),
			'success' => 0
		];

		wp_send_json( $return );

	}//end email_resend

	/**
	 * Send email same origin content type format while resending
	 *
	 * @param  string  $content_type
	 *
	 * @return mixed|string
	 */
	public function resend_filter_mail_content_type( $content_type = 'text/plain' ) {
		$email_type = get_transient( 'cbxwpemaillogger_resend_filter_mail_content_type' );
		if ( $email_type !== false ) {
			delete_transient( 'cbxwpemaillogger_resend_filter_mail_content_type' );

			return $email_type;
		}

		return $content_type;
	}//end resend_filter_mail_content_type

	/**
	 * Download attachments
	 */
	public function download_attachment() {
		check_ajax_referer( 'cbxwpemaillogger', 'cbxwpemaillogger_nonce' );

		//only logged in user and user who has option change capability can change this.
		if ( is_user_logged_in() && user_can( get_current_user_id(), 'manage_options' ) ) {
			$log_id = isset( $_REQUEST['log_id'] ) ? absint( $_REQUEST['log_id'] ) : 0;
			$file   = isset( $_REQUEST['file'] ) ? wp_unslash( sanitize_text_field( $_REQUEST['file'] ) ) : '';

			if ( $log_id > 0 && $file != '' ) {

				$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();
				global $wp_filesystem;
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();

				$file_path = $dir_info['cbxwpemaillogger_base_dir'] . $log_id . '/' . $file;

				if ( $wp_filesystem->exists( $file_path ) ) {

					// Prevent browsers from MIME-sniffing the content-type:
					header( 'X-Content-Type-Options: nosniff' );

					header( 'Content-Type: application/octet-stream' );
					header( 'Content-Disposition: attachment; filename="' . $file . '"' );
					header( 'Content-Length: ' . CBXWPEmailLoggerHelper::fix_integer_overflow( filesize( $file_path ) ) );
					header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s T', filemtime( $file_path ) ) );
					readfile( $file_path );
				}

			}
		}//if user loggedin and has permission to manage options

		die();

	}//end download_attachment


	/**
	 * Email sent fail hook callback
	 *
	 * @param        $wp_error
	 * @param  string  $email
	 */
	public function email_sent_failed( $wp_error, $email = OBJECT ) {

		$setting          = $this->settings_api;
		$email_log_enable = intval( $setting->get_option( 'email_log_enable', 'cbxwpemaillogger_log', 1 ) );

		if ( $email_log_enable == 0 ) {
			return;
		}

		if ( ! ( $wp_error instanceof \WP_Error ) ) {
			return;
		}

		$mail_error_data    = $wp_error->get_error_data( 'wp_mail_failed' );
		$mail_error_message = sanitize_text_field( wp_unslash( $wp_error->get_error_message() ) );


		$headers = isset( $mail_error_data['headers'] ) ? $mail_error_data['headers'] : [];

		if ( isset( $headers['x-cbxwpemaillogger-id'] ) && intval( $headers['x-cbxwpemaillogger-id'] ) > 0 ) {

			$log_id = intval( $headers['x-cbxwpemaillogger-id'] );


			//$code = isset($mail_error_data['phpmailer_exception_code'])? $mail_error_data['phpmailer_exception_code'] : '';

			global $wpdb;
			$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

			$wpdb->update(
				$table_cbxwpemaillogger,
				[
					'status'        => 0,
					'error_message' => $mail_error_message,
				],
				[ 'id' => intval( $log_id ) ],
				[
					'%d',    // status
					'%s'     // status
				],
				[ '%d' ]
			);
		}


	}//end email_sent_failed

	/**
	 * Email log delete ajax handle
	 */
	public function email_log_delete() {
		check_ajax_referer( 'cbxwpemaillogger',
			'security' );

		if ( is_user_logged_in() && user_can( get_current_user_id(), 'manage_options' ) ) {
			$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

			if ( $id > 0 ) {
				global $wpdb;

				$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

				do_action( 'cbxwpemaillogger_log_delete_before', $id );

				$delete_status = $wpdb->query( $wpdb->prepare( "DELETE FROM $table_cbxwpemaillogger WHERE id=%d", $id ) );

				if ( $delete_status !== false ) {


					do_action( 'cbxwpemaillogger_log_delete_after', $id );

					$return = [
						'message' => esc_html__( 'Email log successfully.',
							'cbxwpemaillogger' ),
						'success' => 1,

					];

					wp_send_json( $return );
				}

			}
		}

		$return = [
			'message' => esc_html__( 'Failed to delete or not enough access to delete',
				'cbxwpemaillogger' ),
			'success' => 0,

		];

		wp_send_json( $return );

	}//end email_log_delete

	/**
	 * Delete attachment folder after log delete
	 *
	 * @param  int  $id
	 */
	public function delete_attachments_after_log_delete( $id = 0 ) {
		$id = intval( $id );
		if ( $id > 0 ) {
			//delete attachment folder
			$delete_status = CBXWPEmailLoggerHelper::deleteLogFolder( $id );
		}

		return $delete_status;

	}//end

	public function delete_attachments_folder() {

		$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();

		if ( intval( $dir_info['folder_exists'] ) == 1 ) {
			$cbxwpemaillogger_base_dir = $dir_info['cbxwpemaillogger_base_dir'];

			global $wp_filesystem;
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();


			$status = $wp_filesystem->delete( $cbxwpemaillogger_base_dir, true, 'd' );
		}

	}//end delete_attachments_folder


	/**
	 * Delete old from scheduled event
	 */
	public function delete_old_log() {

		$settings = new CBXWPEmailLoggerSettings();

		$delete_old_log = $settings->get_option( 'delete_old_log', 'cbxwpemaillogger_log', 'no' );

		if ( $delete_old_log == 'yes' ) {

			$log_old_days = intval( $settings->get_option( 'log_old_days', 'cbxwpemaillogger_log', '30' ) );

			if ( $log_old_days > 0 ) {

				CBXWPEmailLoggerHelper::delete_old_log( $log_old_days );
			}
		}

	}//end delete_old_log

	/**
	 * Override from email address
	 *
	 * @param $original_email_address
	 *
	 * @return string
	 */
	public function wp_mail_from_custom( $original_email_address ) {
		$setting = $this->settings_api;

		$email_smtp_enable = intval( $setting->get_option( 'email_smtp_enable', 'cbxwpemaillogger_email', 0 ) );
		$smtp_from_email   = sanitize_email( $setting->get_option( 'smtp_from_email', 'cbxwpemaillogger_email', sanitize_email( get_option( 'admin_email' ) ) ) );


		if ( $email_smtp_enable && $smtp_from_email != '' ) {
			$original_email_address = $smtp_from_email;
		}

		return $original_email_address;
	}//end wp_mail_from_custom

	/**
	 * Override from email name
	 *
	 * @param $original_email_address
	 *
	 * @return string
	 */
	public function wp_mail_from_name_custom( $original_email_name ) {
		$setting = $this->settings_api;

		$email_smtp_enable = intval( $setting->get_option( 'email_smtp_enable', 'cbxwpemaillogger_email', 0 ) );

		$smtp_from_name = sanitize_text_field( $setting->get_option( 'smtp_from_name', 'cbxwpemaillogger_email', sanitize_text_field( get_option( 'blogname' ) ) ) );

		if ( $email_smtp_enable && $smtp_from_name != '' ) {
			$original_email_name = $smtp_from_name;
		}

		return $original_email_name;
	}//end wp_mail_from_name_custom

	public function phpmailer_init_extend( $phpmailer ) {
		global $wpdb;

		$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';
		$content_type           = $phpmailer->ContentType;

		$custom_headers = $phpmailer->getCustomHeaders();
		if ( is_array( $custom_headers ) && sizeof( $custom_headers ) > 0 ) {
			foreach ( $custom_headers as $custom_header ) {
				if ( is_array( $custom_header ) && isset( $custom_header[0] ) && esc_attr( $custom_header[0] ) == 'x-cbxwpemaillogger-id' ) {
					$insert_id = isset( $custom_header[1] ) ? intval( $custom_header[1] ) : 0;
					if ( $insert_id > 0 ) {
						$log_update_status = $wpdb->update(
							$table_cbxwpemaillogger,
							[ 'email_type' => esc_attr( $content_type ) ],
							[ 'id' => $insert_id ],
							[ '%s' ],
							[ '%d' ]
						);
					}
					break;
				}
			}
		}//end email type update

		$setting = $this->settings_api;

		$email_smtp_enable = intval( $setting->get_option( 'email_smtp_enable', 'cbxwpemaillogger_email', 0 ) );


		if ( $email_smtp_enable ) {
			$smtp_email_returnpath = sanitize_email( $setting->get_option( 'smtp_email_returnpath', 'cbxwpemaillogger_email', '' ) );
			$mailer                = esc_attr( sanitize_text_field( $setting->get_option( 'mailer', 'cbxwpemaillogger_email', 'default' ) ) );


			if ( $smtp_email_returnpath != '' ) {
				$phpmailer->AddCustomHeader( 'Return-Path: ' . $smtp_email_returnpath );
				$phpmailer->Sender = $smtp_email_returnpath;
			}

			if ( $mailer == 'custom' ) {
				//if custom emailer then we need to choose which emailer we can use

				$custom_mailer = esc_attr( sanitize_text_field( $setting->get_option( 'custom_mailer', 'cbxwpemaillogger_smtps', 'custom_smtp' ) ) );


				if ( $custom_mailer == 'custom_smtp' ) {

					$smtp_email_server = intval( $setting->get_option( 'smtp_email_server', 'cbxwpemaillogger_smtps', - 1 ) );

					$smtp_email_servers_list = CBXWPEmailLoggerHelper::getSMTPHostServers( true );


					if ( is_array( $smtp_email_servers_list ) && sizeof( $smtp_email_servers_list ) > 0 && isset( $smtp_email_servers_list[ $smtp_email_server ] ) ) {

						$smtp_config = CBXWPEmailLoggerHelper::getSMTPHostServer( $smtp_email_server );


						$phpmailer->Mailer = "smtp";

						$host = isset( $smtp_config['smtp_email_host'] ) ? sanitize_text_field( $smtp_config['smtp_email_host'] ) : 'localhost';
						$port = isset( $smtp_config['smtp_email_port'] ) ? intval( $smtp_config['smtp_email_port'] ) : 25;

						$secure = isset( $smtp_config['smtp_email_secure'] ) ? esc_attr( sanitize_text_field( $smtp_config['smtp_email_secure'] ) ) : 'none';
						if ( $secure == 'none' ) {
							$secure = '';
						}

						$auth = isset( $smtp_config['smtp_email_auth'] ) ? intval( $smtp_config['smtp_email_auth'] ) : 0;

						$username = isset( $smtp_config['smtp_email_username'] ) ? sanitize_text_field( $smtp_config['smtp_email_username'] ) : '';
						$password = isset( $smtp_config['smtp_email_password'] ) ? sanitize_text_field( $smtp_config['smtp_email_password'] ) : '';

						//$phpmailer->From = $this->wsOptions["from"];
						//$phpmailer->FromName = $this->wsOptions["fromname"];
						//$phpmailer->Sender = $phpmailer->From; //Return-Path
						//$phpmailer->AddReplyTo($phpmailer->From, $phpmailer->FromName); //Reply-To

						$phpmailer->Host       = $host;
						$phpmailer->Port       = $port;
						$phpmailer->SMTPSecure = $secure;
						$phpmailer->SMTPAuth   = ( $auth ) ? true : false;

						if ( $phpmailer->SMTPAuth ) {
							$phpmailer->Username = $username;
							$phpmailer->Password = $password;
						}
					}
				}
			}
		}
	}//end phpmailer_init_extend


	/**
	 * If we need to do something in upgrader process is completed for poll plugin
	 *
	 * @param $upgrader_object
	 * @param $options
	 */
	public function plugin_upgrader_process_complete( $upgrader_object, $options ) {
		if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
			foreach ( $options['plugins'] as $each_plugin ) {
				if ( $each_plugin == CBXWPEMAILLOGGER_BASE_NAME ) {
					CBXWPEmailLoggerHelper::createTables();

					set_transient( 'cbxwpemaillogger_upgraded_notice', 1 );
					break;
				}
			}
		}

	}//end plugin_upgrader_process_complete

	/**
	 * Show a notice to anyone who has just installed the plugin for the first time
	 * This notice shouldn't display to anyone who has just updated this plugin
	 */
	public function plugin_activate_upgrade_notices() {
		// Check the transient to see if we've just activated the plugin
		if ( get_transient( 'cbxwpemaillogger_activated_notice' ) ) {
			echo '<div class="notice notice-success is-dismissible" style="border-color: #6648fe !important;">';
			echo '<p><img style="float: left; display: inline-block; margin-right: 20px;" src="' . CBXWPEMAILLOGGER_ROOT_URL . 'assets/images/icon_c_48.png' . '"/>' . sprintf( __( 'Thanks for installing/deactivating <strong>CBX Email SMTP & Logger</strong> V%s - <a href="%s" target="_blank">Codeboxr Team</a>', 'cbxwpemaillogger' ), CBXWPEMAILLOGGER_PLUGIN_VERSION, 'https://codeboxr.com' ) . '</p>';
			echo '<p>' . sprintf( __( 'Check Plugin <a href="%s">Setting</a> and <a href="%s" target="_blank"><span class="dashicons dashicons-external"></span> Documentation</a>', 'cbxwpemaillogger' ), admin_url( 'admin.php?page=cbxwpemailloggersettings' ), 'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' ) . '</p>';
			echo '</div>';


			// Delete the transient so we don't keep displaying the activation message
			delete_transient( 'cbxwpemaillogger_activated_notice' );

			$this->pro_addon_compatibility_campaign();
		}

		// Check the transient to see if we've just activated the plugin
		if ( get_transient( 'cbxwpemaillogger_upgraded_notice' ) ) {
			echo '<div class="notice notice-success is-dismissible" style="border-color: #6648fe !important;">';
			echo '<p><img style="float: left; display: inline-block;  margin-right: 20px;" src="' . CBXWPEMAILLOGGER_ROOT_URL . 'assets/images/icon_c_48.png' . '"/>' . sprintf( __( 'Thanks for upgrading <strong>CBX Email SMTP & Logger</strong> V%s - <a href="%s" target="_blank">Codeboxr Team</a>', 'cbxwpemaillogger' ), CBXWPEMAILLOGGER_PLUGIN_VERSION, 'https://codeboxr.com' ) . '</p>';
			echo '<p>' . sprintf( __( 'Check Plugin <a href="%s">Setting</a> and <a href="%s" target="_blank"><span class="dashicons dashicons-external"></span> Documentation</a>', 'cbxwpemaillogger' ), admin_url( 'admin.php?page=cbxwpemailloggersettings' ), 'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' ) . '</p>';

			echo '</div>';
			// Delete the transient so we don't keep displaying the activation message
			delete_transient( 'cbxwpemaillogger_upgraded_notice' );

			$this->pro_addon_compatibility_campaign();
		}
	}//end plugin_activate_upgrade_notices

	/**
	 * Check plugin compatibility and pro addon install campaign
	 */
	public function pro_addon_compatibility_campaign() {

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		//if the pro addon is active or installed
		if ( in_array( 'cbxwpemailloggerproaddon/cbxwpemailloggerproaddon.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || defined( 'CBXWPEMAILLOGGERPROADDON_PLUGIN_NAME' ) ) {
			//plugin is activated

			$plugin_version = CBXWPEMAILLOGGERPROADDON_PLUGIN_VERSION;


		} else {
			echo '<div style="border-left-color:#6648fe;" class="notice notice-success is-dismissible"><p>' . sprintf( __( '<a target="_blank" href="%s">CBX Email SMTP & Logger Pro Addon</a> has extended features - give it a try.', 'cbcurrencyconverter' ), 'https://codeboxr.com/product/cbx-email-logger-for-wordpress/' ) . '</p></div>';
		}
	}//end pro_addon_compatibility_campaign

	/**
	 * Full plugin reset and redirect
	 */
	public function plugin_fullreset() {
		global $wpdb;

		$option_prefix = 'cbfc_';

		$option_values = CBXWPEmailLoggerHelper::getAllOptionNames();

		foreach ( $option_values as $key => $accounting_option_value ) {
			delete_option( $accounting_option_value['option_name'] );
		}

		do_action( 'cbxwpemaillogger_plugin_option_delete' );


		// create plugin's core table tables
		activate_cbxwpemaillogger();


		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );
		$this->settings_api->admin_init();

		wp_safe_redirect( admin_url( 'admin.php?page=cbxwpemailloggersettings' ) );
		exit();
	}//end plugin_fullreset

	/**
	 * Add field to repeat fields
	 */
	public function add_new_repeat_field() {
		check_ajax_referer( 'cbxwpemaillogger', 'security' );

		$message = [];


		$section_name = isset( $_REQUEST['section_name'] ) ? wp_unslash( sanitize_text_field( $_REQUEST['section_name'] ) ) : '';
		$option_name  = isset( $_REQUEST['option_name'] ) ? wp_unslash( sanitize_text_field( $_REQUEST['option_name'] ) ) : '';
		$field_name   = isset( $_REQUEST['field_name'] ) ? wp_unslash( sanitize_text_field( $_REQUEST['field_name'] ) ) : '';
		$index        = isset( $_REQUEST['index'] ) ? intval( $_REQUEST['index'] ) : 0;

		//$index = 1;
		$html = '';

		if ( $section_name != '' && $option_name != '' ) {
			$all_fields     = $this->get_settings_fields();
			$section_fields = isset( $all_fields[ $section_name ] ) ? $all_fields[ $section_name ] : [];


			$args            = [];
			$args['name']    = $field_name;
			$args['id']      = $option_name;
			$args['section'] = $section_name;

			if ( isset( $section_fields[ $option_name ] ) ) {
				$option_field = $section_fields[ $option_name ];
				$fields       = $option_field['fields'];


				if ( is_array( $fields ) & sizeof( $fields ) > 0 ) {

					//foreach ( $value as $val ) {
					/*if ( ! is_array( $val ) ) {
						$val = array();
					}*/

					$html .= '<div class="form-table-fields-parent-item">';
					$html .= '<h5>' . $field_name . ' #' . ( $index + 1 );
					$html .= '<span class="form-table-fields-parent-item-icon form-table-fields-parent-item-sort"></span>';
					$html .= '<span class="form-table-fields-parent-item-icon form-table-fields-parent-item-control"></span>';
					$html .= '<span class="form-table-fields-parent-item-icon form-table-fields-parent-item-delete"></span>';

					$html .= '</h5>';
					$html .= '<div class="form-table-fields-parent-item-wrap">';

					$html .= '<table class="form-table-fields-items">';
					foreach ( $fields as $field ) {
						$args_t = $args;
						//unset( $args_t['fields'] );
						//unset( $args_t['allow_new'] );

						$args_t['section']           = isset( $args['section'] ) ? $args['section'] . '[' . $args['id'] . '][' . $index . ']' : '';
						$args_t['desc']              = isset( $field['desc'] ) ? $field['desc'] : '';
						$args_t['name']              = isset( $field['name'] ) ? $field['name'] : '';
						$args_t['label']             = isset( $field['label'] ) ? $field['label'] : '';
						$args_t['class']             = isset( $field['class'] ) ? $field['class'] : $args_t['name'];
						$args_t['id']                = $args_t['name'];
						$args_t['size']              = isset( $field['size'] ) ? $field['size'] : null;
						$args_t['min']               = isset( $field['min'] ) ? $field['min'] : '';
						$args_t['max']               = isset( $field['max'] ) ? $field['max'] : '';
						$args_t['step']              = isset( $field['step'] ) ? $field['step'] : '';
						$args_t['options']           = isset( $field['options'] ) ? $field['options'] : '';
						$args_t['default']           = isset( $field['default'] ) ? $field['default'] : '';
						$args_t['sanitize_callback'] = isset( $field['sanitize_callback'] ) ? $field['sanitize_callback'] : '';
						$args_t['placeholder']       = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
						$args_t['type']              = isset( $field['type'] ) ? $field['type'] : 'text';
						$args_t['optgroup']          = isset( $field['optgroup'] ) ? intval( $field['optgroup'] ) : 0;
						$args_t['sortable']          = isset( $field['sortable'] ) ? intval( $field['sortable'] ) : 0;
						$callback                    = isset( $field['callback'] ) ? $field['callback'] : [ $this->settings_api, 'callback_' . $args_t['type'] ];


						$val_t = $args_t['default'];

						$html    .= '<tr class="form-table-fields-item"><td>';
						$html_id = "{$args_t['section']}_{$args_t['id']}";
						$html_id = CBXWPEmailLoggerHelper::settings_clean_label_for( $html_id );
						$html    .= sprintf( '<label class="main-label" for="%1$s">%2$s</label>', $html_id, $args_t['label'] );
						$html    .= '</td></tr>';

						$html .= '<tr class="form-table-fields-item"><td>';
						ob_start();
						call_user_func( $callback, $args_t, $val_t );
						$html .= ob_get_contents();
						ob_end_clean();
						$html .= '</td></tr>';
					}
					$html .= '</table>';
					$html .= '</div>';
					$html .= '</div>';
					$index ++;
					//}

				}
			}
		}//if valid section name, option name
		$message['html']  = $html;
		$message['index'] = $index;

		wp_send_json( $message );
	}//end add_new_repeat_field

	/**
	 * Email testing submit
	 */
	public function email_testing_submit() {
		//if backend sign edit form submit and also nonce verified then go
		if ( ( isset( $_POST['cbxwpemaillogger-email-testing'] ) && intval( $_POST['cbxwpemaillogger-email-testing'] ) == 1 ) &&
		     ( isset( $_POST['cbxwpemaillogger-security'] ) && wp_verify_nonce( $_POST['cbxwpemaillogger-security'],
				     'cbxwpemaillogger' ) )
		) {
			$post_data = wp_unslash( $_POST ); //all needed fields of $_POST is sanitized below
			$to        = isset( $post_data['to'] ) ? sanitize_text_field( $post_data['to'] ) : '';
			$subject   = isset( $post_data['subject'] ) ? sanitize_text_field( $post_data['subject'] ) : '';
			$message   = isset( $post_data['message'] ) ? sanitize_textarea_field( $post_data['message'] ) : '';
			$file      = isset( $_FILES['file']['tmp_name'] ) ? $_FILES['file']['tmp_name'] : '';

			$response = '';

			if ( ! is_email( $to ) ) {
				$response = esc_html__( 'To email is not valid', 'cbxwpemaillogger' );
			} elseif ( $subject == '' ) {
				$response = esc_html__( 'Subject is empty', 'cbxwpemaillogger' );
			} elseif ( $message == '' ) {
				$response = esc_html__( 'Message is empty', 'cbxwpemaillogger' );
			}

			$email_success = 0;

			if ( $response == '' ) {
				add_action( 'wp_mail_failed', [ $this, 'wp_mail_failed_testing' ] );
				try {
					$attachments = [];
					$headers     = '';


					if ( $file != '' ) {
						if ( ! function_exists( 'wp_handle_upload' ) ) {
							require_once( ABSPATH . 'wp-admin/includes/file.php' );
						}

						$uploaded_file = $_FILES['file'];

						$upload_overrides = [
							'test_form' => false
						];

						$move_file = wp_handle_upload( $uploaded_file, $upload_overrides );

						if ( $move_file && ! isset( $move_file['error'] ) ) {
							$attachments[] = $move_file['file'];
							$headers       = [ 'Content-Type: text/html; charset=UTF-8' ];

						} else {
							/*
							 * Error generated by _wp_handle_upload()
							 * @see _wp_handle_upload() in wp-admin/includes/file.php
							 */
							//echo $movefile['error'];
						}


					}


					$status = wp_mail( $to, $subject, $message, $headers, $attachments );
					if ( $status ) {
						$response      = esc_html__( 'Email sent successfully', 'cbxwpemaillogger' );
						$email_success = 1;

						//let's delete the uploaded file
						if ( sizeof( $attachments ) > 0 ) {
							foreach ( $attachments as $attachment ) {
								wp_delete_file( $attachment );
							}
						}
					} else {
						$response = sprintf( __( 'Email sent failed. Error message: %s', 'cbxwpemaillogger' ), $this->test_error_msg );
					}
				} catch ( Exception $e ) {
					$response = sprintf( __( 'Email sent failed. Error message: %s', 'cbxwpemaillogger' ), $e->getMessage() );
				}

				remove_action( 'wp_mail_failed', [ $this, 'wp_mail_failed_testing' ] );
				$this->test_error_msg = '';
			}

			update_option( 'cbxwpemaillogger_testmsg', [ 'message' => $response, 'type' => intval( $email_success ) ] );

			wp_safe_redirect( admin_url( 'admin.php?page=cbxwpemailloggeremailtesting' ) );
			exit;
		}
	}//end email_testing_submit

	public function wp_mail_failed_testing( $wp_error, $email = OBJECT ) {
		if ( ! ( $wp_error instanceof \WP_Error ) ) {
			return;
		}

		$mail_error_data    = $wp_error->get_error_data( 'wp_mail_failed' );
		$mail_error_message = sanitize_text_field( wp_unslash( $wp_error->get_error_message() ) );

		$this->test_error_msg = $mail_error_message;
	}
}//end class CBXWPEmailLogger_Admin