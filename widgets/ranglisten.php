<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Register and load the widget
function shv_ranglisten_load_widget() {
    register_widget( 'shv_ranglisten_widget' );
}
add_action( 'widgets_init', 'shv_ranglisten_load_widget' );
 
// Creating the widget 
class shv_ranglisten_widget extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'shv_ranglisten_widget', 
 
// Widget name will appear in UI
__('shv ranglisten Widget', 'shv_ranglisten_widget_domain'), 
 
// Widget description
array( 'description' => __( 'SHV-ranglisten', 'shv_ranglisten_widget_domain' ), ) 
);
}

// Creating widget front-end
 
public function widget( $args, $instance ) {
$auth = get_option('auth');	
global $wpdb;
$teamsjson = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Teams'");
$teamsphp = json_decode(json_encode($teamsjson), True);

$teams = json_decode ($teamsphp['data'], true);
//print_r($teams);

$Ranglistenjetzt = date('Y-m-d H:i:s');
$dataranglistendb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Ranglisten'");
$dataranglisten = json_decode(json_encode($dataranglistendb), True);
if($dataranglistendb == null){
	$ranglisten = array();
    foreach ($teams as $team){
	     $teamId = $team['teamId'];
	      
	     
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
    "authorization: Basic " . $auth,
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
 print_r($ranglisten);
 $table = $wpdb->prefix."shv_plugin";
    $wpdb->insert( 
        $table, 
        array( 
	        'time' =>$Ranglistenjetzt,
	        'name' => "Ranglisten",
            'data' => $data
            )        );
       
}else{
	//print_r($dataranglisten['data']);

	$result = json_decode ($dataranglisten['data'], true);
	//print_r($result);
}
$jetzt = date('Y-m-d H:i:s');
$datetime1 = new DateTime($dataranglisten['time']);
$datetime2 = new DateTime($jetzt);
$interval = $datetime1->diff($datetime2);
$update = get_option('update');
if ($interval->format('%i') > $update){
	$result = array();
    foreach ($teams as $team){
	     $teamId = $team['teamId'];
	      
	     
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
    "authorization: Basic " . $auth,
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
  $resultapi = json_decode ($response, $assoc);
  $result[$teamId] = $resultapi;
}    
       }
 $data = json_encode($result);

	$table = $wpdb->prefix."shv_plugin";
    $wpdb->update( 
	$table, 
	array( 
	        'time' =>$Ranglistenjetzt,
	        'name' => "Ranglisten",
            'data' => $data
            ), 
	array( 'name' => "Ranglisten" )
);

	}




	$output = "<div>";
foreach ($teams as $team){
	$teamId = $team['teamId'];
	//echo $teamId;
	$output .= "<div class='tab'><input class='accordion-input ' id='tab-" . $teamId ."' type='checkbox' name='tabs'><label class='ranglisten-label' for='tab-" . $teamId ."'>" . $result[$teamId]['groupText'] . "</label><div class='tab-content'>";
	$output .= "<div class='rangliste'><div class='rangliste-header'><span class='rangliste-rang'>Rang</span><span class='rangliste-team'>Team</span><span class='rangliste-siege'>S/U/N</span><span class='rangliste-tore'>T+/TD/T-</span><span class='rangliste-siele'>Spiele</span><span class='rangliste-punkte'>Punkte</span></div>";
	foreach($result[$teamId]['ranking'] as $rangliste){
		$output .= "<div class='rangliste-row'><span class='rangliste-rang'>" . $rangliste['rank'] . "</span><span class='rangliste-team'>" . $rangliste['teamName'] . "</span><span class='rangliste-siege'>" . $rangliste['totalWins'] . "/" . $rangliste['totalDraws'] . "/" . $rangliste['totalLoss'] . "</span><span class='rangliste-tore'>" . $rangliste['totalScoresPlus'] . "/" . $rangliste['totalScoresDiff'] . "/" . $rangliste['totalScoresMinus'] . "</span><span class='rangliste-spiele'>" . $rangliste['totalGames'] . "</span><span class='rangliste-punkte'>" . $rangliste['totalPoints'] . "</span></div>";
	}
	$output .= "</div></div></div>";
	        
}
$output .= "</div>";


$title = apply_filters( 'widget_title', $instance['title'] );
 
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
 
// This is where you run the code and display the output
echo __( $output, 'shv_ranglisten_widget_domain' );
echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'shv_ranglisten_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
     
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class shv_widget ends here
?>