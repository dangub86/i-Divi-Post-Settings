<?php

/**
 * The admin-specific functionality of the plugin.
 */
class idivi_post_settings_Admin {

	/**
	 * The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

  /*
   * Enqueue and localize idivi-ajax script
   */
  public function enqueue_scripts() {
    wp_enqueue_script('idivi-ajax', plugin_dir_url(__FILE__) . 'js/idivi-ajax.js', array('jquery'), $this->version);
    wp_localize_script('idivi-ajax', 'idivi_vars', array(
        'idivi_nonce' => wp_create_nonce('idivi-nonce')
      )
    );
  }

	/*
   * Register stylesheets for the admin
   */
  public function enqueue_styles() {
    wp_enqueue_style('admin-css', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version);
  }

	/**
   * If Divi is not active alert the user the plugin need Divi in order to work otherwise show the info notice redirecting to the linked Theme Customizer
	 *
	 * @since   1.1
	 *
   */
    function inform_user() {
		 $user_id = get_current_user_id();
	   $idivi_dismiss = get_user_option("idivi-dismiss", $user_id);
     $current_theme = wp_get_theme();
     if ( 'Divi' === $current_theme->get( 'Name' ) || 'Divi' === $current_theme->get( 'Template' ) ) {
        if ( $idivi_dismiss != 'dismissed') {
			   echo '<div class="notice notice-info idivi-notice is-dismissible" data-notice-id="idivi-notice"><p>You need to <a href="customize.php?autofocus[panel]=et_divi_blog_settings" class="notice-link">go to the Theme Customizer</a> in order to set your preferences!</p></div>';
		  }
     } else {
      echo '<div class="notice notice-error"><p>You need to have Divi theme active. Divi Post Settings <b>depends</b> from Divi!</p></div>';
    }
   }

  /*
   * Process Ajax request updating 'idivi-dismiss' user option
   */
  function process_ajax() {
    $user_id = get_current_user_id();
    if( !isset( $_POST['idivi_nonce'] ) || !wp_verify_nonce($_POST['idivi_nonce'], 'idivi-nonce') )
      die('Permissions check failed');
    update_user_option( $user_id, "idivi-dismiss", 'dismissed' );
      die();
  }

	/*
	 * Remove the default Divi Metaboxes
	 */
	  public function remove_metabox() {
			remove_action( 'add_meta_boxes', 'et_add_post_meta_box' );
		}

  /*
   * Add options to the Theme Customizer (Blog panel)
   */
  function post_settings_options($wp_customize) {
   $wp_customize->add_section('idivi_post_settings_section', array(
   'title' => __('Divi Post Settings', $this->plugin_name),
   'panel' => 'et_divi_blog_settings',
    ));
   $wp_customize->add_setting('idivi_post_settings_sidebar', array(
    'default' => 'Right',
    'type' => 'option',
    'capability' => 'edit_theme_options',
   ));
   $wp_customize->add_control('idivi_post_settings_layout', array(
    'label' => __('Post Layout', $this->plugin_name),
    'section' => 'idivi_post_settings_section',
    'type' => 'select',
    'choices'    => array(
              'Right' => 'Right Sidebar',
              'Left' => 'Left Sidebar',
              'Full' => 'Fullwidth',
          ),
    'priority' => 5,
    'settings' => 'idivi_post_settings_sidebar'
   ));
   $wp_customize->add_setting('idivi_post_settings_dot', array(
    'default' => 'Off',
    'type' => 'option',
    'capability' => 'edit_theme_options',
   ));
   $wp_customize->add_control('idivi_post_settings_dot_nav', array(
    'label' => __('Dot Navigation', $this->plugin_name),
    'section' => 'idivi_post_settings_section',
    'type' => 'select',
    'choices'    => array(
              'Off' => 'Off',
              'On' => 'On',
          ),
    'priority' => 5,
    'settings' => 'idivi_post_settings_dot'
   ));
   $wp_customize->add_setting('idivi_post_settings_before_scroll', array(
    'default' => 'Default',
    'type' => 'option',
    'capability' => 'edit_theme_options',
   ));
   $wp_customize->add_control('idivi_post_settings_hide_before_scroll', array(
    'label' => __('Hide Nav Before Scroll', $this->plugin_name),
    'section' => 'idivi_post_settings_section',
    'type' => 'select',
    'choices'    => array(
              'Default' => 'Default',
              'Off' => 'Off',
              'On'  => 'On',
          ),
    'priority' => 5,
    'settings' => 'idivi_post_settings_before_scroll'
   ));
   $wp_customize->add_setting('idivi_post_settings_post_title', array(
    'default' => 'Show',
    'type' => 'option',
    'capability' => 'edit_theme_options',
   ));
   $wp_customize->add_control('idivi_post_settings_post_title_show', array(
    'label' => __('Post Title', $this->plugin_name),
    'section' => 'idivi_post_settings_section',
    'type' => 'select',
    'choices'    => array(
              'Show' => 'Show',
              'Hide' => 'Hide',
          ),
    'priority' => 5,
    'settings' => 'idivi_post_settings_post_title'
   ));

// DIVI PAGE SETTINGS
	 $wp_customize->add_section('idivi_page_settings_section', array(
	 'title' => __('Divi Page Settings', $this->plugin_name),
	 'panel' => 'et_divi_blog_settings',
	  ));
	 $wp_customize->add_setting('idivi_page_settings_sidebar', array(
	  'default' => 'Right',
	  'type' => 'option',
	  'capability' => 'edit_theme_options',
	 ));
	 $wp_customize->add_control('idivi_page_settings_layout', array(
	  'label' => __('Page Layout', $this->plugin_name),
	  'section' => 'idivi_page_settings_section',
	  'type' => 'select',
	  'choices'    => array(
	 					 'Right' => 'Right Sidebar',
	 					 'Left' => 'Left Sidebar',
	 					 'Full' => 'Fullwidth',
	 			 ),
	  'priority' => 5,
	  'settings' => 'idivi_page_settings_sidebar'
	 ));
	 $wp_customize->add_setting('idivi_page_settings_dot', array(
	  'default' => 'Off',
	  'type' => 'option',
	  'capability' => 'edit_theme_options',
	 ));
	 $wp_customize->add_control('idivi_page_settings_dot_nav', array(
	  'label' => __('Dot Navigation', $this->plugin_name),
	  'section' => 'idivi_page_settings_section',
	  'type' => 'select',
	  'choices'    => array(
	 					 'Off' => 'Off',
	 					 'On' => 'On',
	 			 ),
	  'priority' => 5,
	  'settings' => 'idivi_page_settings_dot'
	 ));
	 $wp_customize->add_setting('idivi_page_settings_before_scroll', array(
	  'default' => 'Default',
	  'type' => 'option',
	  'capability' => 'edit_theme_options',
	 ));
	 $wp_customize->add_control('idivi_page_settings_hide_before_scroll', array(
	  'label' => __('Hide Nav Before Scroll', $this->plugin_name),
	  'section' => 'idivi_page_settings_section',
	  'type' => 'select',
	  'choices'    => array(
	 					 'Default' => 'Default',
	 					 'Off' => 'Off',
	 					 'On'  => 'On',
	 			 ),
	  'priority' => 5,
	  'settings' => 'idivi_page_settings_before_scroll'
	 ));


// DIVI PROJECT SETTINGS
	 $wp_customize->add_section('idivi_project_settings_section', array(
	 'title' => __('Divi Project Settings', $this->plugin_name),
	 'panel' => 'et_divi_blog_settings',
	  ));
	 $wp_customize->add_setting('idivi_project_settings_sidebar', array(
	  'default' => 'Right',
	  'type' => 'option',
	  'capability' => 'edit_theme_options',
	 ));
	 $wp_customize->add_control('idivi_project_settings_layout', array(
	  'label' => __('Project Layout', $this->plugin_name),
	  'section' => 'idivi_project_settings_section',
	  'type' => 'select',
	  'choices'    => array(
	 					 'Right' => 'Right Sidebar',
	 					 'Left' => 'Left Sidebar',
	 					 'Full' => 'Fullwidth',
	 			 ),
	  'priority' => 5,
	  'settings' => 'idivi_project_settings_sidebar'
	 ));
	 $wp_customize->add_setting('idivi_project_settings_dot', array(
	  'default' => 'Off',
	  'type' => 'option',
	  'capability' => 'edit_theme_options',
	 ));
	 $wp_customize->add_control('idivi_project_settings_dot_nav', array(
	  'label' => __('Dot Navigation', $this->plugin_name),
	  'section' => 'idivi_project_settings_section',
	  'type' => 'select',
	  'choices'    => array(
	 					 'Off' => 'Off',
	 					 'On' => 'On',
	 			 ),
	  'priority' => 5,
	  'settings' => 'idivi_project_settings_dot'
	 ));
	 $wp_customize->add_setting('idivi_project_settings_before_scroll', array(
	  'default' => 'Default',
	  'type' => 'option',
	  'capability' => 'edit_theme_options',
	 ));
	 $wp_customize->add_control('idivi_project_settings_hide_before_scroll', array(
	  'label' => __('Hide Nav Before Scroll', $this->plugin_name),
	  'section' => 'idivi_project_settings_section',
	  'type' => 'select',
	  'choices'    => array(
	 					 'Default' => 'Default',
	 					 'Off' => 'Off',
	 					 'On'  => 'On',
	 			 ),
	  'priority' => 5,
	  'settings' => 'idivi_project_settings_before_scroll'
	 ));
	 $wp_customize->add_setting('idivi_project_settings_nav', array(
	  'default' => 'Hide',
	  'type' => 'option',
	  'capability' => 'edit_theme_options',
	 ));
	 $wp_customize->add_control('idivi_project_settings_nav_show', array(
	  'label' => __('Project Navigation', $this->plugin_name),
	  'section' => 'idivi_project_settings_section',
	  'type' => 'select',
	  'choices'    => array(
	 					 'Hide' => 'Hide',
						 'Show' => 'Show',
	 			 ),
	  'priority' => 5,
	  'settings' => 'idivi_project_settings_nav'
	 ));


	 // ADD SETTING FOR REMEMBER LAST POST OPTIONS
 	 $wp_customize->add_setting('idivi_post_settings_last_used', array(
 		'default' => false,
 		'type' => 'option',
 		'capability' => 'edit_theme_options',
 	 ));
 	 $wp_customize->add_control('idivi_post_settings_last_used_options', array(
 		'label' => __('Remember Last Used Options', $this->plugin_name),
 		'section' => 'idivi_post_settings_section',
 		'type' => 'checkbox',
 		'priority' => 10,
 		'settings' => 'idivi_post_settings_last_used'
 	 ));

	 $wp_customize->add_setting('idivi_page_settings_last_used', array(
 		'default' => false,
 		'type' => 'option',
 		'capability' => 'edit_theme_options',
 	 ));
 	 $wp_customize->add_control('idivi_page_settings_last_used_options', array(
 		'label' => __('Remember Last Used Options', $this->plugin_name),
 		'section' => 'idivi_page_settings_section',
 		'type' => 'checkbox',
 		'priority' => 10,
 		'settings' => 'idivi_page_settings_last_used'
 	 ));

	 $wp_customize->add_setting('idivi_project_settings_last_used', array(
 		'default' => false,
 		'type' => 'option',
 		'capability' => 'edit_theme_options',
 	 ));
 	 $wp_customize->add_control('idivi_project_settings_last_used_options', array(
 		'label' => __('Remember Last Used Options', $this->plugin_name),
 		'section' => 'idivi_project_settings_section',
 		'type' => 'checkbox',
 		'priority' => 10,
 		'settings' => 'idivi_project_settings_last_used'
 	 ));
  }

	// create function that retrieves last post id
	public function get_last_post_id() {
   $latest_post_args = array(
    'numberposts' => 1,
    'orderby' => 'post_date',
		'post_type' => 'post',
    'order' => 'DESC'
   );
   $latest_post = wp_get_recent_posts( $latest_post_args );
   foreach ($latest_post as $post) {
    $latest_post_id = $latest_post[0]["ID"];
		return $latest_post_id;
   }
  }

	// public function get_last_page_id() {
  //  $latest_post_args = array(
  //   'numberposts' => 1,
  //   'orderby' => 'post_date',
	// 	'post_type' => 'page',
  //   'order' => 'DESC'
  //  );
  //  $latest_post = wp_get_recent_posts( $latest_post_args );
  //  foreach ($latest_post as $post) {
  //   $latest_post_id = $latest_post[0]["ID"];
	// 	return $latest_post_id;
  //  }
  // }
	//
	// public function get_last_project_id() {
  //  $latest_post_args = array(
  //   'numberposts' => 1,
  //   'orderby' => 'post_date',
	// 	'post_type' => 'project',
  //   'order' => 'DESC'
  //  );
  //  $latest_post = wp_get_recent_posts( $latest_post_args );
  //  foreach ($latest_post as $post) {
  //   $latest_post_id = $latest_post[0]["ID"];
	// 	return $latest_post_id;
  //  }
  // }

	public function get_last_page_id() {
   $latest_page_args = array(
    'numberposts' => 1,
    'orderby' => 'post_date',
		'post_type' => 'page',
    'order' => 'DESC'
   );
   $latest_page = wp_get_recent_posts( $latest_page_args );
   foreach ($latest_page as $page) {
    $latest_page_id = $latest_page[0]["ID"];
		return $latest_page_id;
   }
  }

	public function get_last_project_id() {
   $latest_project_args = array(
    'numberposts' => 1,
    'orderby' => 'post_date',
		'post_type' => 'project',
    'order' => 'DESC'
   );
   $latest_project = wp_get_recent_posts( $latest_project_args );
   foreach ($latest_project as $project) {
    $latest_project_id = $latest_project[0]["ID"];
		return $latest_project_id;
   }
  }


}
 ?>
