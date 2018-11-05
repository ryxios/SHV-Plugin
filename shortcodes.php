<?php 
function create_shv_plugin_table(){
global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'shv_plugin';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name mediumtext NOT NULL,
		data text NOT NULL
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	
}
function fetch_ranglisten(){
$id = get_option('vereinsId');
$auth = get_option('auth');
global $wpdb;
$teamsjson = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");
$teamsphp = json_decode(json_encode($teamsjson), True);
$teams = json_decode ($teamsphp['data'], true);
$ranglisten = array();
$jetzt = date('Y-m-d H:i:s');
    foreach ($teams as $team){
	     $teamId = $team['teamId'];
	     $gruppe = $team['groupText'];
	     
	     $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.handball.ch/rest/v1/teams/" . $teamId . "/group",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic " . $auth ,
    "cache-control: no-cache",
    "postman-token: dc803738-1a57-3107-9da8-7994854f71be"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $assoc = true;
  $result = json_decode ($response, $assoc);
  $ranglisten[$teamId] = $result;
}    
       }
 $data = json_encode($ranglisten);
 $checkdb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Ranglisten'");
 if($checkdb == null){
	$table = $wpdb->prefix."shv_plugin";
    $wpdb->insert( 
        $table, 
        array( 
	        'time' =>$jetzt,
	        'name' => "Ranglisten",
            'data' => $data
            )        );

}else{
	
$table = $wpdb->prefix."shv_plugin";
    $wpdb->update( 
	$table, 
	array( 
	        'time' =>$jetzt,
	        'name' => "Ranglisten",
            'data' => $data
            ), 
	array( 'name' => "Ranglisten" )
);

}
            
}
function fetch_spiele_teams(){
$id = get_option('vereinsId');
$auth = get_option('auth');
global $wpdb;
$teamsjson = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");
$teamsphp = json_decode(json_encode($teamsjson), True);
$teams = json_decode ($teamsphp['data'], true);
$jetzt = date('Y-m-d H:i:s');
    foreach ($teams as $team){
	     $teamId = $team['teamId'];
	     $gruppe = $team['groupText'];
	     
	     $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.handball.ch/rest/v1/teams/" . $teamId . "/games?status=planned",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic Mjc0Mzg1OkdCWUNyZEd0" ,
    "cache-control: no-cache",
    "postman-token: dc803738-1a57-3107-9da8-7994854f71be"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
   $checkdb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Spiele-" . $teamId . "'"); 
 if($checkdb == null){
	$table = $wpdb->prefix."shv_plugin";
    $wpdb->insert( 
        $table, 
        array( 
	        'time' =>$jetzt,
	        'name' => "Spiele-" . $teamId,
            'data' => $response
            )        );

}else{
	
$table = $wpdb->prefix."shv_plugin";
    $wpdb->update( 
	$table, 
	array( 
	        'time' =>$jetzt,
	        'name' => "Spiele-" . $teamId,
            'data' => $response
            ), 
	array( 'name' => "Spiele-" . $teamId )
);

}
}    
       }

            
}
function fetch_spiele_teams_id($teamId){
$id = get_option('vereinsId');
$auth = get_option('auth');

global $wpdb;
$jetzt = date('Y-m-d H:i:s');

	     
	     $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.handball.ch/rest/v1/teams/" . $teamId . "/games?status=planned",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic " . $auth ,
    "cache-control: no-cache",
    "postman-token: dc803738-1a57-3107-9da8-7994854f71be"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
   $checkdb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Spiele-" . $teamId . "'"); 
 if($checkdb == null){
	$table = $wpdb->prefix."shv_plugin";
    $wpdb->insert( 
        $table, 
        array( 
	        'time' =>$jetzt,
	        'name' => "Spiele-" . $teamId,
            'data' => $response
            )        );

}else{
	
$table = $wpdb->prefix."shv_plugin";
    $wpdb->update( 
	$table, 
	array( 
	        'time' =>$jetzt,
	        'name' => "Spiele-" . $teamId,
            'data' => $response
            ), 
	array( 'name' => "Spiele-" . $teamId )
);

}
}    
       

            
}
function fetch_resultate_teams(){
$id = get_option('vereinsId');
$auth = get_option('auth');
global $wpdb;
$teamsjson = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");
$teamsphp = json_decode(json_encode($teamsjson), True);
$teams = json_decode ($teamsphp['data'], true);
$jetzt = date('Y-m-d H:i:s');
    foreach ($teams as $team){
	     $teamId = $team['teamId'];
	     $gruppe = $team['groupText'];
	     
	     $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.handball.ch/rest/v1/teams/" . $teamId . "/games?status=played&order=desc",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic " . $auth ,
    "cache-control: no-cache",
    "postman-token: dc803738-1a57-3107-9da8-7994854f71be"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
   $checkdb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Resultate-" . $teamId . "'"); 
 if($checkdb == null){
	$table = $wpdb->prefix."shv_plugin";
    $wpdb->insert( 
        $table, 
        array( 
	        'time' =>$jetzt,
	        'name' => "Resultate-" . $teamId,
            'data' => $response
            )        );

}else{
	
$table = $wpdb->prefix."shv_plugin";
    $wpdb->update( 
	$table, 
	array( 
	        'time' =>$jetzt,
	        'name' => "Resultate-" . $teamId,
            'data' => $response
            ), 
	array( 'name' => "Resultate-" . $teamId )
);

}
}    
       }

                  
}
function fetch_resultate_teams_id($teamId){
$id = get_option('vereinsId');
$auth = get_option('auth');

global $wpdb;
$jetzt = date('Y-m-d H:i:s');

	     
	     $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.handball.ch/rest/v1/teams/" . $teamId . "/games?status=played&order=desc",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic " . $auth ,
    "cache-control: no-cache",
    "postman-token: dc803738-1a57-3107-9da8-7994854f71be"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
   $checkdb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Resultate-" . $teamId . "'"); 
 if($checkdb == null){
	$table = $wpdb->prefix."shv_plugin";
    $wpdb->insert( 
        $table, 
        array( 
	        'time' =>$jetzt,
	        'name' => "Resultate-" . $teamId,
            'data' => $response
            )        );

}else{
	
$table = $wpdb->prefix."shv_plugin";
    $wpdb->update( 
	$table, 
	array( 
	        'time' =>$jetzt,
	        'name' => "Resultate-" . $teamId,
            'data' => $response
            ), 
	array( 'name' => "Resultate-" . $teamId )
);

}
}    
       

            
}
function fetch_spiele(){
$id = get_option('vereinsId');
$auth = get_option('auth');
	$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.handball.ch/rest/v1/clubs/" . $id ."/games?status=planned",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic " . $auth,
    "cache-control: no-cache",
    "postman-token: f6a3a98b-c89e-ae91-02ef-3b40b2eea428"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
echo $err;

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
$jetzt = date('Y-m-d H:i:s');
global $wpdb;
$checkdb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Spiele'");

if($checkdb == null){
	$table = $wpdb->prefix."shv_plugin";
    $wpdb->insert( 
        $table, 
        array( 
	        'time' =>$jetzt,
	        'name' => "Spiele",
            'data' => $response
            )        );	
}else{
	
$table = $wpdb->prefix."shv_plugin";
    $wpdb->update( 
	$table, 
	array( 
	        'time' =>$jetzt,
	        'name' => "Spiele",
            'data' => $response
            ), 
	array( 'name' => "Spiele" )
);
}	

}

}
function fetch_resultate(){
$id = get_option('vereinsId');
$auth = get_option('auth');
	$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.handball.ch/rest/v1/clubs/" . $id ."/games?status=played&order=desc",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic " . $auth,
    "cache-control: no-cache",
    "postman-token: f6a3a98b-c89e-ae91-02ef-3b40b2eea428"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
$jetzt = date('Y-m-d H:i:s');
global $wpdb;
$checkdb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Resultate'");

if($checkdb == null){
	$table = $wpdb->prefix."shv_plugin";
    $wpdb->insert( 
        $table, 
        array( 
	        'time' =>$jetzt,
	        'name' => "Resultate",
            'data' => $response
            )        );

	
}else{
	
$table = $wpdb->prefix."shv_plugin";
    $wpdb->update( 
	$table, 
	array( 
	        'time' =>$jetzt,
	        'name' => "Resultate",
            'data' => $response
            ), 
	array( 'name' => "Resultate" )
);
}	

}

}
function fetch_teams(){
	 create_shv_plugin_table();
$id = get_option('vereinsId');
$auth = get_option('auth');

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://api.handball.ch/rest/v1/clubs/" . $id ."/teams",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: Basic " . $auth,
    "cache-control: no-cache",
    "postman-token: f6a3a98b-c89e-ae91-02ef-3b40b2eea428"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {

$jetzt = date('Y-m-d H:i:s');
global $wpdb;
$checkdb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");

if($checkdb == null){
	$table = $wpdb->prefix."shv_plugin";
    $wpdb->insert( 
        $table, 
        array( 
	        'time' =>$jetzt,
	        'name' => "Teams",
            'data' => $response
            )        );

	
}else{
	
$table = $wpdb->prefix."shv_plugin";
    $wpdb->update( 
	$table, 
	array( 
	        'time' =>$jetzt,
	        'name' => "Teams",
            'data' => $response
            ), 
	array( 'name' => "Teams" )
);
}	

}

 
    
}
function getdb_data($dbwhere){
global $wpdb;
$jetzt = date('Y-m-d H:i:s');
$ifempty = '2017-11-28 00:02:34';
$datadb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = '" . $dbwhere . "'");
$data = json_decode(json_encode($datadb), True);
$datetime1 = new DateTime($data['time']);
$datetime2 = new DateTime($jetzt);
$interval = $datetime1->diff($datetime2);
//
//echo $interval ;
//
$update = get_option('update');
if ($interval->format('%i') > $update){
	//echo "get Data from API";
	if ($dbwhere == "Spiele") {
    fetch_spiele();
} elseif ($dbwhere == "Resultate") {
    fetch_resultate();
} elseif ($dbwhere == "SpieleTeams") {
    fetch_spiele_teams();
} elseif ($dbwhere == "ResultateTeams") {
    fetch_resultate_teams();
}
a:
$datadb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = '" . $dbwhere . "'");
$data = json_decode(json_encode($datadb), True);
$response = $data['data'];
        }
        else{
	        //echo "get Data from DB";
	   $response = $data['data'];
        }
 $assoc = true;
$resultdata = json_decode ($response, $assoc);
//print_r($resultdata);
if (!empty($resultdata['Message'])){
	//echo "empty";
	sleep(1);
	//echo "slept";
	if ($dbwhere == "Spiele") {
    fetch_spiele();
    goto a;
} elseif ($dbwhere == "Resultate") {
    fetch_resultate();
    goto a;
} elseif ($dbwhere == "SpieleTeams") {
    fetch_spiele_teams();
    goto a;
} elseif ($dbwhere == "ResultateTeams") {
    fetch_resultate_teams();
    goto a;
}

}	
return $resultdata;	
}
function getdb_data_team($teamId, $dbwhere){
global $wpdb;
$jetzt = date('Y-m-d H:i:s');
$ifempty = '2017-11-28 00:02:34';
$datadb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = '" . $dbwhere . "-" . $teamId . "'");
$data = json_decode(json_encode($datadb), True);
$datetime1 = new DateTime($data['time']);
$datetime2 = new DateTime($jetzt);
$interval = $datetime1->diff($datetime2);
//echo $interval ;

$update = get_option('update');
if ($interval->format('%i') > $update){
	if ($dbwhere == "Spiele") {
    fetch_spiele_teams_id($teamId);
} elseif ($dbwhere == "Resultate") {
    fetch_resultate_teams_id($teamId);
}
	
	//echo "get Data from API";
b:
$datadb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = '" . $dbwhere . "-" . $teamId . "'");
$data = json_decode(json_encode($datadb), True);
$response = $data['data'];
        }
        else{
	        //echo "get Data from DB";
	   $response = $data['data'];
	   
        }
 $assoc = true;
$result = json_decode ($response, $assoc);
if (!empty($result['Message'])){
	//echo "empty";
	sleep(1);
	//echo "slept";
		if ($dbwhere == "Spiele") {
    fetch_spiele_teams_id($teamId);
    goto b;
} elseif ($dbwhere == "Resultate") {
    fetch_resultate_teams_id($teamId);
    goto b;
}
}
return $result;	
}
function create_spiele_list($atts, $result){
$domain = get_site_url(); //or home
$domain = str_replace('http://', '', $domain);
$domain = str_replace('https://', '', $domain);
//$domain = str_replace('www.', '', $domain); //add the . after the www if you don't want it
//$domain = strstr($domain, '/', true); //PHP5 only, this is in case WP is not root
$olddate = "x";
$count = 1;
$spiele = $atts[ 'spieltage' ]+1;
$liga = (bool) $atts[ 'liga' ];
$resultat = (bool) $atts[ 'resultat' ];
$calendar = get_option('calendar');
if(array_key_exists('teamid', $atts)){
		//nothing
	}else{
		$atts[ 'teamid' ] = "alle";
	}
settype($count, "integer");
	if($calendar == "true"){
	if ($resultat === false){
	global $wpdb;
	$teamsjson = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");
	$teamsphp = json_decode(json_encode($teamsjson), True);
	$teams = json_decode ($teamsphp['data'], true);
//print_r($teams);
	$shvSpielplanoutput = "<select class='teamid' onchange='newlink = document.getElementsByClassName(\"teamid\")[0].value; link = document.getElementsByClassName(\"calfeed\"); link2 = document.getElementsByClassName(\"calfile\"); link[0].href = newlink; var newlink2 = newlink.replace(\"webcal\", \"http\"); link2[0].href = newlink2;'>";
	$shvSpielplanoutput .= "<option value='webcal://www." .  get_site_url() . "/feed/kalender.ics?team=alle'>Ganzer Verein</option>";
	foreach($teams as $team){
	if($team['teamId'] == $atts[ 'teamid' ]){
	$shvSpielplanoutput .= "<option value='webcal://www." .  $domain . "/feed/kalender.ics?team=" . $team[ 'teamId' ] ."' selected>" . $team['groupText']. " " . $team['teamName'] . "</option>";
	}else{
		$shvSpielplanoutput .= "<option value='webcal://www." .  $domain . "/feed/kalender.ics?team=" . $team[ 'teamId' ] ."'>" . $team['groupText']. " " . $team['teamName'] . "</option>";
	}
	}
	$shvSpielplanoutput .= "</select>";
	$shvSpielplanoutput .= "<div class='cal-link'>";
	$shvSpielplanoutput .= "<a class='calfeed' href='webcal://www." .  $domain . "/feed/kalender.ics?team=" . $atts[ 'teamid' ] ."'>Kalender abonnieren</a>";
	$shvSpielplanoutput .= " <a class='calfile' href='" .  get_site_url() . "/feed/kalender.ics?team=" . $atts[ 'teamid' ] ."'>Kalenderdatei herunterladen</a>";
	$shvSpielplanoutput .= "</div>";
	$shvSpielplanoutput .= "<div class='Resultate-list'>";
	}else{$shvSpielplanoutput = "<div class='Resultate-list'>";}
	}else{
	$shvSpielplanoutput = "<div class='Resultate-list'>";
	}
if (empty($result)) {
    $shvSpielplanoutput .= '<blockquote class="nodata">Zur Zeit sind keine Spiele oder Resultate vorhanden.</blockquote>';
}

	
foreach($result as $gameData){
	$gameDatetime =  explode("T", $gameData["gameDateTime"]);
	$date = $gameDatetime[0];
	$time = $gameDatetime[1];
	$mapmeta = '<span id="_address' . $count .'" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';
	$mapmeta .='<meta itemprop="streetAddress" content="' . $gameData["venueAddress"] . '">';
	$mapmeta .='<meta itemprop="addressLocality" content="' . $gameData["venueCity"] . '">';
	$mapmeta .='<meta itemprop="postalCode" content="'. $gameData["venueZip"]. '"></span>';
	$maplink = "https://maps.google.com/?q=" . $gameData["venueAddress"] . "," . $gameData["venueCity"] . "," . $gameData["venueZip"];
	$venue = $mapmeta . "<a itemprop='name' href='" . $maplink . "'target='_blank'> " . $gameData["venue"] . "</a>";
	if($olddate == "x"){
		if($count != $spiele){
		//nothing
	}else{break;}
		$shvSpielplanoutput .= "<div class='shv-datum-container'>" . date("d. m. Y", strtotime($date)) . "</div><div class='shv-newday'>";
		$shvSpielplanoutput .= "<div class='shv-title-row'>";
		if ($liga === true){
	    $shvSpielplanoutput .= "<div class='shv-spiele-col'><span class='shv-spiele-titel'>Liga: </span></div>";
	}
	if ($resultat === true){
	$shvSpielplanoutput .= "<div class='shv-spiele-col'><span class='shv-spiele-titel'>Resultat: </span></div>";
	}
		$shvSpielplanoutput .= "<div class='shv-spiele-col'><span class='shv-spiele-titel'>Heim: </span></div><div class='shv-spiele-col'><span class='shv-spiele-titel'>Gast: </span></div><div class='shv-spiele-col'><span class='shv-spiele-titel'>Zeit: </span></div><div class='shv-spiele-col'><span class='shv-spiele-titel'>Ort: </span></div>";
	
		$shvSpielplanoutput .= "<div class='shv-spiele-col'><span class='shv-spiele-titel'>Details: </span></div>";
		
	$shvSpielplanoutput .= "</div>";
		$count = $count+1;
		
	} elseif ($date == $olddate) {
		//nothing

	}else{
		if($count != $spiele){
		//nothing
	}else{break;}
		$shvSpielplanoutput .= "</div><div class='shv-datum-container'>" . date("d. m. Y", strtotime($date)) . "</div><div class='shv-newday'>";
		$shvSpielplanoutput .= "<div class='shv-title-row'>";
		if ($liga === true){
	    $shvSpielplanoutput .= "<div class='shv-spiele-col'><span class='shv-spiele-titel'>Liga: </span></div>";
	}
	if ($resultat === true){
	$shvSpielplanoutput .= "<div class='shv-spiele-col'><span class='shv-spiele-titel'>Resultat: </span></div>";
	}
		$shvSpielplanoutput .= "<div class='shv-spiele-col'><span class='shv-spiele-titel'>Heim: </span></div><div class='shv-spiele-col'><span class='shv-spiele-titel'>Gast: </span></div><div class='shv-spiele-col'><span class='shv-spiele-titel'>Zeit: </span></div><div class='shv-spiele-col'><span class='shv-spiele-titel'>Ort: </span></div>";
	
		$shvSpielplanoutput .= "<div class='shv-spiele-col'><span class='shv-spiele-titel'>Details: </span></div>";

	$shvSpielplanoutput .= "</div>";
		$count = $count+1;
	};
	$shvSpielplanoutput .= "<div class='shv-spiel-container'  itemscope itemtype='http://schema.org/Event'><meta itemprop='name' content='" . $gameData["leagueShort"] . " " . $gameData["teamAName"] . " vs. " . $gameData["teamBName"] . "'>";
	if ($liga === true){
	$shvSpielplanoutput .= "<div class='shv-cell-container'><span class='shv-spiele-span'>Liga: </span><div class='shv-spiele-after'>" . $gameData["leagueShort"] . "</div></div>";
	}
	if ($resultat === true){
	$shvSpielplanoutput .= "<div class='shv-cell-container'><span class='shv-spiele-span'>Resultat: </span><div class='shv-spiele-after'>" . $gameData["teamAScoreFT"] . ":" . $gameData["teamBScoreFT"] . " (" . $gameData["teamAScoreHT"] . ":" . $gameData["teamBScoreHT"] . ")</div></div>";
	}
	$shvSpielplanoutput .= "<div class='shv-cell-container'><span class='shv-spiele-span'>Heim: </span><div class='shv-spiele-after'>" . $gameData["teamAName"] . "</div></div>";
	$shvSpielplanoutput .= "<div class='shv-cell-container'><span class='shv-spiele-span'>Gast: </span><div class='shv-spiele-after'>" . $gameData["teamBName"] . "</div></div>";
	$shvSpielplanoutput .= "<div class='shv-cell-container'><span class='shv-spiele-span'>Zeit: </span><div class='shv-spiele-after'><span itemprop='startDate' content='" . $gameData['gameDateTime'] . "'>" . date("H:i", strtotime($time)) . "</div></div>";
	$shvSpielplanoutput .= "<div class='shv-cell-container'><span class='shv-spiele-span'>Ort: </span><div class='shv-spiele-after' itemprop='location' itemscope itemtype='http://schema.org/Place'>" . $venue . "</div></div>";
	
		$statlink = "https://www.handball.ch/de/matchcenter/spiele/" . $gameData["gameId"];
		$shvSpielplanoutput .= "<div class='shv-cell-container'><span class='shv-spiele-span'>Details: </span><div class='shv-spiele-after'><a href='" . $statlink . "' target='_blank' class='statslink'></a></div></div>";;
	$shvSpielplanoutput .= "</div>";
	$olddate = $date;
}
if (empty($result)) {
    $shvSpielplanoutput .= '</div>';
}else {
$shvSpielplanoutput .= "<div class='shv-clear'></div></div></div>";
}

return $shvSpielplanoutput;
}
function spielplan_shortcode($atts){
$dbwhere = "Spiele";
$result = getdb_data($dbwhere);
$output = create_spiele_list($atts, $result);
return $output;
}
function spielplan_team_shortcode($atts){
$teamId = $atts['teamid'];
$dbwhere = "Spiele";
$resultteam = getdb_data_team($teamId, $dbwhere);
$output = "<h4 class='page-title'>Spiele</h4>";    
$output .= create_spiele_list($atts, $resultteam);   
return $output;
}
function resultate_shortcode($atts){
$dbwhere = "Resultate";
$result = getdb_data($dbwhere);
$output = create_spiele_list($atts, $result);
return $output;
}
function resultate_team_shortcode($atts){
$teamId = $atts['teamid'];
$dbwhere = "Resultate";
$resultteam = getdb_data_team($teamId, $dbwhere);
$output = "<h4 class='page-title'>Resultate</h4>";      
$output .= create_spiele_list($atts, $resultteam); 
return $output;
}

function rangliste_shortcode($atts){
global $wpdb;
$teamsjson = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");
$teamsphp = json_decode(json_encode($teamsjson), True);
$teams = json_decode ($teamsphp['data'], true);
//print_r($teams);

$Ranglistenjetzt = date('Y-m-d H:i:s');
$dataranglistendb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Ranglisten'");
$dataranglisten = json_decode(json_encode($dataranglistendb), True);
$jetzt = date('Y-m-d H:i:s');
$datetime1 = new DateTime($dataranglisten['time']);
$datetime2 = new DateTime($jetzt);
$interval = $datetime1->diff($datetime2);
$update = get_option('update');
if ($interval->format('%i') > $update){
fetch_ranglisten();
$dataranglistendb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Ranglisten'");
$dataranglisten = json_decode(json_encode($dataranglistendb), True);
$result = json_decode ($dataranglisten['data'], true);
	}else{
		$result = json_decode ($dataranglisten['data'], true);
	}

	$output = "<div>";
	$oldteamid = "x";
foreach ($teams as $team){
	$teamId = $team['teamId'];
	//echo $teamId;
	if($oldteamid == $teamId){
	//nothing
	}else{
	$output .= "<div class='tab'><input class='accordion-input ' id='tab-" . $teamId ."' type='checkbox' name='tabs'><label class='ranglisten-label' for='tab-" . $teamId ."'>" . $result[$teamId]['groupText'] . "</label><div class='tab-content'>";
	$output .= "<div class='rangliste'><div class='rangliste-header'><span class='rangliste-rang'>Rang</span><span class='rangliste-team'>Team</span><span class='rangliste-siege'>S/U/N</span><span class='rangliste-tore'>T+/TD/T-</span><span class='rangliste-siele'>Spiele</span><span class='rangliste-punkte'>Punkte</span></div>";
	foreach($result[$teamId]['ranking'] as $rangliste){
		$output .= "<div class='rangliste-row'><span class='rangliste-rang'>" . $rangliste['rank'] . "</span><span class='rangliste-team' data-team='" . $rangliste['teamName'] . "'>" . $rangliste['teamName'] . "</span><span class='rangliste-siege'>" . $rangliste['totalWins'] . "/" . $rangliste['totalDraws'] . "/" . $rangliste['totalLoss'] . "</span><span class='rangliste-tore'>" . $rangliste['totalScoresPlus'] . "/" . $rangliste['totalScoresDiff'] . "/" . $rangliste['totalScoresMinus'] . "</span><span class='rangliste-spiele'>" . $rangliste['totalGames'] . "</span><span class='rangliste-punkte'>" . $rangliste['totalPoints'] . "</span></div>";
		$oldteamid = $teamId;      
	}
	$output .= "</div></div></div>";
	}
if($result[$teamId]['groupText'] == "U13BeH-65"){break;}  
}
$output .= "</div>";

return $output;
}
function rangliste_team_shortcode($atts){
$teamId = $atts['teamid'];
global $wpdb;
$teamsjson = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");
$teamsphp = json_decode(json_encode($teamsjson), True);
$teams = json_decode ($teamsphp['data'], true);
//print_r($teams);

$Ranglistenjetzt = date('Y-m-d H:i:s');
$dataranglistendb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Ranglisten'");
$dataranglisten = json_decode(json_encode($dataranglistendb), True);
$jetzt = date('Y-m-d H:i:s');
$datetime1 = new DateTime($dataranglisten['time']);
$datetime2 = new DateTime($jetzt);
$interval = $datetime1->diff($datetime2);
$update = get_option('update');
if ($interval->format('%i') > $update){
fetch_ranglisten();
$dataranglistendb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Ranglisten'");
$dataranglisten = json_decode(json_encode($dataranglistendb), True);
$result = json_decode ($dataranglisten['data'], true);
	}else{
		$result = json_decode ($dataranglisten['data'], true);
	}

	$output = "<div>";
	$output .= "<div class='rangliste'><div class='rangliste-header'><span class='rangliste-rang'>Rang</span><span class='rangliste-team'>Team</span><span class='rangliste-siege'>S/U/N</span><span class='rangliste-tore'>T+/TD/T-</span><span class='rangliste-siele'>Spiele</span><span class='rangliste-punkte'>Punkte</span></div>";
	foreach($result[$teamId]['ranking'] as $rangliste){
		$output .= "<div class='rangliste-row'><span class='rangliste-rang'>" . $rangliste['rank'] . "</span><span class='rangliste-team' data-team='" . $rangliste['teamName'] . "'>" . $rangliste['teamName'] . "</span><span class='rangliste-siege'>" . $rangliste['totalWins'] . "/" . $rangliste['totalDraws'] . "/" . $rangliste['totalLoss'] . "</span><span class='rangliste-tore'>" . $rangliste['totalScoresPlus'] . "/" . $rangliste['totalScoresDiff'] . "/" . $rangliste['totalScoresMinus'] . "</span><span class='rangliste-spiele'>" . $rangliste['totalGames'] . "</span><span class='rangliste-punkte'>" . $rangliste['totalPoints'] . "</span></div>";
		      
	}
$output .= "</div></div>";

return $output;
}
function spieler_stats_shortcode($atts){
	$goupid = $atts['groupid'];
	$teamid = $atts['teamid'];
	libxml_use_internal_errors(true);
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "http://vvs12.handball.ch/content.asp?include=torestat&mode=anzeige&section=team&teamid=" . $teamid . "&saisonid=2017&gruppeid=" . $goupid . "&isportal=0&currentverband=shv",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Cache-Control: no-cache",
        "Postman-Token: a298dafd-038f-9920-39a8-55f57af83c6a"
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
    echo "cURL Error #:" . $err;
} else {
        $dom = new DOMDocument();
        $dom->loadHTML($response);
        foreach($dom->getElementsByTagName('tr') as $node)
        {
            $array[] = $dom->saveHTML($node);
        }
        $array2 = array_filter($array, function($value) { return $value !== ''; });
        foreach($array2 as $key=>$result){
            if($key > 10){
                $array3[$key] = unserialize(serialize($result));
            }
        }
        foreach($array3 as $spieler){
            $data3 = array_filter(explode(" ",strip_tags($spieler)), function($value) { return $value !== ''; });
            $array4[] = $data3;
        }
        $count = 1;
        $output = "<div class='spielerliste'><div class='title-row'><span class='table-cell'>Nr</span><span class='table-cell'>Name</span><span class='table-cell'>T</span><span class='table-cell'>7m</span><span class='table-cell'>V</span><span class='table-cell hide'>2'</span><span class='table-cell hide'>2te</span><span class='table-cell hide'>3te</span><span class='table-cell'>tot. 2</span><span class='table-cell'>Rot.</span><span class='table-cell'>Disq</span><span class='table-cell'>Spiele</span><span class='table-cell hide''>Tore/Spiel</span></div>";
        
        foreach($array4 as $key => $spieler){
            if(count($spieler) == 14){
                $nachname = $spieler['2'];
                $vorname = $spieler['3'];
                $tore = $spieler['7'];
                $penalty = $spieler['11'];
                $verwarnung = $spieler['15'];
                $min  = $spieler['19'];
                $min2 = $spieler['23'];
                $min3 = $spieler['27'];
                $mintot = $spieler['31'];
                $rot = $spieler['35'];
                $rotblau = $spieler['39'];
                $spiele = $spieler['43'];
                $torespiel = $spieler['47'];
            
            $output .= "<div class='table-row'><span class='table-cell'>". $count."</span><span class='table-cell'>". $nachname." ".$vorname."</span><span class='table-cell'>".$tore."</span><span class='table-cell'>".$penalty."</span><span class='table-cell'>".$verwarnung."</span><span class='table-cell hide'>".$min."</span><span class='table-cell hide'>".$min2."</span><span class='table-cell hide'>".$min3."</span><span class='table-cell'>".$mintot."</span><span class='table-cell'>".$rot."</span><span class='table-cell'>".$rotblau."</span><span class='table-cell'>".$spiele."</span><span class='table-cell hide'>".$torespiel."</span></div>";
            }else if(count($spieler) == 15){
                $name1 = $spieler['2'];
                $name2 = $spieler['3'];
                $name3 = $spieler['4'];
                $tore = $spieler['8'];
                $penalty = $spieler['12'];
                $verwarnung = $spieler['16'];
                $min  = $spieler['20'];
                $min2 = $spieler['24'];
                $min3 = $spieler['28'];
                $mintot = $spieler['32'];
                $rot = $spieler['36'];
                $rotblau = $spieler['40'];
                $spiele = $spieler['44'];
                $torespiel = $spieler['48'];
            
            $output .= "<div class='table-row'><span class='table-cell'>". $count."</span><span class='table-cell'>". $name1." ".$name2." ".$name3."</span><span class='table-cell'>".$tore."</span><span class='table-cell'>".$penalty."</span><span class='table-cell'>".$verwarnung."</span><span class='table-cell hide'>".$min."</span><span class='table-cell hide'>".$min2."</span><span class='table-cell hide'>".$min3."</span><span class='table-cell'>".$mintot."</span><span class='table-cell'>".$rot."</span><span class='table-cell'>".$rotblau."</span><span class='table-cell'>".$spiele."</span><span class='table-cell hide'>".$torespiel."</span></div>";
            }else if(count($spieler) == 16){
                $name1 = $spieler['2'];
                $name2 = $spieler['3'];
                $name3 = $spieler['4'];
                $name4 = $spieler['5'];
                $tore = $spieler['9'];
                $penalty = $spieler['13'];
                $verwarnung = $spieler['17'];
                $min  = $spieler['21'];
                $min2 = $spieler['25'];
                $min3 = $spieler['29'];
                $mintot = $spieler['33'];
                $rot = $spieler['37'];
                $rotblau = $spieler['41'];
                $spiele = $spieler['45'];
                $torespiel = $spieler['49'];
            
            $output .= "<div class='table-row'><span class='table-cell'>". $count."</span><span class='table-cell'>". $name1." ".$name2." ".$name3." ".$name4."</span><span class='table-cell'>".$tore."</span><span class='table-cell'>".$penalty."</span><span class='table-cell'>".$verwarnung."</span><span class='table-cell hide'>".$min."</span><span class='table-cell hide'>".$min2."</span><span class='table-cell hide'>".$min3."</span><span class='table-cell'>".$mintot."</span><span class='table-cell'>".$rot."</span><span class='table-cell'>".$rotblau."</span><span class='table-cell'>".$spiele."</span><span class='table-cell hide'>".$torespiel."</span></div>";
            }else if(count($spieler) == 17){
                $name1 = $spieler['2'];
                $name2 = $spieler['3'];
                $name3 = $spieler['4'];
                $name4 = $spieler['5'];
                $name5 = $spieler['6'];
                $tore = $spieler['10'];
                $penalty = $spieler['14'];
                $verwarnung = $spieler['18'];
                $min  = $spieler['22'];
                $min2 = $spieler['26'];
                $min3 = $spieler['30'];
                $mintot = $spieler['34'];
                $rot = $spieler['38'];
                $rotblau = $spieler['42'];
                $spiele = $spieler['46'];
                $torespiel = $spieler['50'];
            
             $output .= "<div class='table-row'><span class='table-cell'>". $count."</span><span class='table-cell'>". $name1." ".$name2." ".$name3." ".$name4." ".$name5."</span><span class='table-cell'>".$tore."</span><span class='table-cell'>".$penalty."</span><span class='table-cell'>".$verwarnung."</span><span class='table-cell hide'>".$min."</span><span class='table-cell hide'>".$min2."</span><span class='table-cell hide'>".$min3."</span><span class='table-cell'>".$mintot."</span><span class='table-cell'>".$rot."</span><span class='table-cell'>".$rotblau."</span><span class='table-cell'>".$spiele."</span><span class='table-cell hide'>".$torespiel."</span></div>";
            }else if(count($array4)-3 == $key){
                $name1 = $spieler['2'];
                $tore = $spieler['6'];
                $penalty = $spieler['10'];
                $verwarnung = $spieler['14'];
                $min  = $spieler['18'];
                $min2 = $spieler['22'];
                $min3 = $spieler['26'];
                $mintot = $spieler['30'];
                $rot = $spieler['34'];
                $rotblau = $spieler['38'];
            $output .= "<div class='table-row'><span class='table-cell'></span><span class='table-cell'>". $name1."</span><span class='table-cell'>".$tore."</span><span class='table-cell'>".$penalty."</span><span class='table-cell'>".$verwarnung."</span><span class='table-cell hide'>".$min."</span><span class='table-cell hide'>".$min2."</span><span class='table-cell hide'>".$min3."</span><span class='table-cell'>".$mintot."</span><span class='table-cell'>".$rot."</span><span class='table-cell'>".$rotblau."</span><span class='table-cell'></span><span class='table-cell'></span></div>";
            }
            $count = $count+1;
        }
        $output .= "</div>";

$newoutput = '<div class="spielerliste-container">';
$newoutput .= $output;
$newoutput .= '</div>';
return $newoutput;
        
       }
}

function kalenderurls_shortcode(){
global $wpdb;
$teamsjson = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");
$teamsphp = json_decode(json_encode($teamsjson), True);
$teams = json_decode ($teamsphp['data'], true);
//print_r($teams);
$domain = get_site_url(); //or home
$domain = str_replace('http://', '', $domain);
$domain = str_replace('https://', '', $domain);
//$domain = str_replace('www.', '', $domain); //add the . after the www if you don't want it
//$domain = strstr($domain, '/', true); //PHP5 only, this is in case WP is not root
$output = "<div>";
foreach($teams as $team){
	$output.="<div>";
	$output.= "<h3>" . $team['groupText']. " " . $team['teamName'] . "</h3>";
	$output .= "<div class='cal-link'>";
	$output .= "<a class='calfeed' href='webcal://www." .  $domain . "/feed/kalender.ics?team=" . $team[ 'teamId' ] ."'>Im Kalender abonnieren</a>";
	$output .= " <a class='calfile' href='" .  get_site_url() . "/feed/kalender.ics?team=" . $team[ 'teamId' ] ."'>Kalenderdatei herunterladen</a>";
	$output .= "</div>";
	if($team['groupText'] == "U13ChQ-03"){break;}  
}
$output .= "</div>";


return $output;
}

add_shortcode('Spielplan', 'spielplan_shortcode');
add_shortcode('Spielplanteam', 'spielplan_team_shortcode');
add_shortcode('Resultate', 'resultate_shortcode');
add_shortcode('Resultateteam', 'resultate_team_shortcode');
add_shortcode('Rangliste', 'rangliste_shortcode');
add_shortcode('Ranglisteteam', 'rangliste_team_shortcode');
add_shortcode('Spielerstats', 'spieler_stats_shortcode');
add_shortcode('Kalenderurls', 'kalenderurls_shortcode');