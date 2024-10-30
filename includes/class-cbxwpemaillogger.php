<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXWPEmailLogger
 * @subpackage CBXWPEmailLogger/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    CBXWPEmailLogger
 * @subpackage CBXWPEmailLogger/includes
 * @author     Codeboxr <info@codeboxr.com>
 */
class CBXWPEmailLogger {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  2.0.1
	 */
	private static $instance = null;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->version     = CBXWPEMAILLOGGER_PLUGIN_VERSION;
		$this->plugin_name = CBXWPEMAILLOGGER_PLUGIN_NAME;

		$this->load_dependencies();


		//$this->set_locale();
		$this->define_common_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Singleton Instance.
	 *
	 * Ensures only one instance of cbxtakeatour is loaded or can be loaded.
	 *
	 * @return self Main instance.
	 * @see run_cbxtakeatour()
	 * @since  2.0.1
	 * @static
	 */
	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}//end method instance

	/**
	 * All the common hooks
	 *
	 * @since    2.0.1
	 * @access   private
	 */
	private function define_common_hooks()
	{
		add_action('plugins_loaded', [$this, 'load_plugin_textdomain']);
	}//end method define_common_hooks

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.1
	 */
	public function load_plugin_textdomain()
	{
		load_plugin_textdomain('cbxwpemaillogger', false, CBXWPEMAILLOGGER_ROOT_PATH.'languages/');
	}//end method load_plugin_textdomain

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - CBXWPEmailLogger_Loader. Orchestrates the hooks of the plugin.
	 * - CBXWPEmailLogger_i18n. Defines internationalization functionality.
	 * - CBXWPEmailLogger_Admin. Defines all hooks for the admin area.
	 * - CBXWPEmailLogger_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cbxwpemaillogger-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxwpemaillogger-helper.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxwpemaillogger-settings.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cbxwpemaillogger-logs.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/dashboard_widgets/class-cbxwpemaillogger-dashboard-widget.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cbxwpemaillogger-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cbxwpemaillogger-public.php';
	}//end method load_dependencies

	

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new CBXWPEmailLogger_Admin( $this->get_plugin_name(), $this->get_version() );


		//create admin menu page
		add_action( 'admin_init', [$plugin_admin, 'admin_init'] );
		add_action( 'admin_menu', [$plugin_admin, 'admin_pages'] );
		add_action( 'admin_init', [$plugin_admin, 'email_testing_submit'] );
		add_filter( 'set-screen-option', [$plugin_admin, 'cbxscratingreview_listing_per_page'], 10, 3 );

		add_action( 'admin_enqueue_scripts', [$plugin_admin, 'enqueue_styles'] );
		add_action( 'admin_enqueue_scripts', [$plugin_admin, 'enqueue_scripts'] );

		add_filter( 'wp_mail', [$plugin_admin, 'insert_log'] );
		add_action( 'wp_mail_failed', [$plugin_admin, 'email_sent_failed'], 10, 2 );
		//add_action( 'bp_send_email_failure', $plugin_admin, 'email_sent_failed', 10, 2 );
		//bp_send_email_failure  for buddypress
		//added from v1.0.3
		add_filter( 'wp_mail_from', [$plugin_admin, 'wp_mail_from_custom'], 99999 );
		add_filter( 'wp_mail_from_name', [$plugin_admin, 'wp_mail_from_name_custom'], 99999 );
		add_filter( 'phpmailer_init', [$plugin_admin, 'phpmailer_init_extend'], 99999 );
		//add_filter( 'bp_phpmailer_init', $plugin_admin, 'phpmailer_init_extend', 99999 );

		add_action( 'wp_ajax_cbxwpemaillogger_log_delete', [$plugin_admin, 'email_log_delete'] ); //email_log_delete
		add_action( 'wp_ajax_cbxwpemaillogger_log_resend', [$plugin_admin, 'email_resend'] );     //resend email

		add_action( 'wp_ajax_cbxwpemaillogger_download_attachment', [$plugin_admin, 'download_attachment'] ); //download attachment

		add_action( 'cbxwpemaillogger_log_delete_after', [$plugin_admin, 'delete_attachments_after_log_delete'] );
		add_action( 'cbxwpemaillogger_log_all_delete_after', [$plugin_admin, 'delete_attachments_folder'] );

		//cron event
		add_action( 'cbxwpemaillogger_daily_event', [$plugin_admin, 'delete_old_log'] );                      //delete x days old logs every day


		//for upgrade process
		add_action( 'upgrader_process_complete', [$plugin_admin, 'plugin_upgrader_process_complete'], 10, 2 );
		add_action( 'admin_notices', [$plugin_admin, 'plugin_activate_upgrade_notices'] );
		add_filter( 'plugin_action_links_' . CBXWPEMAILLOGGER_BASE_NAME, [$plugin_admin, 'plugin_action_links'] );
		add_filter( 'plugin_row_meta', [$plugin_admin, 'plugin_row_meta'], 10, 4 );

		//dashboard widget

		$dashboard_widget = new CBXWPEmailLoggerDashboardWidget();
		add_action( 'wp_dashboard_setup', [$dashboard_widget, 'dashboard_widget'] );


		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'cbxwpemailloggersettings' && isset( $_REQUEST['cbxwpemaillogger_fullreset'] ) && intval( $_REQUEST['cbxwpemaillogger_fullreset'] ) == 1 ) {
			add_action( 'admin_init', [$plugin_admin, 'plugin_fullreset'] );
		}

		//add new field in repeat fields
		add_action( 'wp_ajax_cbxwpemaillogger_add_new_field', [$plugin_admin, 'add_new_repeat_field'] );      //add new repeat field

	}//end define_admin_hooks

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new CBXWPEmailLogger_Public( $this->get_plugin_name(), $this->get_version() );

		add_action( 'template_redirect', [$plugin_public, 'email_log_body'] );

	}//end define_public_hooks

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}
}//end class CBXWPEmailLogger