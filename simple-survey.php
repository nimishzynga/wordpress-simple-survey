<?php
/*
Plugin Name: WP Simple Survey
Plugin URI: http://www.sailabs.co/products/wordpress-simple-survey/
Description: Use this plugin to easily create surveys and graded quizzes. You can track the results and guide users to different locations based on their scores.
Version: 3.0.2
Author: Richard Royal
Author URI: http://www.sailabs.co/
*/

define('WPSS_PATH',  plugin_dir_path( __FILE__ ) );
define('WPSS_URL', WP_PLUGIN_URL."/wordpress-simple-survey/" );
require_once(WPSS_PATH."lib/class.util.php");
$util = new WPSS_Util();

register_activation_hook(__FILE__,'wpss_plugin_install');



/*------------------------------------------------*/
/* Admin Pages                                    */
/*------------------------------------------------*/
function wpss_admin_help_page() {     require_once("admin/help/help.php"); }
function wpss_admin_options_page() {  require_once("admin/options/options.php"); }
function wpss_admin_quizzes() {       require_once(WPSS_PATH."admin/dispatcher.php"); }
function wpss_admin_pages() {
  if (current_user_can('manage_options')) {
    add_menu_page("Setup Quizzes and Surveys", "Surveys/Quizzes", "publish_posts", "wpss-quizzes","wpss_admin_quizzes", 'dashicons-welcome-learn-more' );
    add_submenu_page( "wpss-quizzes", "WP Simple Survey - Options", "WPSS Options", "publish_posts", "wpss-options", "wpss_admin_options_page");
    add_submenu_page( "wpss-quizzes", "WP Simple Survey - Help", "WPSS Help", "publish_posts", "wpss-help", "wpss_admin_help_page");
  }
}add_action('admin_menu', 'wpss_admin_pages');




/*------------------------------------------------*/
/* WP Shortcode Handlers                          */
/*------------------------------------------------*/
function wpss_quiz_shortcode_handler($atts, $content=null, $code=""){
   return wpss_get_quiz($atts);
}add_shortcode('wp_simple_survey', 'wpss_quiz_shortcode_handler');

function wpss_gift_handler($atts, $content=null, $code="") {
    echo "handler called";
   return wpss_gift($atts);
}add_shortcode('wp_simple_gift', 'wpss_gift_handler');




/*------------------------------------------------*/
/* Register JS and CSS                            */
/*------------------------------------------------*/
function wpss_register_javascripts(){
  if(!is_admin()){
    $util = new WPSS_Util();

    wp_enqueue_script('jquery-wp-simple-survey', $util->get_js('jquery.wp-simple-survey.js'), array('jquery'), '3.0.0');
    wp_enqueue_script('wpss', $util->get_js('wpss.js'), array('jquery-wp-simple-survey'), '3.0.0');
  }
}add_action('wp_print_scripts', 'wpss_register_javascripts');


function wpss_register_stylesheets() {
  if(!is_admin()){
    $util = new WPSS_Util();
    wp_enqueue_style('wpss-style', $util->get_css('wpss.css'));
    wp_enqueue_style('wpss-style-1', $util->get_css('my.css'));
    wp_enqueue_style('wpss-custom-db-style', get_bloginfo('url').'/?wpss-routing=custom-css');
  }
}add_action('wp_print_styles', 'wpss_register_stylesheets');


function wpss_register_admin_stylesheets(){
  wp_enqueue_style('wpss-admin-style', WPSS_URL.'assets/css/wpss-admin.css');
}add_action('admin_init', 'wpss_register_admin_stylesheets');







/*-------------------------------------------------------------*/
/**
 *  Setup custom URL for plugin to POST quiz results to,
 *  CRUD actions, and data export.
 *  Allows for proper access to 'global worpress' scope
 *  including database settings needed for tracking, without
 *  headers being outputted first.
 *
 *  /?wpss-routing=results
 *  /?wpss-routing=crud
 *  /?wpss-routing=export
 *  /?wpss-routing=print
 *  /?wpss-routing=css
 */
function wpss_parse_request($wp) {
    if (array_key_exists('wpss-routing', $wp->query_vars) && $wp->query_vars['wpss-routing'] == 'results') {
      include(WPSS_PATH.'submit/submit.php');
      die();exit();
    }
    if (array_key_exists('wpss-routing', $wp->query_vars) && $wp->query_vars['wpss-routing'] == 'crud') {
      $util = new WPSS_Util();
      $util->crud_redirect();
      die();
    }
    if (array_key_exists('wpss-routing', $wp->query_vars) && $wp->query_vars['wpss-routing'] == 'print') {
      echo wpss_get_printer_friendly_quiz((int) $_GET['quiz_id']);
      die();exit();
    }
    if (array_key_exists('wpss-routing', $wp->query_vars) && $wp->query_vars['wpss-routing'] == 'custom-css') {
      $custom_css = get_option('wpss_custom_css');
      header( 'Content-Type: text/css' );
      echo $custom_css;
      die();exit();
    }
}add_action('parse_request', 'wpss_parse_request');


function wpss_parse_query_vars($vars) {
    $vars[] = 'wpss-routing';
    return $vars;
}add_filter('query_vars', 'wpss_parse_query_vars');

/*-------------------------------------------------------------*/

?>
