<?php
/**
 * Plugin Name: Docty Clinic Plugin
 * Description: Displays clinic information such as staff, locations, specialties, and reviews.
 * Version: 1.0.2
 * Author: Your Name
 * Text Domain: docty-clinic
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin path
define('DOCTY_CLINIC_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DOCTY_CLINIC_PLUGIN_DIR', plugin_dir_url(__FILE__));
define('DOCTY_API_URL', 'https://dev-backend.docty.life/');
define('DOCTY_IFRAME_URL', 'https://patientv2.docty.life/wp/profile/');
define('DOCTY_CLINIC_VERSION', '1.0.2');

// Include necessary files
include_once DOCTY_CLINIC_PLUGIN_PATH . 'includes/admin-menu.php';
include_once DOCTY_CLINIC_PLUGIN_PATH . 'includes/api-functions.php';
//include_once DOCTY_CLINIC_PLUGIN_PATH . 'includes/shortcodes.php';

include_once DOCTY_CLINIC_PLUGIN_PATH . 'includes/shortcodes-new.php';


// Add CSS and JS 

add_action('wp_enqueue_scripts', 'enqueue_docty_scripts_and_styles');


function enqueue_docty_scripts_and_styles() {

    wp_enqueue_style('owl.carousel', plugin_dir_url(__FILE__) . 'css/owl.carousel.min.css', false, DOCTY_CLINIC_VERSION );
    wp_enqueue_style('docty_css', plugin_dir_url(__FILE__) . 'css/front.css', false, DOCTY_CLINIC_VERSION );
     
    wp_enqueue_script( 'owl.carousel', plugins_url( 'js/owl.carousel.js', __FILE__ ), array('jquery'),DOCTY_CLINIC_VERSION );
    wp_enqueue_script( 'docty_js', plugins_url( 'js/front.js', __FILE__ ), array('jquery','owl.carousel'), DOCTY_CLINIC_VERSION );
   
    //    wp_localize_script( "docty_js",'admin',array("ajaxurl"=>admin_url('admin-ajax.php')));   

}

function admin_enqueue_docty_scripts_and_styles(){
    
    wp_enqueue_style( 'docty_css', plugin_dir_url(__FILE__) . 'css/docty_admin.css', false, DOCTY_CLINIC_VERSION );
    wp_enqueue_script( 'docty_admin_js', plugins_url( 'js/docty_admin.js', __FILE__ ), array('jquery'), DOCTY_CLINIC_VERSION );
    wp_localize_script( 'docty_admin_js','es', array('ajaxurl'=> admin_url('admin-ajax.php') ) );
    
}
   
add_action('admin_enqueue_scripts', 'admin_enqueue_docty_scripts_and_styles');