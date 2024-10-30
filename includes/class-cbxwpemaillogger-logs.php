<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class CBXWPEmailLogger_List_Table extends WP_List_Table {

	/**
	 * The current list of all branches.
	 *
	 * @since  3.1.0
	 * @access public
	 * @var array
	 */
	function __construct() {

		//Set parent defaults
		parent::__construct( [
			'singular' => 'cbxwpemaillogger',     //singular name of the listed records
			'plural'   => 'cbxwpemailloggers',    //plural name of the listed records
			'ajax'     => false                   //does this table support ajax?
		] );
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/
			$this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/
			$item['id']                //The value of the checkbox should be the record's id
		);
	}//end column_cb

	/**
	 * Callback for column 'id'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_id( $item ) {
		/*return '<a href="' . admin_url( 'admin.php?page=cbxscratingreviewreviewlist&view=view&id=' . $item['id'] ) . '" title="' . esc_html__( 'View Review', 'cbxwpemaillogger' ) . '">' . $item['id'] . '</a>' . ' (<a target="_blank" href="' . admin_url( 'admin.php?page=cbxscratingreviewreviewlist&view=addedit&id=' . $item['id'] ) . '" title="' . esc_html__( 'Edit Review', 'cbxwpemaillogger' ) . '">' . esc_html__( 'Edit', 'cbxwpemaillogger' ) . '</a>)';*/
	}


	/**
	 * Callback for column 'Date Created'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_date_created( $item ) {

		$actions = [];

		$view_url = add_query_arg( [
			'view'   => 'email',
			'log_id' => intval( $item['id'] )
		] );

		$body_url = add_query_arg( [
			'action'   => 'cbxwpemaillogger_log_body',
			'id'       => intval( $item['id'] ),
			'_wpnonce' => wp_create_nonce( 'cbxwpemaillogger' )
		], site_url() );

		$actions['view'] = '<a class="cbxwpemaillogger_actions cbxwpemaillogger_actions_view" href="' . esc_url( $view_url ) . '">' . esc_html__( 'View', 'cbxwpemaillogger' ) . '</a>';

		$actions['delete'] = '<a data-busy="0" data-id="' . intval( $item['id'] ) . '" class="cbxwpemaillogger_actions cbxwpemaillogger_actions_delete" href="#">' . esc_html__( 'Delete', 'cbxwpemaillogger' ) . '</a>';

		$actions['template'] = '<a title="' . esc_html__( 'Email Template/Body Preview', 'cbxwpemaillogger' ) . '" class="thickbox cbxwpemaillogger_actions cbxwpemaillogger_actions_template" href="' . esc_url( $body_url ) . '">' . esc_html__( 'Template', 'cbxwpemaillogger' ) . '</a>';

		$actions['resend'] = '<a  data-busy="0" data-id="' . intval( $item['id'] ) . '" class="cbxwpemaillogger_actions cbxwpemaillogger_actions_resend" href="#">' . esc_html__( 'ReSend', 'cbxwpemaillogger' ) . '</a>';

		$date_created = '';
		if ( $item['date_created'] != '' ) {
			$date_created = CBXWPEmailLoggerHelper::DateReadableFormat( stripslashes( $item['date_created'] ), 'M j, Y g:i a' );
		}

		return $date_created . $this->row_actions( $actions );
	}//end column_date_created


	/**
	 * Callback for column 'email_from'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_email_from( $item ) {
		$email_data  = maybe_unserialize( $item['email_data'] );
		$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : [];
		$email_from  = isset( $headers_arr['email_from'] ) ? $headers_arr['email_from'] : [];

		return $email_from['from_name'] . '(' . sanitize_email( $email_from['from_email'] ) . ')';
	}//end column_to

	/**
	 * Callback for column 'email_to'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_email_to( $item ) {
		$email_data  = maybe_unserialize( $item['email_data'] );
		$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : [];
		$emails      = isset( $headers_arr['email_to'] ) ? $headers_arr['email_to'] : [];

		if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
			$formatted_emails = [];
			foreach ( $emails as $email ) {
				if ( $email['recipient_name'] != '' ) {
					$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email( $email['address'] ) . ')';
				} else {
					$formatted_emails[] = sanitize_email( $email['address'] );
				}
			}

			return implode( ',', $formatted_emails );
		}

		return '';
	}//end column_to

	/**
	 * Callback for column 'email_reply_to'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_email_reply_to( $item ) {
		$email_data  = maybe_unserialize( $item['email_data'] );
		$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : [];
		$emails      = isset( $headers_arr['email_reply_to'] ) ? $headers_arr['email_reply_to'] : [];

		if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
			$formatted_emails = [];
			foreach ( $emails as $email ) {
				if ( $email['recipient_name'] != '' ) {
					$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email( $email['address'] ) . ')';
				} else {
					$formatted_emails[] = sanitize_email( $email['address'] );
				}
			}

			return implode( ',', $formatted_emails );
		}

		return '';
	}//end column_to


	/**
	 * Callback for column 'email_cc'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_email_cc( $item ) {
		$email_data  = maybe_unserialize( $item['email_data'] );
		$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : [];
		$emails      = isset( $headers_arr['email_cc'] ) ? $headers_arr['email_cc'] : [];

		if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
			$formatted_emails = [];
			foreach ( $emails as $email ) {
				if ( $email['recipient_name'] != '' ) {
					$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email( $email['address'] ) . ')';
				} else {
					$formatted_emails[] = sanitize_email( $email['address'] );
				}
			}

			return implode( ',', $formatted_emails );
		}

		return '';
	}//end column_cc

	/**
	 * Callback for column 'email_bcc'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_email_bcc( $item ) {
		$email_data  = maybe_unserialize( $item['email_data'] );
		$headers_arr = isset( $email_data['headers_arr'] ) ? $email_data['headers_arr'] : [];
		$emails      = isset( $headers_arr['email_bcc'] ) ? $headers_arr['email_bcc'] : [];

		if ( is_array( $emails ) && sizeof( $emails ) > 0 ) {
			$formatted_emails = [];
			foreach ( $emails as $email ) {
				if ( $email['recipient_name'] != '' ) {
					$formatted_emails[] = $email['recipient_name'] . '(' . sanitize_email( $email['address'] ) . ')';
				} else {
					$formatted_emails[] = sanitize_email( $email['address'] );
				}
			}

			return implode( ',', $formatted_emails );
		}

		return '';

	}//end column_bcc


	/**
	 * Callback for column 'subject'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_subject( $item ) {
		$subject = esc_attr( wp_unslash( $item['subject'] ) );

		return $subject;
	}//end column_subject


	/**
	 * Callback for column 'attachment'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_attachment( $item ) {

		$id         = intval( $item['id'] );
		$email_data = maybe_unserialize( $item['email_data'] );

		$attachments = isset( $email_data['attachments'] ) ? $email_data['attachments'] : [];


		if ( is_array( $attachments ) && sizeof( $attachments ) > 0 ) {

			$setting = new CBXWPEmailLoggerSettings();;
			$enable_store_attachment = intval( $setting->get_option( 'enable_store_attachment', 'cbxwpemaillogger_log', 0 ) );
			if ( $enable_store_attachment ) {
				global $wp_filesystem;
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();

				$dir_info = CBXWPEmailLoggerHelper::checkUploadDir();

				$log_folder_dir = $dir_info['cbxwpemaillogger_base_dir'] . $id;

				if ( $wp_filesystem->exists( $log_folder_dir ) ) {

					$files = [];

					foreach ( $attachments as $attachment ) {

						if ( $wp_filesystem->exists( $log_folder_dir . '/' . $attachment ) ) {

							$attachment_download_url = admin_url( 'admin-ajax.php?file=' . $attachment . '&action=cbxwpemaillogger_download_attachment&log_id=' . $id );
							$attachment_download_url = wp_nonce_url( $attachment_download_url, 'cbxwpemaillogger', 'cbxwpemaillogger_nonce' );

							$mime_type       = wp_check_filetype( $log_folder_dir . '/' . $attachment );
							$mime_type_icon  = wp_mime_type_icon( $mime_type['type'] );
							$attachment_name = wp_basename( $log_folder_dir . '/' . $attachment );

							$attachment_url = '<a title="' . esc_attr( $attachment_name ) . '" href="' . esc_url( $attachment_download_url ) . '"><img src="' . $mime_type_icon . '" /></a>';

							$files[] = $attachment_url;
						} else {
							$files[] = $attachment;
						}

					}

					return implode( '<br/>', $files );
				} else {
					return implode( '<br/>', $attachments );
				}

			} else {
				return implode( '<br/>', $attachments );
			}
		}

		return '';
	}//end column_subject


	/**
	 * Callback for column 'status'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_status( $item ) {

		$status = isset( $item['status'] ) ? intval( $item['status'] ) : 0;

		return ( $status ) ? esc_html__( 'Sent', 'cbxwpemaillogger' ) : '<a title="' . esc_html__( 'See error message in details mode', 'cbxwpemaillogger' ) . '" href="' . admin_url( 'admin.php?page=cbxwpemaillogger&view=email&log_id=' ) . intval( $item['id'] ) . '">' . esc_html__( 'Failed', 'cbxwpemaillogger' ) . '</a>';
	}//end column_subject

	/**
	 * Display col 'ip_address' value
	 *
	 * @param $item
	 *
	 * @return string|void
	 */
	function column_ip_address( $item ) {
		$ip_address = isset( $item['ip_address'] ) ? $item['ip_address'] : '';

		return esc_attr( $ip_address );
	}//end column_ip_address


	/**
	 * Display col 'src_tracked' value
	 *
	 * @param $item
	 *
	 * @return string|void
	 */
	function column_src_tracked( $item ) {
		$src_tracked = isset( $item['src_tracked'] ) ? esc_attr( wp_unslash( $item['src_tracked'] ) ) : '';

		$sources = CBXWPEmailLoggerHelper::email_known_src();


		return isset( $sources[ $src_tracked ] ) ? esc_attr( $sources[ $src_tracked ] ) : esc_html__( 'Untraced', 'cbxwpemaillogger' );
	}//end column_ip_address

	/**
	 * Callback for column 'actions'
	 *
	 * @param  array  $item
	 *
	 * @return string
	 */
	function column_actions( $item ) {

		$actions = [];

		$actions[] = '<a class="button button-primary button-small cbxwpemaillogger_actions cbxwpemaillogger_actions_view" href="#">' . esc_html__( 'View', 'cbxwpemaillogger' ) . '</a>';

		$actions[] = '<a data-busy="0" data-id="' . intval( $item['id'] ) . '" class="button button-small cbxwpemaillogger_actions cbxwpemaillogger_actions_delete" href="#">' . esc_html__( 'Delete', 'cbxwpemaillogger' ) . '</a>';

		return implode( ' ', $actions );
	}//end column_subject


	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'date_created':
				return $item[ $column_name ];
			case 'email_from':
				return $item[ $column_name ];
			case 'email_to':
				return $item[ $column_name ];
			case 'email_reply_to':
				return $item[ $column_name ];
			case 'cc':
				return $item[ $column_name ];
			case 'bcc':
				return $item[ $column_name ];
			case 'subject':
				return $item[ $column_name ];
			case 'email_type':
				return $item[ $column_name ];
			case 'status':
				return $item[ $column_name ];
			case 'attachment':
				return $item[ $column_name ];
			case 'ip_address':
				return $item[ $column_name ];
			default:
				//return print_r( $item, true ); //Show the whole array for troubleshooting purposes
				echo apply_filters( 'cbxwpemaillogger_log_listing_column_default', $item, $column_name );
		}
	}//end column_default

	function get_columns() {
		$columns = [
			'cb'             => '<input type="checkbox" />', //Render a checkbox instead of text
			'date_created'   => esc_html__( 'Date', 'cbxwpemaillogger' ),
			'email_to'       => esc_html__( 'To', 'cbxwpemaillogger' ),
			'subject'        => esc_html__( 'Subject', 'cbxwpemaillogger' ),
			'email_type'     => esc_html__( 'Type', 'cbxwpemaillogger' ),
			'email_from'     => esc_html__( 'From', 'cbxwpemaillogger' ),
			'email_reply_to' => esc_html__( 'ReplyTo', 'cbxwpemaillogger' ),
			'email_cc'       => esc_html__( 'CC', 'cbxwpemaillogger' ),
			'email_bcc'      => esc_html__( 'BCC', 'cbxwpemaillogger' ),
			'status'         => esc_html__( 'Status', 'cbxwpemaillogger' ),
			'attachment'     => esc_html__( 'Attachments', 'cbxwpemaillogger' ),
			'ip_address'     => esc_html__( 'IP Address', 'cbxwpemaillogger' ),
			'src_tracked'    => esc_html__( 'Source', 'cbxwpemaillogger' ),
			//'actions'       => esc_html__( 'Actions', 'cbxwpemaillogger' )
		];

		return apply_filters( 'cbxwpemaillogger_log_listing_columns', $columns );
	}//end get_columns


	function get_sortable_columns() {
		$sortable_columns = [
			//'id'           => array( 'logs.id', false ), //true means it's already sorted
			'date_created' => [ 'logs.date_created', false ],
			//'email_from'    => array( 'logs.email_from', false ),
			//'email_to'      => array( 'logs.email_to', false ),
			'subject'      => [ 'logs.subject', false ],
			'email_type'   => [ 'logs.email_type', false ],
			'status'       => [ 'logs.status', false ],
			'ip_address'   => [ 'logs.ip_address', false ],
			'src_tracked'  => [ 'logs.src_tracked', false ],
		];

		return apply_filters( 'cbxwpemaillogger_log_listing_sortable_columns', $sortable_columns );
	}//end get_sortable_columns


	function get_bulk_actions() {
		$status_arr = CBXWPEmailLoggerHelper::StatusOptions();

		$bulk_actions = apply_filters( 'cbxwpemaillogger_action', $status_arr );

		return $bulk_actions;
	}//end get_bulk_actions

	function process_bulk_action() {

		$new_status = $this->current_action();

		if ( $new_status == - 1 ) {
			return;
		}

		global $wpdb;

		$table_cbxwpemaillogger = $wpdb->prefix . 'cbxwpemaillogger_log';

		if ( isset( $_REQUEST['delete_all'] ) && ! empty( $_REQUEST['delete_all'] ) ) {
			do_action( 'cbxwpemaillogger_log_all_delete_before' );

			$delete_all_status = $wpdb->query( "DELETE FROM $table_cbxwpemaillogger WHERE 1" );

			if ( $delete_all_status !== false ) {
				do_action( 'cbxwpemaillogger_log_all_delete_after' );
			}

		}//end delete all

		if ( isset( $_REQUEST['delete_all_old'] ) && ! empty( $_REQUEST['delete_all_old'] ) ) {

			$settings = new CBXWPEmailLoggerSettings();

			$delete_old_log = $settings->get_option( 'delete_old_log', 'cbxwpemaillogger_log', 'no' );

			if ( $delete_old_log == 'yes' ) {

				$log_old_days = intval( $settings->get_option( 'log_old_days', 'cbxwpemaillogger_log', '30' ) );

				if ( $log_old_days > 0 ) {

					CBXWPEmailLoggerHelper::delete_old_log( $log_old_days );

				}
			}

		}//end delete all


		//Detect when a bulk action is being triggered...
		if ( ! empty( $_REQUEST['cbxwpemaillogger'] ) ) {


			$results = $_REQUEST['cbxwpemaillogger'];
			foreach ( $results as $id ) {

				$id = intval( $id );

				if ( 'delete' === $new_status ) {
					do_action( 'cbxwpemaillogger_log_delete_before', $id );

					$delete_status = $wpdb->query( $wpdb->prepare( "DELETE FROM $table_cbxwpemaillogger WHERE id=%d", $id ) );

					if ( $delete_status !== false ) {
						do_action( 'cbxwpemaillogger_log_delete_after', $id );
					}
				}
			}
		}

		return;
	}//end process_bulk_action


	/**
	 * Prepare the review log items
	 */
	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		$user   = get_current_user_id();
		$screen = get_current_screen();

		$current_page = $this->get_pagenum();

		$option_name = $screen->get_option( 'per_page', 'option' ); //the core class name is WP_Screen

		$per_page = intval( get_user_meta( $user, $option_name, true ) );

		if ( $per_page == 0 ) {
			$per_page = intval( $screen->get_option( 'per_page', 'default' ) );
		}


		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();


		$this->_column_headers = [ $columns, $hidden, $sortable ];


		$this->process_bulk_action();


		$search      = isset( $_REQUEST['s'] ) ? esc_attr( wp_unslash( $_REQUEST['s'] ) ) : '';
		$logdate     = ( isset( $_REQUEST['logdate'] ) && $_REQUEST['logdate'] != '' ) ? sanitize_text_field( $_REQUEST['logdate'] ) : '';
		$emailsource = ( isset( $_REQUEST['emailsource'] ) && $_REQUEST['emailsource'] != '' ) ? sanitize_text_field( $_REQUEST['emailsource'] ) : '';
		$status      = isset( $_REQUEST['status'] ) ? intval( $_REQUEST['status'] ) : - 1;

		$order   = ( isset( $_REQUEST['order'] ) && $_REQUEST['order'] != '' ) ? sanitize_text_field( $_REQUEST['order'] ) : 'DESC';
		$orderby = ( isset( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'logs.id';


		$data        = CBXWPEmailLoggerHelper::getLogData( $search, $logdate, $emailsource, $status, $orderby, $order, $per_page, $current_page );
		$total_items = intval( CBXWPEmailLoggerHelper::getLogDataCount( $search, $logdate, $emailsource, $status ) );

		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( [
			'total_items' => $total_items,                      //WE have to calculate the total number of items
			'per_page'    => $per_page,                         //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
		] );

	}


	/**
	 * Generates content for a single row of the table
	 *
	 * @access public
	 *
	 * @param  object  $item  The current item
	 */
	public function single_row( $item ) {
		$row_class = 'cbxwpemaillogger_row';
		$row_class = apply_filters( 'cbxwpemaillogger_row_class', $row_class, $item );
		echo '<tr id="cbxwpemaillogger_row_' . $item['id'] . '" class="' . $row_class . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}//end single_row

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since  3.1.0
	 * @access public
	 */
	public function no_items() {
		echo '<div class="notice notice-warning inline "><p>' . esc_html__( 'No log found. Please change your search criteria for better result.', 'cbxwpemaillogger' ) . '</p></div>';
	}//end no_items

	/**
	 * @param  string  $which
	 */
	protected function extra_tablenav( $which ) {

		if ( 'top' !== $which ) {
			return;
		}
		?>
        <div class="actions">
			<?php
			/*if ( ! is_singular() ) {
				if ( ! $this->is_trash ) {
					$this->months_dropdown( 'attachment' );
				}

				//This action is documented in wp-admin/includes/class-wp-posts-list-table.php
				do_action( 'restrict_manage_posts', '', $which );

				submit_button( __( 'Filter' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
			}*/

			if ( $this->has_items() ) {
				submit_button( esc_html__( 'Delete All', 'cbxwpemaillogger' ), 'apply', 'delete_all', false );
				submit_button( esc_html__( 'Delete Old Logs', 'cbxwpemaillogger' ), 'apply', 'delete_all_old', false );
			} ?>
        </div>
		<?php
	}//end extra_tablenav


	/**
	 * @param  string  $which
	 */
	protected function pagination( $which ) {

		if ( empty( $this->_pagination_args ) ) {
			return;
		}

		$total_items     = $this->_pagination_args['total_items'];
		$total_pages     = $this->_pagination_args['total_pages'];
		$infinite_scroll = false;
		if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
			$infinite_scroll = $this->_pagination_args['infinite_scroll'];
		}

		if ( 'top' === $which && $total_pages > 1 ) {
			$this->screen->render_screen_reader_content( 'heading_pagination' );
		}

		$output = '<span class="displaying-num">' . sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

		$current              = $this->get_pagenum();
		$removable_query_args = wp_removable_query_args();

		$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

		$current_url = remove_query_arg( $removable_query_args, $current_url );

		$page_links = [];

		$total_pages_before = '<span class="paging-input">';
		$total_pages_after  = '</span></span>';

		$disable_first = $disable_last = $disable_prev = $disable_next = false;

		if ( $current == 1 ) {
			$disable_first = true;
			$disable_prev  = true;
		}
		if ( $current == 2 ) {
			$disable_first = true;
		}
		if ( $current == $total_pages ) {
			$disable_last = true;
			$disable_next = true;
		}
		if ( $current == $total_pages - 1 ) {
			$disable_last = true;
		}

		$search      = isset( $_REQUEST['s'] ) ? esc_attr( wp_unslash( $_REQUEST['s'] ) ) : '';
		$logdate     = ( isset( $_REQUEST['logdate'] ) && $_REQUEST['logdate'] != '' ) ? sanitize_text_field( $_REQUEST['logdate'] ) : '';
		$emailsource = ( isset( $_REQUEST['emailsource'] ) && $_REQUEST['emailsource'] != '' ) ? sanitize_text_field( $_REQUEST['emailsource'] ) : '';
		$status      = isset( $_REQUEST['status'] ) ? intval( $_REQUEST['status'] ) : - 1;

		$pagination_params = [];

		if ( $search != '' ) {
			$pagination_params['s'] = $search;
		}

		if ( $logdate != '' ) {
			$pagination_params['logdate'] = $logdate;
		}

		if ( $emailsource != '' ) {
			$pagination_params['emailsource'] = $emailsource;
		}

		if ( $status != - 1 ) {
			$pagination_params['status'] = $status;
		}


		$pagination_params = apply_filters( 'cbxwpemaillogger_log_pagination_params', $pagination_params );

		if ( $disable_first ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
		} else {
			$page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				__( 'First page', 'cbxwpemaillogger' ),
				'&laquo;'
			);
		}

		if ( $disable_prev ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
		} else {
			$pagination_params['paged'] = max( 1, $current - 1 );

			$page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( $pagination_params, $current_url ) ),
				__( 'Previous page' ),
				'&lsaquo;'
			);
		}

		if ( 'bottom' === $which ) {
			$html_current_page  = $current;
			$total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">';
		} else {
			$html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' /><span class='tablenav-paging-text'>",
				'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
				$current,
				strlen( $total_pages )
			);
		}
		$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
		$page_links[]     = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;

		if ( $disable_next ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
		} else {
			$pagination_params['paged'] = min( $total_pages, $current + 1 );

			$page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( $pagination_params, $current_url ) ),
				__( 'Next page' ),
				'&rsaquo;'
			);
		}

		if ( $disable_last ) {
			$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
		} else {
			$pagination_params['paged'] = $total_pages;

			$page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
				esc_url( add_query_arg( $pagination_params, $current_url ) ),
				__( 'Last page' ),
				'&raquo;'
			);
		}

		$pagination_links_class = 'pagination-links';
		if ( ! empty( $infinite_scroll ) ) {
			$pagination_links_class = ' hide-if-js';
		}
		$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

		if ( $total_pages ) {
			$page_class = $total_pages < 2 ? ' one-page' : '';
		} else {
			//$page_class = ' no-pages';
			$page_class = ' ';
		}
		$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

		echo $this->_pagination;
	}//end pagination

	/**
	 * Displays the search box.
	 *
	 * @param  string  $text  The 'submit' button label.
	 * @param  string  $input_id  ID attribute value for the search input field.
	 *
	 * @since 3.1.0
	 *
	 */
	public function search_box( $text, $input_id ) {
		/*if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
			return;
		}*/

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['post_mime_type'] ) ) {
			echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['detached'] ) ) {
			echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
		}
		?>
        <div class="search-box">
            <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>"/>
			<?php submit_button( $text, '', '', false, [ 'id' => 'search-submit' ] ); ?>
        </div>
		<?php
	}//end search_box

}//end class CBXWPEmailLogger_List_Table