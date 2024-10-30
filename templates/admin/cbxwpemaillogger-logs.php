<?php
/**
 * Provide a dashboard rating log listing
 *
 * This file is used to markup the admin-facing rating log listing
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    cbxwpemaillogger
 * @subpackage cbxwpemaillogger/templates
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

$cbxwpemaillogger_logs = new CBXWPEmailLogger_List_Table();

//Fetch, prepare, sort, and filter log data
$cbxwpemaillogger_logs->prepare_items();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
		<?php esc_html_e( 'Email Logs', 'cbxwpemaillogger' ); ?>
    </h1>
    <p>
        <a class="button button-primary  cbxwpemaillogger_logs_btn" href="<?php echo esc_url( admin_url( 'admin.php?page=cbxwpemailloggersettings' ) ); ?>"><?php esc_html_e( 'Global Setting', 'cbxwpemaillogger' ); ?></a>
        <a class="button button-secondary  cbxwpemaillogger_logs_btn" href="<?php echo esc_url( admin_url( 'admin.php?page=cbxwpemailloggeremailtesting' ) ); ?>"><?php esc_html_e( 'Email Testing', 'cbxwpemaillogger' ); ?></a>
    </p>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
                            <form id="cbxwpemaillogger_logs" method="post">
								<?php $cbxwpemaillogger_logs->views(); ?>

                                <input type="hidden" name="page" value="<?php echo esc_attr( wp_unslash( $_REQUEST['page'] ) ); ?>"/>
                                <div class="search-box-filters">
									<?php


									$sources    = CBXWPEmailLoggerHelper::email_known_src();
									$current_sc = isset( $_REQUEST['emailsource'] ) ? esc_attr( wp_unslash( $_REQUEST['emailsource'] ) ) : '';
									$status     = isset( $_REQUEST['status'] ) ? intval( $_REQUEST['status'] ) : - 1;
									?>
                                    <div class="search-box">
                                        <select id="cbxscratingreviewlog-status-input" name="status" placeholder="<?php esc_html_e( 'Filter By Status', 'cbxwpemaillogger' ); ?>">
                                            <option value="-1" <?php selected( $status, - 1, true ); ?> ><?php esc_html_e( 'Filter By Status', 'cbxwpemaillogger' ); ?></option>
                                            <option value="1" <?php selected( $status, 1, true ); ?> ><?php esc_html_e( 'Sent', 'cbxwpemaillogger' ); ?></option>
                                            <option value="0" <?php selected( $status, 0, true ); ?> ><?php esc_html_e( 'Failed', 'cbxwpemaillogger' ); ?></option>
                                        </select>

                                        <input autocomplete="new-password" type="text" id="cbxscratingreviewlog-logdate-input" name="logdate" value="<?php echo isset( $_REQUEST['logdate'] ) ? esc_attr( wp_unslash( $_REQUEST['logdate'] ) ) : ''; ?>" placeholder="<?php esc_html_e( 'Date', 'cbxwpemaillogger' ); ?>"/>
                                    </div>
                                    <div class="search-box">
                                        <select id="cbxscratingreviewlog-source-input" name="emailsource" placeholder="<?php esc_html_e( 'Filter By Email Source', 'cbxwpemaillogger' ); ?>">
                                            <option value="" <?php selected( $current_sc, '', true ); ?> ><?php esc_html_e( 'Filter By Email Source', 'cbxwpemaillogger' ); ?></option>
											<?php foreach ( $sources as $source_key => $source_name ): ?>
                                                <option value="<?php echo $source_key; ?>" <?php selected( $current_sc, $source_key, true ); ?> ><?php echo esc_attr( ucfirst( $source_name ) ); ?></option>
											<?php endforeach; ?>
                                        </select>
                                    </div>
									<?php
									$cbxwpemaillogger_logs->search_box( esc_html__( 'Search', 'cbxwpemaillogger' ), 'cbxscratingreviewlogsearch' );
									?>
                                </div>

								<?php $cbxwpemaillogger_logs->display() ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear clearfix"></div>
    </div>
</div>