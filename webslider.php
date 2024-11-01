<?php
/*
  Plugin Name: Web Slider
  Description: Web Slider is an ultimate responsive image slider in WordPress, where the user can easily add number of multiple images, videos
  Author: Techforce
  Author URI: https://techforceglobal.com
  Version: 1.0
  Text Domain: Web Slider
  License: GNU General Public License v2 or later
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /languages
*/

/**
 * Enhance your blog or website with and Web Slider, WordPress's most easy plugins.
 * Simply drag and drop images, photos, videos, and other media from your WordPress Media
 * Library into place, then customise the slide captions, links, and SEO sections all from
 * one page.
 *
 * Web Slider is a WordPress image, photo, and video plugin that allows you to create a
 * beautiful slider, slideshow, carousel, or gallery using the most basic and intuitive plugin
 * interface of any custom image, photo, and video plugin.
 *
 * The user can show off some latest work, photos, and videos, or even products from online store.
 * People will have no trouble discovering your site and the slider, slide show, gallery, or carousel you make.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
function webslider_jquery_scripts() 
{
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-core');
  wp_enqueue_script('jquery-ui-sortable');
}    
add_action('admin_init', 'webslider_jquery_scripts');
function webslider_menu()
{
  add_menu_page("Web Slider", "Web Slider", "manage_options", "webslider", "webslider_include",plugins_url('/WebSlider/upload/img.jpg'));
}
if(!defined('WEB_SLIDER_URL')) 
{
  define('WEB_SLIDER_URL', plugin_dir_url( __FILE__ ));
}
add_action("admin_menu", "webslider_menu");
function webslider_enqueue_scripts()
{
  wp_enqueue_style('admin_css',WEB_SLIDER_URL.'css/admin-style.css', array(),'1.0','all');
  wp_enqueue_script('validate-js',WEB_SLIDER_URL.'/js/jquery.validate.min.js',array('jquery'));
}
function webslider_frontenqueue_scripts()
{ 
  wp_enqueue_style('bootstrap',WEB_SLIDER_URL.'css/bootstrap.min.css', array(),'1.0','all');
  wp_enqueue_script('bootstrap',WEB_SLIDER_URL.'/js/bootstrap.min.js',array('jquery'));
  wp_enqueue_style('style_css', WEB_SLIDER_URL.'css/style.css', array(),'1.0','all');
  wp_enqueue_style('animate_css', WEB_SLIDER_URL.'css/animate.min.css', array(),'1.0','all');
}
add_action('admin_enqueue_scripts', 'webslider_enqueue_scripts');
add_action('wp_enqueue_scripts', 'webslider_frontenqueue_scripts');
/* include file of image upload */
function webslider_include()
{
  include "img_upload.php";
} 
/*delete image functionlity*/
function webslider_deleteImage()
{
  global $wpdb; 
  if (isset($_POST['id'])) 
  {
    $imgid =sanitize_text_field($_POST['id']);
    $table_name=$wpdb->prefix.'slider'; 
    $row_id= $wpdb->get_results('SELECT * FROM '.$table_name.' where id="'.$imgid.'"');
    $file= $row_id[0]->file;
    unlink(dirname(__FILE__).'/upload/'.$file);
    $removefromdb= $wpdb->delete( $table_name, array('id' => $imgid));  
    return $removefromdb;
  }
}
add_action( "wp_ajax_deleteImage", "webslider_deleteImage" );
add_action( "wp_ajax_nopriv_deleteImage", "webslider_deleteImage" );
/*image and video drag and drop functionality */
function webslider_sortimage()
{
  global $wpdb;    
  if (isset($_POST['imageIds'])) 
  {
   
    $difference = rest_sanitize_array($_POST['difference']);
     $imageIdsArray = rest_sanitize_array($_POST['imageIds']);
     $finalarray=array_merge($imageIdsArray,$difference);
      $table=$wpdb->prefix.'slider';
      $count = 1; 
      foreach ($finalarray as $id) 
      {
         $imageOrder = $count;
        $imageId = $id; 
        $result = $wpdb->query($wpdb->prepare("UPDATE $table SET image_order=$imageOrder WHERE id=$imageId")); 
        if($result > 0)
        {
          $update = 1;
        }
          else
          {
            $update = 0;
          }
        $count ++;
      }
    }
    echo wp_kses_post($update);  
}
add_action( "wp_ajax_webslider_sortimage", "webslider_sortimage" );
add_action( "wp_ajax_nopriv_webslider_sortimage", "webslider_sortimage" );

?>

<?php  function webslider_shortcode() {
    {
      global $wpdb;
      $table_name=$wpdb->prefix.'slider';
      $base = plugin_dir_url(__FILE__).'upload/';
      $result = $wpdb->get_results('SELECT * FROM '.$table_name.' ORDER BY `image_order` ASC');
      ?>
      <div id="techslideshow-wrapper" class="techslideshow-wrapper">
      <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <?php 
      $i=0;
      foreach($result as $item)
      {
      $class=''; 
      if($i==0)
      {
        $class='active';
      }
      ?>
       <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo  wp_kses_post($i); ?>" class="<?php echo wp_kses_post($class); ?>" aria-current="true" aria-label="Slide 1"></button>
       <?php $i++;
      }
    ?>
      </div>
    <div class="carousel-inner">
    <?php
    $j=0;
    foreach ($result as $item) 
    {
          $class=''; 
          if($j==0)
          {
            $class='active';
          } 
        ?> 
        <div class="carousel-item <?php echo wp_kses_post($class); ?>">
          <?php $allowed = array('gif', 'png', 'jpg');
          $extension = pathinfo($base.$item->file, PATHINFO_EXTENSION);   
           if(!in_array($extension,$allowed))
           {?>
           <video loop="true" autoplay="autoplay" controls muted >
            <source src="<?php echo esc_url($base.$item->file); ?>" id="preview-vid">
            Your browser does not support HTML5 video.
           </video>
           <?php 
            }
            else 
            {
            ?> 
            <div class="fill" style="background-image:url(<?php echo esc_url($base.$item->file)?>)"></div>
            <?php } ?>
             <div class="carousel-caption">
          <h2 class="animated fadeInLeft"><?php echo wp_kses_post($item->title); ?></h2>
           <p class="animated fadeInUp"><?php echo wp_kses_post($item->content); ?></p>
           <?php if(!empty($item->link))
            {?>
            <p class="animated fadeInUp"><a href="<?php echo esc_url($item->link) ?>" class="btn btn-transparent btn-rounded btn-large">Learn More</a></p>
            <?php }
            else
            {
            ?>
            <p class="animated fadeInUp"><a href="<?php echo esc_url($item->link) ?>" style="display: none;"
            class="btn btn-transparent btn-rounded btn-large">Learn More</a></p>
            <?php } ?>
            </div>
            </div>
            <?php $j++; 
           }
          ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
</div>

<?php 
 } 
}
add_shortcode('Webslider', 'webslider_shortcode');
register_activation_hook(__FILE__, 'webslider_activation');
// callback function to create table
function webslider_activation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'slider';
    if( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name )
     {
      $sql = "CREATE TABLE `$table_name` (";
      $sql .= "`id` int NOT NULL,";
      $sql .= "`file` varchar(225) NOT NULL,";
      $sql .= "`title` varchar(225) NOT NULL,";
      $sql .= "`content` varchar(225) NOT NULL,";
      $sql .= "`link` varchar(225) NOT NULL,";
      $sql .= "`image_order` int NOT NULL";
      $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' ); 
    // Create Table
    dbDelta($sql);
    $sql = "ALTER TABLE $table_name ADD PRIMARY KEY (`id`)";  
    $wpdb->query($sql);
    $sql = "ALTER TABLE $table_name MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1";
    $wpdb->query($sql);
    $data_inserted = array( array(
    'id'   => '1', 
    'file'  => 'banner-1.jpg', 
    'title'=> 'Banner 1',
    'content'   => 'Banner 1 Description',
    'link'   => 'https://www.google.com/',
    'image_order'=> '1' 
    ),
    
  array(
        'id'   => '2', 
        'file'  => 'banner-2.jpg', 
        'title'=> 'Banner 2',
        'content'   => 'Banner 2 Description',
        'link'   => 'https://www.google.com/',
        'image_order'=> '2' 
    ),
  array(
        'id'   => '3', 
        'file'  => 'banner-3.jpg', 
        'title'=> 'Banner 3',
        'content'   => 'Banner 3 Description',
        'link'   => 'https://www.google.com/',
        'image_order'=> '3'
    ),
 array(
      'id'   => '4', 
      'file'  => 'video-1.mp4', 
      'title'=> '',
      'content'   => '',
      'link'   => '',
      'image_order'=> '4' 
  ),
  array(
    'id'   => '5', 
    'file'  => 'video-2.mp4', 
    'title'=> '',
    'content'   => '',
    'link'   => '',
    'image_order'=> '5'
  ));

    $values = $place_holders = array();
    if(count($data_inserted) > 0) {
    foreach($data_inserted as $data)
     {
        array_push( $values, $data['id'], $data['file'], $data['title'], $data['content'], $data['link'], $data['image_order']);
        $place_holders[] = "( %s, %s, %s, %s, %s, %s)";
    }
    webslider_datainsert($place_holders, $values);
}
}
}
function webslider_datainsert($place_holders, $values) 
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'slider';
  $query = "INSERT INTO $table_name (`id`, `file`, `title`, `content`, `link`, `image_order`) VALUES ";
  $query = implode(', ',$place_holders );
  $sql = $wpdb->prepare("$query ", $values );
  if($wpdb->query($sql)) 
  {
      return true;
  } 
  else 
  {
      return false;
  }
}
