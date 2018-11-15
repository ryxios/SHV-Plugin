<?php
/*
Plugin Name:  SHV-Plugin
Plugin URI:   http://atvkv.ch
Description:  Anzeige Daten von SHV API
Version:      2.1
Author:       Joel Nowak
Author URI:   http://atvkv.ch
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  shv_plugin
Domain Path:  /languages
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require_once('widgets/spielplan.php' );
require_once('widgets/resultate.php' );
require_once('widgets/ranglisten.php' );
require_once('shortcodes.php' );
require_once('cal.php' );
require_once('calfeed.php' );
add_action('admin_menu', 'shv_plugin_add_pages');
add_action( 'init', 'load_files' );
function load_files(){
    wp_register_style('shv_spielplan_widget', plugins_url('style.css',__FILE__ ));
    wp_enqueue_style('shv_spielplan_widget');
    add_action( 'wp_head', 'shvcustomcss');
}
function shv_plugin_add_pages() {
	// Add a new top-level menu (ill-advised):
    add_menu_page(__('SHV-Plugin','menu-shv-plugin'), __('SHV Plugin','menu-shv-plugin'), 'manage_options', 'shv-plugin-top-level-handle', 'shv_plugin_toplevel_page' );
    wp_enqueue_script( 'shv-plugin-scripts', plugins_url( 'shv-plugin-scripts.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ) );
     wp_enqueue_style( 'wp-color-picker' );
}
// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function shv_plugin_toplevel_page() {
    //must check that the user has the required capability
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names
    $opt_name = 'vereinsId';
    $opt2_name = 'update';
    $opt3_name = 'auth';
    $opt4_name = 'farbe1';
    $opt5_name = 'farbe2';
    $opt6_name = 'farbe3';
    $opt7_name = 'calendar';

    $hidden_field_name = 'vereinsiD_hidden';
    $hidden_field2_name = 'fetch_teams_hidden';
    $hidden_field3_name = 'fetch_spiele_hidden';
    $hidden_field4_name = 'fetch_resultate_hidden';
    $hidden_field5_name = 'fetch_ranglisten_hidden';
    $hidden_field6_name = 'fetch_spiele_teams_hidden';
    $hidden_field7_name = 'fetch_resultate_teams_hidden';
    $hidden_field8_name = 'shv_plugin_farben_hidden';
    $hidden_field9_name = 'shv_plugin_calendar_hidden';

    $data_field_name = 'shv_plugin_vereinsId';
    $data_field2_name = 'shv_plugin_update';
    $data_field3_name = 'shv_plugin_auth';
    $data_field4_name = 'shv_plugin_farbe1';
    $data_field5_name = 'shv_plugin_farbe2';
    $data_field6_name = 'shv_plugin_farbe3';
    $data_field7_name = 'shv_plugin_calendar';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );
    $opt2_val = get_option( $opt2_name );
    $opt3_val = get_option( $opt3_name );
    $opt4_val = get_option( $opt4_name );
    $opt5_val = get_option( $opt5_name );
    $opt6_val = get_option( $opt6_name );
    $opt7_val = get_option( $opt7_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];
        $opt2_val = $_POST[ $data_field2_name ];
        $opt3_val = $_POST[ $data_field3_name ];
        $opt7_val = $_POST[ $data_field7_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );
        update_option( $opt2_name, $opt2_val );
        update_option( $opt7_name, $opt7_val );

        // Put a "settings saved" message on the screen

	fetch_teams();
	fetch_spiele();
	fetch_resultate();
	fetch_ranglisten();
	add_calendar_feed()
	?>
<div class="updated"><p><strong><?php _e('Vereins ID saved.', 'menu-shv-plugin' ); ?></strong></p></div>
<?php

    }

        if( isset($_POST[ $hidden_field2_name ]) && $_POST[ $hidden_field2_name ] == 'Y' ) {
      fetch_teams();
      ?>
  <div class="updated"><p><strong><?php _e('Teams fetched', 'menu-shv-plugin' ); ?></strong></p></div>
<?php


    }
	if( isset($_POST[ $hidden_field3_name ]) && $_POST[ $hidden_field3_name ] == 'Y' ) {
      fetch_spiele();
?>
  <div class="updated"><p><strong><?php _e('Spiele fetched', 'menu-shv-plugin' ); ?></strong></p></div>
<?php

    }
    if( isset($_POST[ $hidden_field4_name ]) && $_POST[ $hidden_field4_name ] == 'Y' ) {
      fetch_resultate();
?>
  <div class="updated"><p><strong><?php _e('Resultate fetched', 'menu-shv-plugin' ); ?></strong></p></div>
<?php

    }
    if( isset($_POST[ $hidden_field5_name ]) && $_POST[ $hidden_field5_name ] == 'Y' ) {
      fetch_ranglisten();
?>
  <div class="updated"><p><strong><?php _e('Ranglisten fetched', 'menu-shv-plugin' ); ?></strong></p></div>
<?php

    }
    if( isset($_POST[ $hidden_field6_name ]) && $_POST[ $hidden_field6_name ] == 'Y' ) {
      fetch_spiele_teams();
?>
  <div class="updated"><p><strong><?php _e('Spiele by Teams fetched', 'menu-shv-plugin' ); ?></strong></p></div>
<?php

    }
    if( isset($_POST[ $hidden_field7_name ]) && $_POST[ $hidden_field7_name ] == 'Y' ) {
      fetch_resultate_teams()
?>
  <div class="updated"><p><strong><?php _e('Resultate by Teams fetched', 'menu-shv-plugin' ); ?></strong></p></div>
<?php

    }
    if( isset($_POST[ $hidden_field8_name ]) && $_POST[ $hidden_field8_name ] == 'Y' ) {
	  $opt4_val = $_POST[ $data_field4_name ];
	  $opt5_val = $_POST[ $data_field5_name ];
	  $opt6_val = $_POST[ $data_field6_name ];

      update_option( $opt4_name, $opt4_val );
      update_option( $opt5_name, $opt5_val );
      update_option( $opt6_name, $opt6_val );
?>
  <div class="updated"><p><strong><?php _e('CSS gespeichert', 'menu-shv-plugin' ); ?></strong></p></div>
<?php

    }

    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'SHV-Plugin', 'menu-shv-plugin' ) . "</h2>";

    // settings form

    ?>
<div class="shvleft">
<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("VereinsId:", 'menu-shv-plugin' ); ?>
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
</p>
<p><?php _e("Update (min):", 'menu-shv-plugin' ); ?>
<input type="text" name="<?php echo $data_field2_name; ?>" value="<?php echo $opt2_val; ?>" size="20">
</p>
<p><?php _e("Auth:", 'menu-shv-plugin' ); ?>
<input type="text" name="<?php echo $data_field3_name; ?>" value="<?php echo $opt3_val; ?>" size="20">
</p>
<p><?php _e("Kalender:", 'menu-shv-plugin' ); ?>
<input type="text" name="<?php echo $data_field7_name; ?>" value="<?php echo $opt7_val; ?>" size="20">
</p>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Speichern') ?>" />
</p><hr />

</form>

<form name="form2" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field2_name; ?>" value="Y">

<p><?php _e("Fetch Teams:", 'menu-shv-plugin' ); ?> <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Fetch') ?>" />
</p><hr />
</form>
</form>

<form name="form3" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field3_name; ?>" value="Y">

<p><?php _e("Fetch Spiele:", 'menu-shv-plugin' ); ?> <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Fetch') ?>" />
</p><hr />
</form>

<form name="form4" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field4_name; ?>" value="Y">

<p><?php _e("Fetch Resultate:", 'menu-shv-plugin' ); ?> <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Fetch') ?>" />
</p><hr />
</form>
<form name="form5" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field5_name; ?>" value="Y">

<p><?php _e("Fetch Ranglisten:", 'menu-shv-plugin' ); ?> <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Fetch') ?>" />
</p><hr />
</form>
<form name="form6" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field6_name; ?>" value="Y">

<p><?php _e("Fetch Spiele by Teams:", 'menu-shv-plugin' ); ?> <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Fetch') ?>" />
</p><hr />
</form>
<form name="form7" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field7_name; ?>" value="Y">

<p><?php _e("Fetch Resultate by Teams:", 'menu-shv-plugin' ); ?> <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Fetch') ?>" />
</p><hr />
</form>
</form>
<form name="form8" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field8_name; ?>" value="Y">
<p><?php _e("Farbe 1:", 'menu-shv-plugin' ); ?> <input type="text" name="<?php echo $data_field4_name; ?>" class="shv-plugin-color-picker" id="farbe1" value="<?php echo $opt4_val; ?>" /></p>
<p><?php _e("Farbe 2:", 'menu-shv-plugin' ); ?> <input type="text" name="<?php echo $data_field5_name; ?>" class="shv-plugin-color-picker" id="farbe2" value="<?php echo $opt5_val; ?>" /></p>
<p><?php _e("Farbe 3:", 'menu-shv-plugin' ); ?> <input type="text" name="<?php echo $data_field6_name; ?>" class="shv-plugin-color-picker" id="farbe3" value="<?php echo $opt6_val; ?>" /></p>
<p><?php _e("Speichern:", 'menu-shv-plugin' ); ?> <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Speichern') ?>" /></p>
<hr />
</form>
</div>
<div class="shvright"
<h3>Shortcodes:</h3>
<p>[Spielplan spieltage=100 liga=1 resultat=0]</p>
<p>[Spielplanteam spieltage=5 teamid=28138 liga=0 resultat=0]]</p>
<p>[Resultate Spieltage=100 liga=1 resultat=1]</p>
<p>[Resultateteam spieltage=100 teamid=28138 liga=0 resultat=1]</p>
<p>[Rangliste]</p>
<p>[Ranglisteteam teamid=27984]</p>
<p>[Spielerstats groupid= 1160 teamid=27983]</p>
<p>[Kalenderurls]</p>
<hr />
<?php

//Display Teams
global $wpdb;
$teamsjson = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");
$teamsphp = json_decode(json_encode($teamsjson), True);
$teams = json_decode ($teamsphp['data'], true);
$teamslist = "<div>";
if ($teams == null){

$teamslist .= "<strong> Setup Plugin</strong>";

}else{
	$teamslist .= "<ul style='display:table;border-collapse:collapse'>";
	$teamslist .= "<li style='display:table-row;padding:5px;border: 1px solid black;'><span style='display:table-cell;padding:5px;border: 1px solid black;'>Teamname</span><span style='display:table-cell;padding-left:5px;padding:5px;border: 1px solid black;'>Gruppe</span><span style='display:table-cell;padding:5px;border: 1px solid black;'>TeamId</span><span style='display:table-cell;padding:5px;border: 1px solid black;'>GroupId</span></li>";
	foreach ($teams as $teamsdetails){
	$teamslist .= "<li style='display:table-row;padding:5px;border: 1px solid black;'><span style='display:table-cell;padding:5px;border: 1px solid black;'> " . $teamsdetails['teamName'] ."</span><span style='display:table-cell;padding-left:5px;padding:5px;border: 1px solid black;'>" . $teamsdetails['groupText'] ."</span><span style='display:table-cell;padding:5px;border: 1px solid black;'>" . $teamsdetails['teamId'] ."</span><span style='display:table-cell;padding:5px;border: 1px solid black;'>" . $teamsdetails['leagueId'] ."</span></li>";
	}
	$teamslist .= "</ul>";
}
$teamslist .= "</div>";
?>
<h3>Teamslist:</h3>
<div><?php echo $teamslist; ?></div>
</div>
<?php
}
function shvcustomcss()
{
	$farbe1 = get_option( 'farbe1' );
    $farbe2 = get_option( 'farbe2' );
    $farbe3 = get_option( 'farbe3' );
    ?>
         <style type="text/css" id="shv-plugin-css">
             .shv-datum-container { background:  <?php echo $farbe1 ?>; }
             .shv-spiel-container { border-bottom: 1px solid <?php echo $farbe2 ?>;}
             .shv-spiel-container:nth-child(even) { background:<?php echo $farbe3 ?> }
             .shv-title-row {border-bottom: 2px solid <?php echo $farbe2 ?>;}
             .ranglisten-label {background: <?php echo $farbe2 ?>;}
             .rangliste-header {background: <?php echo $farbe1 ?>;}
             .title-row {background: <?php echo $farbe1 ?>;}
			 .table-row {border-bottom: 1px solid <?php echo $farbe2 ?>;}
			 .table-row:nth-child(even) { background: <?php echo $farbe3 ?>; }
			 .table-row:last-child {border-bottom: 0px solid <?php echo $farbe2 ?>; background: <?php echo $farbe1 ?>;}
         </style>
    <?php
}
