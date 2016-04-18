<?php
/*
Plugin Name: LikeTon
Plugin URI: www.memyselfandhoney.xyz
Description: A simple plugin that adds like button to wordpress
Version: Alpha
Author: eralpsahin
Author URI: www.memyselfandhoney.xyz
License: GPL2
*/

//Exit if accessed directly
if(!defined('ABSPATH'))
  exit;
//Do I really need this??
//$liketon_options = get_option('liketon_setting'); //empty() means use the default

register_activation_hook( __FILE__, 'liketon_db' );

function liketon_db() 
{
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $tName = $wpdb->prefix.'likebutton';
  $sql = "CREATE TABLE IF NOT EXISTS $tName (
          post_id bigint(20) UNSIGNED NOT NULL,
          user_id bigint(20) UNSIGNED NOT NULL,
          like_date datetime NOT NULL,
          PRIMARY KEY(post_id,user_id)
          )$charset_collate;";
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}

register_deactivation_hook( __FILE__, 'liketon_deact' );

function liketon_deact()
{
  /*echo '<script language="javascript">';
  echo 'alert("LikeTon has been deactivated...")';
  echo '</script>';*/ 
}
register_uninstall_hook( __FILE__, 'liketon_uninstall' );

function liketon_uninstall()
{
  global $wpdb; //required global declaration of WP variable
  $table_name = $wpdb->prefix.'likebutton';
  $sql = "DROP TABLE ". $table_name;
  $wpdb->query($sql);
}
function liketon_likes( $atts )
{ //page numberlari ayni linkin parametrelisini calls queryi parametreye gore 	yaparim shortcode ile yazarim hepsini.
	
	global $wpdb;
 	$limit = get_option('liketon_setting');
    $user_id = get_current_user_id();
    $tableName = $wpdb->prefix.'likebutton';
    $commentT = $wpdb->prefix.'comments';
   	$page = isset($_GET['page']) ? (int) substr($_GET['page'],1,strlen($_GET['page'])) : 1;
   	$start = ($page > 1) ? ($page * $limit) - $limit : 0 ;
   	$posts = $wpdb->get_results("
                SELECT L.post_id, L.like_date, C.comment_date
                FROM $tableName L
                LEFT JOIN (SELECT comment_post_ID, MAX(comment_date) AS comment_date
                	   FROM $commentT
                	   WHERE comment_approved=1
                	   GROUP BY comment_post_ID) C
                ON C.comment_post_ID = L.post_id
                WHERE L.user_id = $user_id
               	ORDER BY C.comment_date DESC
                LIMIT $start ,$limit
      ");


    ob_start();
    echo $start;
    //var_dump($posts);
    echo '</br>';
    //echo count($posts);
    //echo $_SERVER['REQUEST_URI'];
    //echo '<a href="url">link text</a>';
	echo 'Page is this'.$page.'The limit is'.$limit.'</br>';

	foreach ($posts as $likedpost)
    {
      echo ': <a href="'.get_permalink($likedpost->post_id).'">'.get_the_title($likedpost->post_id).'   '.$likedpost->comment_date.'</a>';
      echo '</br>';
    }
	return ob_get_clean();
}

add_shortcode( 'Liketon', 'liketon_likes' );

function liketon_addBtn($content)
{
  if(!is_page() && is_single() && is_user_logged_in())
  {
  	global $liketon_options;
    global $wpdb;
    global $post;
    $uid=get_current_user_id();
    $tableName = $wpdb->prefix.'likebutton';

    $liketon_status = $wpdb->get_row(
                "SELECT *
                 FROM $tableName
                 WHERE post_id=$post->ID AND user_id=$uid");
   
    ob_start();
    echo '<div id="form-msg"></div>';
    echo '<form id="like-form" method="post" action="'.plugins_url().'/liketon-plugin/getter.php">';
    echo '<input type="hidden" id="postid" value='.$post->ID.'>';

    if(null == $liketon_status)
    {
      echo '<input type="submit" id="likebtn" value="Like">';
    }
    else
    {
      echo '<input type="submit" id="likebtn" value="Dislike">';
    }
    if(empty($liketon_options))
    	echo 'Default Liketon options';
    else
    	echo $liketon_options;
    echo get_the_title();
    return $content.ob_get_clean().'</form>';
  }
  return $content;
}
add_action('the_content','liketon_addBtn');

//process the ajax request
function like_post()
{
  if ( isset( $_POST['post_id'] ) && wp_verify_nonce($_POST['liketon_nonce'], 'liketon-nonce') )
   {
    if(liketon_post_liked($_POST['post_id']))//li_mark_post_as_loved($_POST['postid'], $_POST['userid'])) 
    {
      echo 'liked';
    } 
    else 
    {
      echo 'failed';
    }
  }
  die();
}
add_action('wp_ajax_like_it', 'like_post');

function dislike_post()
{
  if ( isset( $_POST['post_id'] ) && wp_verify_nonce($_POST['liketon_nonce'], 'liketon-nonce') )
  {
    global $wpdb;
    if($wpdb->delete($wpdb->prefix.'likebutton',array(
            'post_id' => $_POST['post_id'],
            'user_id' => get_current_user_id())))
    {
      echo 'disliked';
    } 
    else 
    {
      echo 'failed';
    }
  }
  die();

}
add_action('wp_ajax_dislike_it','dislike_post');

function liketon_post_liked($post_id)
{
  global $wpdb;
  $tableName = $wpdb->prefix.'likebutton';
  $wpdb->insert(
    $tableName,array(
      'post_id'        => $post_id,
      'user_id'        => get_current_user_id(),
      'like_date'      => current_time('mysql',0)
      ));
  return true;
}
function liketon_scripts() {
    wp_enqueue_script('liketon-script', plugins_url() . '/liketon-plugin/liketon.js', array( 'jquery' ) ); 
     wp_localize_script( 'liketon-script', 'liketondata', 
       array('ajaxurl' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('liketon-nonce')) );
}
add_action('wp_enqueue_scripts', 'liketon_scripts');
function liketon_register()
{
  if(is_user_logged_in()) 
    register_widget('Liketon_Widget');
}
function liketon_admin_options() 
{
    add_options_page(
        'Liketon Settings',
        'Liketon',
        'manage_options',
        'liketon-plugin',
        'liketon_options_page'
    );
}
function liketon_options_page()
{
	if(!current_user_can('manage_options'))
	{
		wp_die('Permission denied');
	}
	global $liketon_options;
    ob_start();
	?>
	<div class="wrap">
		<h2>Liketon Settings</h2>

		<form method="post" action="options.php">
		<?php 
		settings_fields( 'liketon_settings_group' ); 
		do_settings_fields( 'liketon_settings_group', '' );
		?>
		<label for="liketon_setting">Number of Likes in a Page:</label>
		<input type="text" id="liketon_setting" name="liketon_setting" placeholder="Default is 10" value="<?php echo get_option('liketon_setting'); ?>" />

		<?php
		submit_button();
		?>
		</form>
	</div>
	<?php
    echo ob_get_clean();
}
add_action( 'admin_menu', 'liketon_admin_options' );

function liketon_register_settings()
{
	register_setting('liketon_settings_group','liketon_setting');
	register_setting('liketon_settings_group','liketon_likepage');
}
add_action('admin_init','liketon_register_settings');
require_once (plugin_dir_path(__FILE__) . '/liketon_widget.php');
// registering the widget
add_action('widgets_init','liketon_register');
?>