<?php defined('WPSS_URL') or die('Restricted access');
/**
 *  Save posted results for quiz, send emails, and send API calls
 *  and redirect to new URL. Data saved as serialize column in one row.
 */
$util = new WPSS_Util();
$result = new WPSS_Result();
$result->parse_results($_POST);

if( $result->redirect_to['error'] === false ){

  setcookie( "wpss_submitter_id", $result->submitter_id );
  wp_redirect("/wordpress/?page_id=9&hash=".$result->redirect_to['hash']);

} else {

  wp_die( $result->redirect_to['msg'] );

}

die();exit;
?>
