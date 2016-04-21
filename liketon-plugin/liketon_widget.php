<?php
class Liketon_Widget extends WP_Widget 
{
  function __construct()
  {
    parent::__construct(
      'liketon_widget', // Base ID
      __( 'Liketon the Like Button', 'lt_domain' ), // Name
      array( 'description' => __( 'Shows liked post titles', 'lt_domain' ), ) // Args
    );
  }
  function form($instance)
  {
    $lines = $instance[ 'lines' ];
     
    // markup for form ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'lines' ); ?>">Number of likes the widget shows:</label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'lines' ); ?>" name="<?php echo $this->get_field_name( 'lines' ); ?>" placeholder="Default number is 10" value="<?php echo esc_attr( $lines ); ?>">
    </p>
             
<?php
  }

  function update( $new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance[ 'lines' ] = strip_tags( $new_instance[ 'lines' ] );
    return $instance;
  }
  function cmp($a,$b) //sorts according to primarily most recent commented post, secondary most recent liked pots
  {
    global $wpdb;
    $commentT = $wpdb->prefix.'comments';
    $most_recent_a = $wpdb->get_row("
                  SELECT comment_date
                  FROM $commentT
                  WHERE comment_post_ID=$a->post_id AND comment_approved=1
                  ORDER BY comment_date DESC
                  LIMIT 1");
    $most_recent_b = $wpdb->get_row("
                  SELECT comment_date
                  FROM $commentT
                  WHERE comment_post_ID=$b->post_id AND comment_approved=1
                  ORDER BY comment_date DESC
                  LIMIT 1");

    if(null == $most_recent_a && null== $most_recent_b)
      return $a->like_date < $b->like_date;
    else if(null == $most_recent_a)
      return true;
    else if(null == $most_recent_b)
      return false;
    return $most_recent_a->comment_date < $most_recent_b->comment_date; // < order by most recent first
  }
  function widget($args, $instance)
  {
    echo '<b>Liked Posts\' Titles</b></br>';
    global $wpdb;
    $user_id = get_current_user_id();
    $tableName = $wpdb->prefix.'likebutton';
    $posts = $wpdb->get_results("
                SELECT post_id,like_date
                FROM $tableName
                WHERE user_id = $user_id
      ");

    usort($posts,array($this,'cmp'));

    $lines =10;
    if(!empty($instance[ 'lines' ]) && ($instance['lines']>=1))
    $lines = $instance['lines']; 
    $count=0;
    foreach ($posts as $likedpost)
    {
      $count+=1;
      echo $count.': <a href="'.get_permalink($likedpost->post_id).'">'.get_the_title($likedpost->post_id).'</a>';
      echo'</br>';
       if($count==$lines)
        break;
    }
  }
 
}
?>
