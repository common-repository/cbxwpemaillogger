<?php
/**
 * Provide a settings view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://codeboxr.com
 * @since      1.0.0
 *
 * @package    Cbxform
 * @subpackage Cbxform/admin/templates
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
    <div class="cbx-backend_container_header">
        <div class="cbx-backend_wrapper cbx-backend_header_wrapper">
            <div class="menu-heading">
                <img title="CBX Email logger - Settings" src="<?php echo CBXWPEMAILLOGGER_ROOT_URL . 'assets/images/icon_log.svg'
				?>" alt="CBX Email logger - Settings" width="32" height="32">
                <h2 class="wp-heading-inline wp-heading-inline-setting">
					<?php esc_html_e( 'CBX Email logger - Email Testing', 'cbxwpemaillogger' ); ?>
                </h2>
            </div>
            <div class="setting_tool">
                <a class="button button-primary  save-settings cbxwpemaillogger_logs_btn" href="<?php echo esc_url( admin_url( 'admin.php?page=cbxwpemaillogger' ) ); ?>"><?php esc_html_e( 'Log Listing', 'cbxwpemaillogger' ); ?><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAARUlEQVRIie3PoQoAIBRD0eGPa3x/bJxF0GLbgrBThd0nEBFSJMsdoDXCo14PSgUAzfYdYMoXr+u7fPwKeMZ3YNjGIz62APJohRdPxDxbAAAAAElFTkSuQmCC"/></a>
                <a class="button button-secondary  save-settings cbxwpemaillogger_logs_btn" href="<?php echo esc_url( admin_url( 'admin.php?page=cbxwpemailloggersettings' ) ); ?>"><?php esc_html_e( 'Global Setting', 'cbxwpemaillogger' ); ?><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAAARUlEQVRIie3PoQoAIBRD0eGPa3x/bJxF0GLbgrBThd0nEBFSJMsdoDXCo14PSgUAzfYdYMoXr+u7fPwKeMZ3YNjGIz62APJohRdPxDxbAAAAAElFTkSuQmCC"/></a>
            </div>

        </div>
    </div>

    <div class="cbx-backend_container">
        <div class="cbx-backend_wrapper cbx-backend_setting_wrapper">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-1">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <div class="postbox">
                                <!--<h3><span><?php esc_html_e( 'Settings', 'cbxwpemaillogger' ); ?></span></h3>-->
                                <div class="inside">
									<?php
									$submission_msg = get_option( 'cbxwpemaillogger_testmsg' );
									if ( $submission_msg ):
										delete_option( 'cbxwpemaillogger_testmsg' );

										$msg   = isset( $submission_msg['message'] ) ? $submission_msg['message'] : '';
										$class = ( isset( $submission_msg['type'] ) && intval( $submission_msg['type'] ) == 1 ) ? 'notice-success inline' : 'notice-error inline';
										?>
                                        <div class="notice <?php echo esc_attr( $class ); ?>">
                                            <p>
												<?php
												echo $msg;
												?>
                                            </p>
                                        </div>
									<?php
									endif;

									$current_user = wp_get_current_user();

									?>
                                    <form class="emailtesting-form" action="<?php echo esc_url( admin_url( 'admin.php?page=cbxwpemailloggeremailtesting' ) ); ?>" method="post" enctype="multipart/form-data">
                                        <div class="emailtesting-form-input">
                                            <label for="emailtesting-form-to"><?php esc_html_e( 'To', 'cbxwpemaillogger' ); ?></label>
                                            <input required placeholder="<?php esc_html_e( 'To email', 'cbxwpemaillogger' ); ?>" type="email" name="to" value="<?php echo esc_attr( $current_user->user_email ); ?>" class="regular-text" id="emailtesting-form-to"/>
                                        </div>
                                        <div class="emailtesting-form-input emailtesting-form-input-subject">
                                            <label for="emailtesting-form-subject"><?php esc_html_e( 'Subject', 'cbxwpemaillogger' ); ?></label>
                                            <input required placeholder="<?php esc_html_e( 'Email Subject', 'cbxwpemaillogger' ); ?>" type="text" name="subject" value="<?php esc_attr_e( 'Test Email Subject', 'cbxwpemaillogger' ); ?>" class="regular-text" id="emailtesting-form-subject"/>
                                        </div>
                                        <div class="emailtesting-form-input emailtesting-form-input-message">
                                            <label for="emailtesting-form-message"><?php esc_html_e( 'Message', 'cbxwpemaillogger' ); ?></label>
                                            <textarea required placeholder="<?php esc_html_e( 'Email Body Test', 'cbxwpemaillogger' ); ?>" name="message" id="emailtesting-form-message" cols="30" rows="10"><?php esc_attr_e( 'Test Email Body Text', 'cbxwpemaillogger' ); ?></textarea>
                                        </div>
                                        <div class="emailtesting-form-input emailtesting-form-input-file">
                                            <label for="emailtesting-form-message"><?php esc_html_e( 'File', 'cbxwpemaillogger' ); ?></label>
                                            <input type="file" name="file">
                                            <p><?php esc_html_e( 'Note: File will be deleted after sending email', 'cbxwpemaillogger' ); ?></p>
                                        </div>
                                        <input type="hidden" name="cbxwpemaillogger-email-testing" value="1"/>
                                        <input type="hidden" name="cbxwpemaillogger-security" value="<?php echo wp_create_nonce( 'cbxwpemaillogger' ); ?>"/>
                                        <p>
                                            <input type="submit" class="button-primary" value="<?php esc_html_e( 'Send Test Email', 'cbxwpemaillogger' ); ?>"/>
                                        </p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>