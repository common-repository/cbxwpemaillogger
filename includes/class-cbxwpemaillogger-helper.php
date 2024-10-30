<?php
/**
 * The file that defines the plugin helper class
 *
 * A class contains static method for quick usages
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    CBXWPEmailLogger
 * @subpackage CBXWPEmailLogger/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<?php

/**
 * Helper class with static methods
 *
 * Class CBXWPEmailLoggerHelper
 */
class CBXWPEmailLoggerHelper {
	/**
	 * Known src, from which plugin email is sent
	 *
	 * @return mixed|void
	 */
	public static function email_known_src() {
		$src = [
			'contact-form-7' => esc_html__( 'Contact Form 7', 'cbxwpemaillogger' )
		];

		return apply_filters( 'cbxwpemaillogger_known_src', $src );
	}//end email_known_src


	/**
	 * Get all  core tables list
	 */
	public static function getAllDBTablesList() {
		global $wpdb;

		$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

		$table_names                     = [];
		$table_names['cbxwpemaillogger'] = $table_cbxwpemaillogger;


		return apply_filters( 'cbxwpemaillogger_table_list', $table_names );
	}//end getAllDBTablesList

	/**
	 * List all global option name with prefix cbxuseronline_
	 */
	public static function getAllOptionNames() {
		global $wpdb;

		$prefix       = 'cbxwpemaillogger_';
		$option_names = $wpdb->get_results( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'", ARRAY_A );

		return apply_filters( 'cbxwpemaillogger_option_names', $option_names );
	}//end getAllOptionNames

	/**
	 * Returns list of custom mailers
	 *
	 * @return mixed|void
	 */
	public static function getCustomMailer() {
		$custom_mailer = [];

		$custom_mailer['custom_smtp'] = esc_html__( 'Custom SMTP Host Server', 'cbxwpemaillogger' );

		return apply_filters( 'cbxwpemaillogger_custom_mailer', $custom_mailer );
	}//end getCustomMailer

	/*public static function smtp_email_servers_default(){
		$servers = array();
		$servers[] = array(
			'smtp_email_enable'   => 1,
			'smtp_email_host'     => 'localhost',
			'smtp_email_port'     => '25',
			'smtp_email_secure'   => 'none',
			'smtp_email_auth'     => 0,
			'smtp_email_username' => '',
			'smtp_email_password' => '',
		);

		return apply_filters('smtp_email_servers_default', $servers);
	}*/

	/**
	 * Returns list of custom smtp host servers
	 *
	 * @param  bool  $enabled_only
	 *
	 * @return array
	 */
	public static function getSMTPHostServers( $enabled_only = false ) {
		$settings           = new CBXWPEmailLoggerSettings();
		$smtp_email_servers = $settings->get_option( 'smtp_email_servers', 'cbxwpemaillogger_smtps', [] );
		if ( ! is_array( $smtp_email_servers ) ) {
			$smtp_email_servers = [];
		}

		$smtp_email_servers_list = [];

		if ( is_array( $smtp_email_servers ) && sizeof( $smtp_email_servers ) > 0 ) {
			$index = 0;
			foreach ( $smtp_email_servers as $smtp_email_server ) {

				$smtp_email_enable = isset( $smtp_email_server['smtp_email_enable'] ) ? intval( $smtp_email_server['smtp_email_enable'] ) : 0;
				$smtp_email_host   = isset( $smtp_email_server['smtp_email_host'] ) ? esc_attr( $smtp_email_server['smtp_email_host'] ) : '';
				$smtp_email_port   = isset( $smtp_email_server['smtp_email_port'] ) ? intval( $smtp_email_server['smtp_email_port'] ) : 0;

				if ( $enabled_only && $smtp_email_enable ) {
					$smtp_email_servers_list[ $index ] = sprintf( esc_html__( 'SMTP Host Servers #%d(%s, %s)', 'cbxwpemaillogger' ), ( $index + 1 ), $smtp_email_host, $smtp_email_port );
				}


				$index ++;
			}
		}

		return $smtp_email_servers_list;
	}//end getSMTPHostServers


	/**
	 * Returns smtp server config
	 *
	 *
	 *
	 * @return array
	 */
	public static function getSMTPHostServer( $index ) {
		$settings           = new CBXWPEmailLoggerSettings();
		$smtp_email_servers = $settings->get_option( 'smtp_email_servers', 'cbxwpemaillogger_smtps', [] );
		if ( ! is_array( $smtp_email_servers ) ) {
			$smtp_email_servers = [];
		}

		$smtp_config = isset( $smtp_email_servers[ $index ] ) ? $smtp_email_servers[ $index ] : [];

		return $smtp_config;

	}//end getSMTPHostServer

	/**
	 * Get IP address
	 *
	 * @return string|void
	 */
	public static function get_ipaddress() {

		if ( empty( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {

			$ip_address = $_SERVER["REMOTE_ADDR"];
		} else {

			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}

		if ( strpos( $ip_address, ',' ) !== false ) {

			$ip_address = explode( ',', $ip_address );
			$ip_address = $ip_address[0];
		}

		return esc_attr( $ip_address );
	}//end get_ipaddress

	/**
	 * Returns bulk actions
	 *
	 * @return array
	 */
	public static function StatusOptions() {
		$status_arr = [];

		$status_arr['delete'] = esc_html__( 'Delete', 'cbxwpemaillogger' );

		return $status_arr;
	}//end StatusOptions

	/**
	 * Readable timestamp
	 *
	 * @param $timestamp
	 *
	 * @return false|string;
	 */
	public static function DateReadableFormat( $timestamp, $format = 'M j, Y' ) {
		$format = ( $format == '' ) ? 'M j, Y' : $format;

		return date( $format, strtotime( $timestamp ) );
	}//end dateReadableFormat

	/**
	 * Get Single review by review id
	 *
	 * @param  int  $post_id
	 * @param  int  $user_id
	 *
	 * @return null|string
	 */
	public static function SingleLog( $log_id = 0 ) {
		global $wpdb;
		$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

		$log_id = intval( $log_id );

		$log = null;
		if ( $log_id > 0 ) {

			$where_sql = $sql_select = '';
			$where_sql = $wpdb->prepare( "log.id=%d", $log_id );

			$sql_select = "SELECT log.* FROM $table_cbxwpemaillogger AS log";

			$log = $wpdb->get_row( "$sql_select WHERE $where_sql ", 'ARRAY_A' );
		}

		return $log;
	}//end SingleLog


	/**
	 * Delete $log_old_days days old log
	 *
	 * @param  int  $log_old_days
	 */
	public static function delete_old_log( $log_old_days = 30 ) {

		global $wpdb;
		$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

		$sql_select = "SELECT log.* FROM $table_cbxwpemaillogger AS log";

		$logs = $wpdb->get_results( "$sql_select WHERE log.date_created <= NOW() - INTERVAL $log_old_days DAY", 'ARRAY_A' );

		if ( is_array( $logs ) && sizeof( $logs ) > 0 ) {
			foreach ( $logs as $log ) {
				$id = intval( $log['id'] );

				do_action( 'cbxwpemaillogger_log_delete_before', $id );

				$delete_status = $wpdb->query( $wpdb->prepare( "DELETE FROM $table_cbxwpemaillogger WHERE id=%d", $id ) );

				if ( $delete_status !== false ) {
					do_action( 'cbxwpemaillogger_log_delete_after', $id );
				}
			}
		}
	}//end delete_old_log

	/**
	 * Return default from email of wordpress core
	 */
	public static function default_from_email() {
		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;

		return $from_email;

	}//end default_from_email

	/**
	 * Returns default from name of wordpress core
	 */
	public static function default_email_name() {

		return 'WordPress';

	}//end default_email_name

	/**
	 * delete uploaded photos of the petition
	 *
	 * @param  int  $log_id
	 *
	 * @return bool
	 */
	public static function deleteLogFolder( $log_id = 0 ) {
		$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();


		$status = false;
		if ( absint( $log_id ) > 0 && intval( $dir_info['folder_exists'] ) == 1 ) {

			global $wp_filesystem;
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();

			//$dir_to_del       = wp_upload_dir()['basedir'] . '/cbxwpemaillogger/' . $review_id;
			//$dir_thumb_to_del = $dir_to_del . '/thumbnail';
			$dir_to_del = $dir_info['cbxwpemaillogger_base_dir'] . $log_id;
			//$dir_thumb_to_del = $dir_to_del . '/thumbnail';

			//if dir exists then delete


			/*array_map( 'unlink', glob( "$dir_to_del/*.*" ) );
			array_map( 'unlink', glob( "$dir_thumb_to_del/*.*" ) );
			if ( @rmdir( $dir_thumb_to_del ) ) {
				@rmdir( $dir_to_del );
			}*/

			$status = $wp_filesystem->delete( $dir_to_del, true, 'd' );
		}

		return $status;
	}//end deleteLogFolder

	/**
	 * make cbxwpemaillogger folder in uploads directory if not exist, return path info
	 *
	 * @return mixed|void
	 */
	public static function checkUploadDir() {
		/*if ( apply_filters( 'cbxwpsimpleaccounting_install_skip_create_files', false ) ) {
			return;
		}

		// Install files and folders for uploading files and prevent hotlinking.
		$upload_dir      = wp_upload_dir();
		//$download_method = get_option( 'woocommerce_file_download_method', 'force' );

		$files = array(
			array(
				'base'    => $upload_dir['basedir'] . '/cbxwpsimpleaccounting_uploads',
				'file'    => 'index.html',
				'content' => '',
			),
			array(
				'base'    => $upload_dir['basedir'] . '/cbxwpsimpleaccounting_uploads',
				'file'    => '.htaccess',
				'content' => 'deny from all',
			)
		);


		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen
				if ( $file_handle ) {
					fwrite( $file_handle, $file['content'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
					fclose( $file_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
				}
			}
		}*/

		$upload_dir = wp_upload_dir();

		//wordpress core base dir and url
		$upload_dir_basedir = $upload_dir['basedir'];
		$upload_dir_baseurl = $upload_dir['baseurl'];

		//cbxwpemaillogger base dir and base url
		$cbxwpemaillogger_base_dir = $upload_dir_basedir . '/cbxwpemaillogger/';
		$cbxwpemaillogger_base_url = $upload_dir_baseurl . '/cbxwpemaillogger/';

		//cbxwpemaillogger temp dir and temp url
		//$cbxwpemaillogger_temp_dir = $upload_dir_basedir . '/cbxwpemaillogger/temp/';
		//$cbxwpemaillogger_temp_url = $upload_dir_baseurl . '/cbxwpemaillogger/temp/';

		/*if ( ! class_exists( 'WP_Filesystem_Base' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
		}*/

		global $wp_filesystem;
		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		WP_Filesystem();

		$folder_exists = 1;
		//let's check if the cbxwpemaillogger folder exists in upload dir
		//if ( ! ( new WP_Filesystem_Base )->exists( $cbxwpemaillogger_temp_dir ) ) {
		if ( ! $wp_filesystem->exists( $cbxwpemaillogger_base_dir ) ) {

			$created = wp_mkdir_p( $cbxwpemaillogger_base_dir );
			if ( $created ) {
				$folder_exists = 1;

				$files = [
					[
						'base'    => $upload_dir_basedir . '/cbxwpemaillogger',
						'file'    => 'index.html',
						'content' => '',
					],
					[
						'base'    => $upload_dir_basedir . '/cbxwpemaillogger',
						'file'    => '.htaccess',
						'content' => 'deny from all',
					]
				];


				foreach ( $files as $file ) {
					if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
						$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen
						if ( $file_handle ) {
							fwrite( $file_handle, $file['content'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
							fclose( $file_handle );                   // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
						}
					}
				}


			} else {
				$folder_exists = 0;
			}
		}

		$dir_info = [
			'folder_exists'             => $folder_exists,
			'upload_dir_basedir'        => $upload_dir_basedir,
			'upload_dir_baseurl'        => $upload_dir_baseurl,
			'cbxwpemaillogger_base_dir' => $cbxwpemaillogger_base_dir,
			'cbxwpemaillogger_base_url' => $cbxwpemaillogger_base_url
		];

		return apply_filters( 'cbxwpemaillogger_dir_info', $dir_info );
	}//end checkUploadDir

	/**
	 * Create  necessary tables
	 */
	public static function createTables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		//tables
		$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		//create rating log table
		$table_rating_log_sql = "CREATE TABLE $table_cbxwpemaillogger (
                          id bigint(20) unsigned NOT NULL AUTO_INCREMENT,                             
                          date_created datetime NOT NULL COMMENT 'created date',                                                                              
                          subject varchar(255) NOT NULL DEFAULT '' COMMENT 'email subject',
                          email_type varchar(255) NOT NULL DEFAULT 'text/plain' COMMENT 'email type',
                          email_data longtext NOT NULL DEFAULT '' COMMENT 'email body and header',
                          ip_address varchar(45) NOT NULL,                                                       
                          status tinyint(3) NOT NULL DEFAULT 1 COMMENT '1 means sent, 0 means failed',
                          error_message text NOT NULL DEFAULT '' COMMENT 'email sending error message',
                          src_tracked varchar(255) NOT NULL DEFAULT '' COMMENT 'track plugin',
                          PRIMARY KEY (id)
                        ) $charset_collate; ";

		dbDelta( $table_rating_log_sql );
	}//end createTables

	// Fix for overflowing signed 32 bit integers,
	// works for sizes up to 2^32-1 bytes (4 GiB - 1):
	public static function fix_integer_overflow( $size ) {
		if ( $size < 0 ) {
			$size += 2.0 * ( PHP_INT_MAX + 1 );
		}

		return $size;
	}//end fix_integer_overflow

	/**
	 * Get logs data
	 *
	 * @param  string  $search
	 * @param  string  $logdate
	 * @param  string  $emailsource
	 * @param  int  $status
	 * @param  string  $orderby
	 * @param  string  $order
	 * @param  int  $perpage
	 * @param  int  $page
	 *
	 * @return array|null|object
	 */
	public static function getLogData( $search = '', $logdate = '', $emailsource = '', $status = - 1, $orderby = 'logs.id', $order = 'DESC', $perpage = 20, $page = 1 ) {

		global $wpdb;

		$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';


		$sql_select = "logs.*";
		$where_sql  = '';

		if ( $search != '' ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}

			$where_sql .= $wpdb->prepare( " logs.subject LIKE '%%%s%%' OR logs.email_data LIKE '%%%s%%' ", $search, $search );
		}

		if ( $emailsource != '' ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}

			$where_sql .= $wpdb->prepare( " logs.src_tracked = %s ", $emailsource );
		}

		if ( $status != - 1 ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}

			$where_sql .= $wpdb->prepare( " logs.status = %d ", $status );
		}

		if ( $logdate != '' ) {

			$logdate   = explode( "-", $logdate );
			$date_from = trim( $logdate[0] ) . '-' . trim( $logdate[1] ) . '-' . trim( $logdate[2] );
			$date_to   = trim( $logdate[3] ) . '-' . trim( $logdate[4] ) . '-' . trim( $logdate[5] );

			if ( $date_from != '' && $date_to != '' ) {
				if ( $where_sql != '' ) {
					$where_sql .= ' AND ';
				}

				//$where_sql .= $wpdb->prepare( " DATE(logs.date_created) = %s ", $logdate );
				$where_sql .= " CAST(logs.date_created AS DATE) ";
				$where_sql .= $wpdb->prepare( "between %s AND %s ", $date_from, $date_to );
			}


		}


		$where_sql = apply_filters( 'cbxwpemaillogger_log_listing_where', $where_sql, $search, $orderby, $order, $perpage, $page );

		if ( $where_sql == '' ) {
			$where_sql = '1';
		}

		$start_point = ( $page * $perpage ) - $perpage;
		$limit_sql   = "LIMIT";
		$limit_sql   .= ' ' . $start_point . ',';
		$limit_sql   .= ' ' . $perpage;

		$sortingOrder = " ORDER BY $orderby $order ";

		$data = $wpdb->get_results( "SELECT $sql_select FROM $table_cbxwpemaillogger as logs WHERE  $where_sql $sortingOrder  $limit_sql", 'ARRAY_A' );

		return $data;
	}//end getLogData

	/**
	 * Logs data count
	 *
	 * @param  string  $search
	 * @param  string  $logdate
	 * @param  string  $emailsource
	 *
	 * @return null|string
	 */
	public static function getLogDataCount( $search = '', $logdate = '', $emailsource = '', $status = - 1 ) {

		global $wpdb;
		$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

		$sql_select = "SELECT COUNT(*) FROM $table_cbxwpemaillogger as logs";

		$where_sql = '';


		if ( $search != '' ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}

			$where_sql .= $wpdb->prepare( " logs.subject LIKE '%%%s%%' OR logs.email_data LIKE '%%%s%%' ", $search, $search );
		}

		if ( $emailsource != '' ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}

			$where_sql .= $wpdb->prepare( " logs.src_tracked = %s ", $emailsource );
		}

		if ( $status != - 1 ) {
			if ( $where_sql != '' ) {
				$where_sql .= ' AND ';
			}

			$where_sql .= $wpdb->prepare( " logs.status = %d ", $status );
		}

		if ( $logdate != '' ) {
			$logdate   = explode( "-", $logdate );
			$date_from = trim( $logdate[0] ) . '-' . trim( $logdate[1] ) . '-' . trim( $logdate[2] );
			$date_to   = trim( $logdate[3] ) . '-' . trim( $logdate[4] ) . '-' . trim( $logdate[5] );

			if ( $date_from != '' && $date_to != '' ) {
				if ( $where_sql != '' ) {
					$where_sql .= ' AND ';
				}

				//$where_sql .= $wpdb->prepare( " DATE(logs.date_created) = %s ", $logdate );
				$where_sql .= " CAST(logs.date_created AS DATE) ";
				$where_sql .= $wpdb->prepare( "between %s AND %s ", $date_from, $date_to );
			}
		}

		$where_sql = apply_filters( 'cbxwpemaillogger_log_listing_where_total', $where_sql, $search );

		if ( $where_sql == '' ) {
			$where_sql = '1';
		}


		$count = $wpdb->get_var( "$sql_select WHERE  $where_sql" );

		return $count;
	}//end getLogDataCount

	/**
	 * Clean label_for or id tad
	 *
	 * @param $str
	 *
	 * @return mixed
	 */
	public static function settings_clean_label_for( $str ) {
		$str = str_replace( '][', '_', $str );
		$str = str_replace( ']', '_', $str );
		$str = str_replace( '[', '_', $str );

		return $str;
	}//end settings_clean_label_for


	/**
	 * Add utm params to any url
	 *
	 * @param  string  $url
	 *
	 * @return string
	 */
	public static function url_utmy( $url = '' ) {
		if ( $url == '' ) {
			return $url;
		}

		$url = add_query_arg( [
			'utm_source'   => 'plgsidebarinfo',
			'utm_medium'   => 'plgsidebar',
			'utm_campaign' => 'wpfreemium',
		], $url );

		return $url;
	}//end url_utmy
}//end class CBXWPEmailLoggerHelper