<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Register and load the widget
function shv_resultate_load_widget() {
    register_widget( 'shv_resultate_widget' );
}
add_action( 'widgets_init', 'shv_resultate_load_widget' );
 
// Creating the widget 
class shv_resultate_widget extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'shv_resultate_widget', 
 
// Widget name will appear in UI
__('shv Resultate Widget', 'shv_resultate_widget_domain'), 
 
// Widget description
array( 'description' => __( 'SHV-Resultate', 'shv_resultate_widget_domain' ), ) 
);
}

// Creating widget front-end
 
public function widget( $args, $instance ) {
	
global $wpdb;
	$Resultatejetzt = date('Y-m-d H:i:s');
$ifempty = '2017-11-28 00:02:34';
$dataResultatedb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Resultate'");
$dataResultate = json_decode(json_encode($dataResultatedb), True);
$Resultatedatetime1 = new DateTime($dataResultate['time']);
$Resultatedatetime2 = new DateTime($Resultatejetzt);
$interval = $Resultatedatetime1->diff($Resultatedatetime2);
//echo $interval ;
$update = get_option('update');
if ($interval->format('%i') > $update){
	//echo "get Data from API";
fetch_resultate();
$dataResultatedb = $wpdb->get_row("SELECT * FROM wp_shv_plugin WHERE name = 'Resultate'");
$dataResultate = json_decode(json_encode($dataResultatedb), True);
$responseResultate = $dataResultate['data'];
        }
        else{
	        //echo "get Data from DB";
	   $responseResultate = $dataResultate['data'];
	   
        }
$shvResultateoutput = "<div class='Resultate-list'>";
 $assoc = true;
$result = json_decode ($responseResultate, $assoc);
$olddate = "x";
$count = 1;
settype($count, "integer");
foreach($result as $gameData){
	$gameDatetime =  explode("T", $gameData["gameDateTime"]);
	$Resultatedate = $gameDatetime[0];
	$time = $gameDatetime[1];
	$maplink = "https://maps.google.com/?q=" . $gameData["venueAddress"] . "," . $gameData["venueCity"] . "," . $gameData["venueZip"];
	$venue = "<a href='" . $maplink . "'target='_blank'> " . $gameData["venue"] . "</a>";
	if($olddate == "x"){
		$shvResultateoutput .= "<div class='shv-datum-container'>" . date("d. m. Y", strtotime($Resultatedate)) . "</div><div class='shv-newday'>";
		$count = $count+1;
		
	} elseif ($Resultatedate == $olddate) {
		//nothing

	}else{
		$shvResultateoutput .= "</div><div class='shv-datum-container'>" . date("d. m. Y", strtotime($Resultatedate)) . "</div><div class='shv-newday'>";
		$count = $count+1;
	};
	$shvResultateoutput .= "<div class='shv-spiel-container'><div class='shv-liga-container'><span class='shv-spiele-titel'>Liga: </span><div class='shv-spiele-after'>" . $gameData["leagueShort"] . "</div></div>";
	$shvResultateoutput .= "<div class='shv-resultat-container'><span class='shv-spiele-titel'>Resultat: </span><div class='shv-spiele-after'>" . $gameData["teamAScoreFT"] . ":" . $gameData["teamBScoreFT"] . " (" . $gameData["teamAScoreHT"] . ":" . $gameData["teamBScoreHT"] . ")</div></div>";
	$shvResultateoutput .= "<div class='shv-heim-container'><span class='shv-spiele-titel'>Heim: </span><div class='shv-spiele-after'>" . $gameData["teamAName"] . "</div></div>";
	$shvResultateoutput .= "<div class='shv-gast-container'><span class='shv-spiele-titel'>Gast: </span><div class='shv-spiele-after'>" . $gameData["teamBName"] . "</div></div>";
	$shvResultateoutput .= "<div class='shv-ort-container'><span class='shv-spiele-titel'>Ort: </span><div class='shv-spiele-after'>" . $gameData["venue"] . "</div></div></div>";
	
	$olddate = $Resultatedate;
}

$shvResultateoutput .= "<div class='shv-clear'></div></div></div>";

$title = apply_filters( 'widget_title', $instance['title'] );
 
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
 
// This is where you run the code and display the output
echo __( $shvResultateoutput, 'shv_resultate_widget_domain' );
echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'shv_resultate_widget_domain' );
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