<?php
function createcal(){
	$teamId = $_GET['team'];
	//print_r($teamId);
	$dbwhere = "Spiele";
	if(is_numeric($teamId)){
		$result = getdb_data_team($teamId, $dbwhere);
	}else{
		$result = getdb_data($dbwhere);
	}
	calfeed($result, $teamId);
	}
// Add a custom endpoint "calendar"
function add_calendar_feed(){
	add_feed('kalender.ics', 'createcal');
    // Only uncomment these 2 lines the first time you load this script, to update WP rewrite rules
	//global $wp_rewrite;
    //$wp_rewrite->flush_rules( false );
}
add_action('init', 'add_calendar_feed');