<?php
/*
 * Plugin Name: i-Divi Post Settings
 * Plugin URI:  http://www.howidivit.com/divi_post_settings
 * Description: The plugin add some fields in Divi Theme Customizer from which you can set your favorite default post, page and project settings.
 * Author:      Dan Mardis - Howidivit.com
 * Version:     1.1
 * Author URI:  http://www.howidivit.com
 */

 // Prevent file from being loaded directly
 if ( ! defined( 'WPINC' ) ) {
 	die( 'Sorry. No sufficient permissions.' );
 }

 /**
  * The code that runs during plugin activation.
  */
 function activate_idivi_post_settings() {
 	require_once plugin_dir_path( __FILE__ ) . 'includes/class-divi-post-settings-activator.php';
 	idivi_post_settings_Activator::activate();
 }

 /**
  * The code that runs during plugin deactivation.
  */
  function deactivate_idivi_post_settings() {
  	require_once plugin_dir_path( __FILE__ ) . 'includes/class-divi-post-settings-deactivator.php';
  	idivi_post_settings_Deactivator::deactivate();
  }

 register_activation_hook( __FILE__, 'activate_idivi_post_settings' );
 register_deactivation_hook( __FILE__, 'deactivate_idivi_post_settings' );


/**
 * The core plugin class that is used to define admin-specific hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-divi-post-settings.php';

/**
 * Adding the custom Metaboxes.
 */
function idivi_add_custom_metabox() {

  // Add Page settings meta box only if it's not disabled for current user
  $current_theme = wp_get_theme();
  if ( 'Divi' === $current_theme->get( 'Name' ) || 'Divi' === $current_theme->get( 'Template' ) ) {
    add_meta_box( 'et_settings_meta_box', esc_html__( 'Divi Page Settings', 'Divi' ), 'idivi_single_settings_meta_box', 'page', 'side', 'high' );
    add_meta_box( 'et_settings_meta_box', esc_html__( 'Divi Post Settings', 'Divi' ), 'idivi_single_settings_meta_box', 'post', 'side', 'high' );
    add_meta_box( 'et_settings_meta_box', esc_html__( 'Divi Product Settings', 'Divi' ), 'idivi_single_settings_meta_box', 'product', 'side', 'high' );
    add_meta_box( 'et_settings_meta_box', esc_html__( 'Divi Project Settings', 'Divi' ), 'idivi_single_settings_meta_box', 'project', 'side', 'high' );
  }
}
add_action( 'admin_init', 'idivi_add_custom_metabox' );

/*
 * Rewrite the settings meta box Divi function according to the Theme Customizer options
 */
 function idivi_single_settings_meta_box( $post ) {
   global $plugin_name, $version;
   $post_id = get_the_ID();

   $post_layout_opt = get_option( 'idivi_post_settings_sidebar' );
   $page_layout_opt = get_option( 'idivi_page_settings_sidebar' );
   $project_layout_opt = get_option( 'idivi_project_settings_sidebar' );

   $dot_nav_opt = get_option( 'idivi_post_settings_dot' );
   $dot_page_nav_opt = get_option( 'idivi_page_settings_dot' );
   $dot_project_nav_opt = get_option( 'idivi_project_settings_dot' );

   $before_scroll_opt = get_option( 'idivi_post_settings_before_scroll' );
   $before_page_scroll_opt = get_option( 'idivi_page_settings_before_scroll' );
   $before_project_scroll_opt = get_option( 'idivi_project_settings_before_scroll' );

   $title_opt = get_option( 'idivi_post_settings_post_title' );
   $project_navigation = get_option( 'idivi_project_settings_nav' );

   $last_used = get_option('idivi_post_settings_last_used');
   $last_page_used = get_option('idivi_page_settings_last_used');
   $last_project_used = get_option('idivi_project_settings_last_used');

   // call admin class function for getting last post id
   $last_post_used = new idivi_post_settings_Admin($plugin_name, $version);
   $last_post_id = $last_post_used->get_last_post_id();
   $last_page_id = $last_post_used->get_last_page_id();
   $last_project_id = $last_post_used->get_last_project_id();

   // retrieve last post values
   $last_post_layout = get_post_meta( $last_post_id, '_et_pb_page_layout', true );
   $last_side_nav = get_post_meta( $last_post_id, '_et_pb_side_nav', true );
   $last_post_hide_nav = get_post_meta( $last_post_id, '_et_pb_post_hide_nav', true );
   $last_show_title = get_post_meta( $last_post_id, '_et_pb_show_title', true );

   $last_page_layout = get_post_meta( $last_page_id, '_et_pb_page_layout', true );
   $last_page_side_nav = get_post_meta( $last_page_id, '_et_pb_side_nav', true );
   $last_page_hide_nav = get_post_meta( $last_page_id, '_et_pb_post_hide_nav', true );

   $last_project_layout = get_post_meta( $last_project_id, '_et_pb_page_layout', true );
   $last_project_side_nav = get_post_meta( $last_project_id, '_et_pb_side_nav', true );
   $last_project_hide_nav = get_post_meta( $last_project_id, '_et_pb_post_hide_nav', true );

   $last_project_nav = get_post_meta( $last_project_id, '_et_pb_project_nav', true );

   wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );

   $page_layout = get_post_meta( $post_id, '_et_pb_page_layout', true );
   $side_nav = get_post_meta( $post_id, '_et_pb_side_nav', true );
   $project_nav = get_post_meta( $post_id, '_et_pb_project_nav', true );
   $post_hide_nav = get_post_meta( $post_id, '_et_pb_post_hide_nav', true );
   $post_hide_nav = $post_hide_nav && 'off' === $post_hide_nav ? 'default' : $post_hide_nav;
   $show_title = get_post_meta( $post_id, '_et_pb_show_title', true );

if ( 'post' === $post->post_type ) {
   if (
       ( !$post_layout_opt && (!isset($last_used) || empty($last_used) ) ) ||
       ( $post_layout_opt === 'Right' && (!isset($last_used) || empty($last_used)) ) ||
     ( ( isset($last_used) && ($last_used == 1) ) && $last_post_layout === 'et_right_sidebar')
   ) {
    if ( is_rtl() ) {
     $page_layouts = array(
      'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
      'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
      'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
      );
    } else {
     $page_layouts = array(
      'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
      'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
      'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
      );
    }
   } else if (
   ( $post_layout_opt === 'Left' && (!isset($last_used) || empty($last_used)) ) ||
   ( ( isset($last_used) && ($last_used == 1) ) && $last_post_layout === 'et_left_sidebar')
   ) {
    if ( is_rtl() ) {
     $page_layouts = array(
      'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
      'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
      'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
      );
    } else {
     $page_layouts = array(
      'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
      'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
      'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
      );
    }
   } else {
    if ( is_rtl() ) {
     $page_layouts = array(
      'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
      'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
      'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
      );
    } else {
     $page_layouts = array(
      'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
      'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
      'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
      );
    }
   }
 } else if ( 'page' === $post->post_type ) {
    if (
        ( !$page_layout_opt && (!isset($last_page_used) || empty($last_page_used) ) ) ||
        ( $page_layout_opt === 'Right' && (!isset($last_page_used) || empty($last_page_used)) ) ||
      ( ( isset($last_page_used) && ($last_page_used == 1) ) && $last_page_layout === 'et_right_sidebar')
    ) {
     if ( is_rtl() ) {
      $page_layouts = array(
       'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
       'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
       'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
       );
     } else {
      $page_layouts = array(
       'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
       'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
       'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
       );
     }
    } else if (
    ( $page_layout_opt === 'Left' && (!isset($last_page_used) || empty($last_page_used)) ) ||
    ( ( isset($last_page_used) && ($last_page_used == 1) ) && $last_page_layout === 'et_left_sidebar')
    ) {
     if ( is_rtl() ) {
      $page_layouts = array(
       'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
       'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
       'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
       );
     } else {
      $page_layouts = array(
       'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
       'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
       'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
       );
     }
    } else {
     if ( is_rtl() ) {
      $page_layouts = array(
       'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
       'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
       'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
       );
     } else {
      $page_layouts = array(
       'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
       'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
       'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
       );
     }
    }
  } else if ( 'project' === $post->post_type ) {
     if (
         ( !$project_layout_opt && (!isset($last_project_used) || empty($last_project_used) ) ) ||
         ( $project_layout_opt === 'Right' && (!isset($last_project_used) || empty($last_project_used)) ) ||
       ( ( isset($last_project_used) && ($last_project_used == 1) ) && $last_project_layout === 'et_right_sidebar')
     ) {
      if ( is_rtl() ) {
       $page_layouts = array(
        'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
        'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
        'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
        );
      } else {
       $page_layouts = array(
        'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
        'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
        'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
        );
      }
     } else if (
     ( $project_layout_opt === 'Left' && (!isset($last_project_used) || empty($last_project_used)) ) ||
     ( ( isset($last_project_used) && ($last_project_used == 1) ) && $last_project_layout === 'et_left_sidebar')
     ) {
      if ( is_rtl() ) {
       $page_layouts = array(
        'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
        'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
        'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
        );
      } else {
       $page_layouts = array(
        'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
        'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
        'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
        );
      }
     } else {
      if ( is_rtl() ) {
       $page_layouts = array(
        'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
        'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
        'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
        );
      } else {
       $page_layouts = array(
        'et_full_width_page' => esc_html__( 'Fullwidth', 'Divi' ),
        'et_right_sidebar' => esc_html__( 'Right Sidebar', 'Divi' ),
        'et_left_sidebar' => esc_html__( 'Left Sidebar', 'Divi' ),
        );
      }
     }
   }
   $layouts = array(
    'light' => esc_html__( 'Light', 'Divi' ),
    'dark' => esc_html__( 'Dark', 'Divi' ),
    );
   $post_bg_color = ( $bg_color = get_post_meta( $post_id, '_et_post_bg_color', true ) ) && '' !== $bg_color
   ? $bg_color
   : '#ffffff';
   $post_use_bg_color = get_post_meta( $post_id, '_et_post_use_bg_color', true )
   ? true
   : false;
   $post_bg_layout = ( $layout = get_post_meta( $post_id, '_et_post_bg_layout', true ) ) && '' !== $layout
   ? $layout
   : 'light'; ?>

    <p class="et_pb_page_settings et_pb_page_layout_settings">
    <label for="et_pb_page_layout" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Page Layout', 'Divi' ); ?>: </label>

    <select id="et_pb_page_layout" name="et_pb_page_layout">
    <?php
    foreach ( $page_layouts as $layout_value => $layout_name ) {
    printf( '<option value="%2$s"%3$s>%1$s</option>',
    esc_html( $layout_name ),
    esc_attr( $layout_value ),
    selected( $layout_value, $page_layout, false )
     );
    } ?>
    </select>
    </p>

    <p class="et_pb_page_settings et_pb_side_nav_settings" style="display: none;">
    <label for="et_pb_side_nav" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Dot Navigation', 'Divi' ); ?>: </label>
<?php
if ( 'post' === $post->post_type ) { ?>
    <?php if (
      ( !$dot_nav_opt && (!isset($last_used) || empty($last_used) ) ) ||
      ( $dot_nav_opt === 'Off' && (!isset($last_used) || empty($last_used) ) ) ||
      (( isset($last_used) && ($last_used == 1) ) && $last_side_nav === 'off')
    ) { ?>
    <select id="et_pb_side_nav" name="et_pb_side_nav">
    <option value="off" <?php selected( 'off', $side_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
    <option value="on" <?php selected( 'on', $side_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
    </select>
    <?php } else { ?>
     <select id="et_pb_side_nav" name="et_pb_side_nav">
     <option value="on" <?php selected( 'on', $side_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
     <option value="off" <?php selected( 'off', $side_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
     </select>
    <?php }
  } else if ( 'page' === $post->post_type ) {
    if (
      ( !$dot_page_nav_opt && (!isset($last_page_used) || empty($last_page_used) ) ) ||
      ( $dot_page_nav_opt === 'Off' && (!isset($last_page_used) || empty($last_page_used) ) ) ||
      (( isset($last_page_used) && ($last_page_used == 1) ) && $last_page_side_nav === 'off')
    ) { ?>
    <select id="et_pb_side_nav" name="et_pb_side_nav">
    <option value="off" <?php selected( 'off', $side_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
    <option value="on" <?php selected( 'on', $side_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
    </select>
    <?php } else { ?>
     <select id="et_pb_side_nav" name="et_pb_side_nav">
     <option value="on" <?php selected( 'on', $side_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
     <option value="off" <?php selected( 'off', $side_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
     </select>
 <?php }
} else if ( 'project' === $post->post_type ) {
   if (
  ( !$dot_project_nav_opt && (!isset($last_project_used) || empty($last_project_used) ) ) ||
  ( $dot_project_nav_opt === 'Off' && (!isset($last_project_used) || empty($last_project_used) ) ) ||
  (( isset($last_project_used) && ($last_project_used == 1) ) && $last_project_side_nav === 'off')
) { ?>
<select id="et_pb_side_nav" name="et_pb_side_nav">
<option value="off" <?php selected( 'off', $side_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
<option value="on" <?php selected( 'on', $side_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
</select>
<?php } else { ?>
 <select id="et_pb_side_nav" name="et_pb_side_nav">
 <option value="on" <?php selected( 'on', $side_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
 <option value="off" <?php selected( 'off', $side_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
 </select>
<?php }
 } ?>

    </p>
    <p class="et_pb_page_settings">
    <label for="et_pb_post_hide_nav" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Hide Nav Before Scroll', 'Divi' ); ?>: </label>
<?php
if ( 'post' === $post->post_type ) {
   if (
      ( !$before_scroll_opt && (!isset($last_used) || empty($last_used) ) ) ||
      ( $before_scroll_opt === 'Default' && (!isset($last_used) || empty($last_used) ) ) ||
      (( isset($last_used) && ($last_used == 1) ) && $last_post_hide_nav === 'default')
   ) { ?>
    <select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
    <option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
    <option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
    <option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
    </select>
    <?php } else if (
      ( $before_scroll_opt === 'Off' && (!isset($last_used) || empty($last_used)) ) ||
      ( ( isset($last_used) && ($last_used == 1) ) && $last_post_hide_nav === 'no')
    ) { ?>
     <select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
     <option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
     <option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
     <option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
     </select>
    <?php } else { ?>
     <select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
     <option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
     <option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
     <option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
     </select>
   <?php }
 } else if ( 'page' === $post->post_type ) {
     if (
        ( !$before_page_scroll_opt && (!isset($last_page_used) || empty($last_page_used) ) ) ||
        ( $before_page_scroll_opt === 'Default' && (!isset($last_page_used) || empty($last_page_used) ) ) ||
        (( isset($last_page_used) && ($last_page_used == 1) ) && $last_page_hide_nav === 'default')
     ) { ?>
      <select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
      <option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
      <option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
      <option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
      </select>
      <?php } else if (
        ( $before_page_scroll_opt === 'Off' && (!isset($last_page_used) || empty($last_page_used)) ) ||
        ( ( isset($last_page_used) && ($last_page_used == 1) ) && $last_page_hide_nav === 'no')
      ) { ?>
       <select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
       <option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
       <option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
       <option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
       </select>
      <?php } else { ?>
       <select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
       <option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
       <option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
       <option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
       </select>
<?php }
 } else if ( 'project' === $post->post_type ) {
     if (
        ( !$before_project_scroll_opt && (!isset($last_project_used) || empty($last_project_used) ) ) ||
        ( $before_project_scroll_opt === 'Default' && (!isset($last_project_used) || empty($last_project_used) ) ) ||
        (( isset($last_project_used) && ($last_project_used == 1) ) && $last_project_hide_nav === 'default')
     ) { ?>
      <select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
      <option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
      <option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
      <option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
      </select>
      <?php } else if (
        ( $before_project_scroll_opt === 'Off' && (!isset($last_project_used) || empty($last_project_used)) ) ||
        ( ( isset($last_project_used) && ($last_project_used == 1) ) && $last_project_hide_nav === 'no')
      ) { ?>
       <select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
       <option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
       <option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
       <option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
       </select>
      <?php } else { ?>
       <select id="et_pb_post_hide_nav" name="et_pb_post_hide_nav">
       <option value="on" <?php selected( 'on', $post_hide_nav ); ?>><?php esc_html_e( 'On', 'Divi' ); ?></option>
       <option value="default" <?php selected( 'default', $post_hide_nav ); ?>><?php esc_html_e( 'Default', 'Divi' ); ?></option>
       <option value="no" <?php selected( 'no', $post_hide_nav ); ?>><?php esc_html_e( 'Off', 'Divi' ); ?></option>
       </select>
<?php
   }
 }
      ?>
    </p>

<?php if ('post' === $post->post_type) { ?>
    <p class="et_pb_page_settings et_pb_single_title" style="display: none;">
    <label for="et_single_title" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Post Title', 'Divi' ); ?>: </label>

    <?php if (
      ( !$title_opt && (!isset($last_used) || empty($last_used) ) )  ||
      ( $title_opt === 'Show' && (!isset($last_used) || empty($last_used) ) ) ||
      (( isset($last_used) && ($last_used == 1) ) && $last_show_title === 'on')
    ) { ?>
    <select id="et_single_title" name="et_single_title">
    <option value="on" <?php selected( 'on', $show_title ); ?>><?php esc_html_e( 'Show', 'Divi' ); ?></option>
    <option value="off" <?php selected( 'off', $show_title ); ?>><?php esc_html_e( 'Hide', 'Divi' ); ?></option>
    </select>
    <?php } else { ?>
     <select id="et_single_title" name="et_single_title">
     <option value="off" <?php selected( 'off', $show_title ); ?>><?php esc_html_e( 'Hide', 'Divi' ); ?></option>
     <option value="on" <?php selected( 'on', $show_title ); ?>><?php esc_html_e( 'Show', 'Divi' ); ?></option>
     </select>
    <?php } ?>
    </p>

    <p class="et_divi_quote_settings et_divi_audio_settings et_divi_link_settings et_divi_format_setting et_pb_page_settings">
    <label for="et_post_use_bg_color" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Use Background Color', 'Divi' ); ?></label>
    <input name="et_post_use_bg_color" type="checkbox" id="et_post_use_bg_color" <?php checked( $post_use_bg_color ); ?> />
    </p>

    <p class="et_post_bg_color_setting et_divi_format_setting et_pb_page_settings">
    <input id="et_post_bg_color" name="et_post_bg_color" class="color-picker-hex" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'Divi' ); ?>" value="<?php echo esc_attr( $post_bg_color ); ?>" data-default-color="#ffffff" />
    </p>

    <p class="et_divi_quote_settings et_divi_audio_settings et_divi_link_settings et_divi_format_setting">
    <label for="et_post_bg_layout" style="font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Text Color', 'Divi' ); ?>: </label>
    <select id="et_post_bg_layout" name="et_post_bg_layout">
    <?php
    foreach ( $layouts as $layout_name => $layout_title )
    printf( '<option value="%s"%s>%s</option>',
    esc_attr( $layout_name ),
    selected( $layout_name, $post_bg_layout, false ),
    esc_html( $layout_title )
     );
    ?>
    </select>
    </p>
    <?php
}

    if ( 'project' === $post->post_type ) : ?>

    <p class="et_pb_page_settings et_pb_project_nav" style="display: none;">
    <label for="et_project_nav" style="display: block; font-weight: bold; margin-bottom: 5px;"><?php esc_html_e( 'Project Navigation', 'Divi' ); ?>: </label>
    <?php
    if (
      ( !$project_navigation && (!isset($last_project_used) || empty($last_project_used) ) ) ||
      ( $project_navigation === 'Hide' && (!isset($last_project_used) || empty($last_project_used) ) ) ||
      (( isset($last_project_used) && ($last_project_used == 1) ) && $last_project_nav === 'off')
    ) { ?>
    <select id="et_project_nav" name="et_project_nav">
    <option value="off" <?php selected( 'off', $project_nav ); ?>><?php esc_html_e( 'Hide', 'Divi' ); ?></option>
    <option value="on" <?php selected( 'on', $project_nav ); ?>><?php esc_html_e( 'Show', 'Divi' ); ?></option>
    </select>
    <?php } else { ?>
      <select id="et_project_nav" name="et_project_nav">
      <option value="on" <?php selected( 'on', $project_nav ); ?>><?php esc_html_e( 'Show', 'Divi' ); ?></option>
      <option value="off" <?php selected( 'off', $project_nav ); ?>><?php esc_html_e( 'Hide', 'Divi' ); ?></option>
      </select>
    </p>
  <?php }
endif;
   }

  /*
   * Save the Theme Customizer options
   */
  function idivi_divi_post_settings_save_details( $post_id, $post ){
     global $pagenow;

     if ( 'post.php' !== $pagenow || ! $post || ! is_object( $post ) ) {
      return;
     }

     if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return;
     }

     $post_type = get_post_type_object( $post->post_type );
      if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
      return;
     }

     if ( ! isset( $_POST['et_settings_nonce'] ) || ! wp_verify_nonce(    $_POST['et_settings_nonce'], basename( __FILE__ ) ) ) {
      return;
     }

    if ( isset( $_POST['et_post_use_bg_color'] ) )
      update_post_meta( $post_id, '_et_post_use_bg_color', true );
    else
      delete_post_meta( $post_id, '_et_post_use_bg_color' );

    if ( isset( $_POST['et_post_bg_color'] ) )
      update_post_meta( $post_id, '_et_post_bg_color', sanitize_text_field( $_POST['et_post_bg_color'] ) );
    else
      delete_post_meta( $post_id, '_et_post_bg_color' );

    if ( isset( $_POST['et_post_bg_layout'] ) )
      update_post_meta( $post_id, '_et_post_bg_layout', sanitize_text_field( $_POST['et_post_bg_layout'] ) );
    else
      delete_post_meta( $post_id, '_et_post_bg_layout' );

    if ( isset( $_POST['et_single_title'] ) )
      update_post_meta( $post_id, '_et_pb_show_title', sanitize_text_field( $_POST['et_single_title'] ) );
    else
      delete_post_meta( $post_id, '_et_pb_show_title' );

    if ( isset( $_POST['et_pb_post_hide_nav'] ) )
      update_post_meta( $post_id, '_et_pb_post_hide_nav', sanitize_text_field( $_POST['et_pb_post_hide_nav'] ) );
    else
      delete_post_meta( $post_id, '_et_pb_post_hide_nav' );

    if ( isset( $_POST['et_project_nav'] ) )
      update_post_meta( $post_id, '_et_pb_project_nav', sanitize_text_field( $_POST['et_project_nav'] ) );
    else
      delete_post_meta( $post_id, '_et_pb_project_nav' );

    if ( isset( $_POST['et_pb_page_layout'] ) ) {
      update_post_meta( $post_id, '_et_pb_page_layout', sanitize_text_field( $_POST['et_pb_page_layout'] ) );
    } else {
      delete_post_meta( $post_id, '_et_pb_page_layout' );
    }

    if ( isset( $_POST['et_pb_side_nav'] ) ) {
      update_post_meta( $post_id, '_et_pb_side_nav', sanitize_text_field( $_POST['et_pb_side_nav'] ) );
    } else {
      delete_post_meta( $post_id, '_et_pb_side_nav' );
    }
  }

add_action( 'save_post', 'idivi_divi_post_settings_save_details', 10, 2 );


/**
 * Begins execution of the plugin.
 */
function run_idivi_post_settings() {

	$plugin = new idivi_post_settings();
	$plugin->run();

}
run_idivi_post_settings();

?>
