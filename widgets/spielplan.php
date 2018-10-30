<?php 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Register and load the widget
function shv_spielplan_load_widget() {
    register_widget( 'shv_spielplan_widget' );
}
add_action( 'widgets_init', 'shv_spielplan_load_widget' );
 
// Creating the widget 
class shv_spielplan_widget extends WP_Widget {
 
function __construct() {
parent::__construct(
 
// Base ID of your widget
'shv_spielplan_widget', 
 
// Widget name will appear in UI
__('shv Spielplan Widget', 'shv_spielplan_widget_domain'), 
 
// Widget description
array( 'description' => __( 'SHV-Spielplan', 'shv_spielplan_widget_domain' ), ) 
);
}

// Creating widget front-end
 
public function widget( $args, $instance ) {
$spiele = $instance[ 'spiele' ];
$atts = array(
    "spieltage" => $spiele,
    "liga" => 1,
    "resultat" => 0,
    "teamid" => "alle"
);
$dbwhere = "Spiele";
$result = getdb_data($dbwhere);
$output = create_spiele_list($atts, $result);
$title = apply_filters( 'widget_title', $instance['title'] );
 
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
 
// This is where you run the code and display the output
echo __( $output, 'shv_spielplan_widget_domain' );
echo $args['after_widget'];
}
         
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'shv_spielplan_widget_domain' );
}
if ( isset( $instance[ 'spiele' ] ) ) {
$spiele = $instance[ 'spiele' ];
}
else {
$spiele = __( 5, 'shv_spielplan_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id( 'spiele' ); ?>"><?php _e( 'Anzahl Spieltage:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'spiele' ); ?>" name="<?php echo $this->get_field_name( 'spiele' ); ?>" type="number" value="<?php echo esc_attr( $spiele ); ?>" />
</p>
<?php 
}
     
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
$instance['spiele'] = ( ! empty( $new_instance['spiele'] ) ) ? strip_tags( $new_instance['spiele'] ) : '';
return $instance;
}
} // Class shv_spielplan_widget ends here