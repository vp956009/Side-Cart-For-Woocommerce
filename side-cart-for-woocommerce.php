<?php
/**
* Plugin Name: Side Cart For Woocommerce
* Description: This plugin allows you to Create Sidebar cart in WooCommerce.
* Version: 1.0
* Copyright: 2019 
*/

if (!defined('ABSPATH')) {
  die('-1');
}
if (!defined('SCFW_PLUGIN_NAME')) {
  define('SCFW_PLUGIN_NAME', 'Side Cart For Woocommerce');
}
if (!defined('SCFW_PLUGIN_VERSION')) {
  define('SCFW_PLUGIN_VERSION', '2.0.0');
}
if (!defined('SCFW_PLUGIN_FILE')) {
  define('SCFW_PLUGIN_FILE', __FILE__);
}
if (!defined('SCFW_PLUGIN_DIR')) {
  define('SCFW_PLUGIN_DIR',plugins_url('', __FILE__));
}
if (!defined('SCFW_DOMAIN')) {
  define('SCFW_DOMAIN', 'scfw');
}


if (!class_exists('SCFW')) {

  	class SCFW {

    	protected static $SCFW_instance;

    	public static function SCFW_instance() {
	      	if (!isset(self::$SCFW_instance)) {
	        	self::$SCFW_instance = new self();
	        	self::$SCFW_instance->init();
	        	self::$SCFW_instance->includes();
	      	}
	      	return self::$SCFW_instance;
	    }

      	function __construct() {
        	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        	//check plugin activted or not
        	add_action('admin_init', array($this, 'SCFW_check_plugin_state'));
      	}

      	function init() {
	      	add_action( 'admin_notices', array($this, 'SCFW_show_notice'));
	      	add_action( 'admin_enqueue_scripts', array($this, 'SCFW_load_admin_script_style'));
	      	add_action( 'wp_enqueue_scripts',  array($this, 'SCFW_load_script_style'));
	    }

	    //Load all includes files
	    function includes() {
	      	include_once('includes/scfw_backend.php');
	      	include_once('includes/scfw_front.php');
	    }

	    //Add JS and CSS on Backend
	    function SCFW_load_admin_script_style() {
	      	wp_enqueue_style( 'SCFW-admin-style', SCFW_PLUGIN_DIR . '/css/scfw_admin_style.css', false, '1.0.0' );
	      	wp_enqueue_script( 'SCFW-admin-script', SCFW_PLUGIN_DIR . '/js/scfw_admin_script.js', array( 'jquery', 'select2') );
	      	wp_localize_script( 'ajaxloadpost', 'ajax_postajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	      	wp_enqueue_style( 'woocommerce_admin_styles-css', WP_PLUGIN_URL. '/woocommerce/assets/css/admin.css',false,'1.0',"all");
	      	wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker-alpha', SCFW_PLUGIN_DIR . '/js/wp-color-picker-alpha.js', array( 'wp-color-picker' ), '1.0.0', true );
	    }


	    function SCFW_load_script_style() {
	    	wp_enqueue_script( 'owlcarousel', SCFW_PLUGIN_DIR . '/owlcarousel/owl.carousel.js', false, '1.0.0' );
	    	wp_enqueue_style( 'owlcarousel-min', SCFW_PLUGIN_DIR . '/owlcarousel/assets/owl.carousel.min.css', false, '1.0.0' );
	      	wp_enqueue_style( 'owlcarousel-theme', SCFW_PLUGIN_DIR . '/owlcarousel/assets/owl.theme.default.min.css', false, '1.0.0' );
	      	wp_enqueue_style( 'SCFW-front_css', SCFW_PLUGIN_DIR . '/css/scfw_front_style.css', false, '1.0.0' );
	      	wp_enqueue_script( 'SCFW-front_js', SCFW_PLUGIN_DIR . '/js/scfw_front_script.js', false, '1.0.0' );
	      	wp_localize_script( 'SCFW-front_js', 'ajax_postajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	        wp_enqueue_script( 'jquery-effects-core' );
			wp_localize_script('SCFW-front_js', 'scfw_urls', array(
			    'pluginsUrl' => SCFW_PLUGIN_DIR,
			));
			wp_localize_script('SCFW-front_js', 'scfw_sidebar_width', array(
			    'scfw_width' => get_option( 'wfc_sidecart_width', '350').'px',
			));
	    }


    	function SCFW_show_notice() {
        	if ( get_transient( get_current_user_id() . 'wfcerror' ) ) {

          		deactivate_plugins( plugin_basename( __FILE__ ) );

          		delete_transient( get_current_user_id() . 'wfcerror' );

          		echo '<div class="error"><p> This plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=woocommerce">WooCommerce</a> plugin installed and activated.</p></div>';
        	}
    	}


    	function SCFW_check_plugin_state(){
      		if ( ! ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) ) {
        		set_transient( get_current_user_id() . 'wfcerror', 'message' );
      		}
    	}
	    
  	}

  	add_action('plugins_loaded', array('SCFW', 'SCFW_instance'));
}