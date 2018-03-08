<?php

/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// DECLARE PLUGIN OPTIONS
// Post Settings
$option_sidebar = 'idivi_post_settings_sidebar';
$option_dot = 'idivi_post_settings_dot';
$option_scroll = 'idivi_post_settings_before_scroll';
$option_title = 'idivi_post_settings_post_title';
$post_options_remember = 'idivi_post_settings_last_used';

//Page Settings
$option_page_sidebar = 'idivi_page_settings_sidebar';
$option_page_dot = 'idivi_page_settings_dot';
$option_page_scroll = 'idivi_page_settings_before_scroll';
$page_options_remember = 'idivi_page_settings_last_used';

//Project Settings
$option_project_sidebar = 'idivi_project_settings_sidebar';
$option_project_dot = 'idivi_project_settings_dot';
$option_project_scroll = 'idivi_project_settings_before_scroll';
$option_project_nav = 'idivi_project_settings_nav';
$project_options_remember = 'idivi_project_settings_last_used';

//DELETE PLUGIN OPTIONS
//Post Settings
delete_option($option_sidebar);
delete_option($option_dot);
delete_option($option_scroll);
delete_option($option_title);
delete_option($post_options_remember);
//Page Settings
delete_option($option_page_sidebar);
delete_option($option_page_dot);
delete_option($option_page_scroll);
delete_option($page_options_remember);
//Project Settings
delete_option($option_project_sidebar);
delete_option($option_project_dot);
delete_option($option_project_scroll);
delete_option($option_project_nav);
delete_option($project_options_remember);

// drop a custom database row.
global $wpdb;
$user_id = get_current_user_id();
$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'wp_idivi-dismiss',
                                       'user_id' => $user_id )
                                     );

?>
