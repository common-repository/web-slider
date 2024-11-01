<?php
if (isset($_POST['submit_file'])) 
{
  global $wpdb;
  $table_name=$wpdb->prefix.'slider';
  $base =  dirname(__FILE__) . '/upload/';
  if($_POST['radiobtn1'] == 'Video')
  {
    if (!function_exists('wp_handle_upload'))
     {
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
    }
    $files =sanitize_mime_type($_FILES['vfile']['name']);
  }
  else
  {
    if (!function_exists('wp_handle_upload'))
     {
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
      require_once( ABSPATH . 'wp-admin/includes/image.php' );
      require_once( ABSPATH . 'wp-admin/includes/media.php' );
     }
    $files = sanitize_mime_type($_FILES['file']['name']);
  }
  for($i = 0; $i < count($files); $i++) 
  {
    
    $title = sanitize_text_field($_POST['img_title'][$i]);
    $link= sanitize_url($_POST['img_link'][$i]);
    $content = sanitize_text_field($_POST['img_content'][$i]);
    if($_POST['radiobtn1'] == 'Video')
    {
      if (!function_exists('wp_handle_upload'))
      {
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
      }
      $slide = sanitize_mime_type($_FILES['vfile']['name'][$i]);
      $allowed = array('mpg', 'mp2', 'mpeg','mpe', 'ogg', 'mp4','m4p', 'm4v');
      $extension = pathinfo($slide, PATHINFO_EXTENSION);   
      $flag = 0; 
      if (($slide != ""  && (in_array($extension,$allowed)))) 
      {
      if (!function_exists('wp_handle_upload'))
      {
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
      } 
        $flag = 1;  
        $target = $base . basename($slide);
        move_uploaded_file($_FILES['vfile']['tmp_name'][$i], "$target");
        chmod($target, 0755);
      }
      else 
      {
        $msg ='<div class="alert alert-success mt-5" id="deldata" role="alert">Please upload file having extensions .mpg/.mp2/.mpeg/.mpe/.ogg/.mp4/.m4p/.m4v only.</div>';
      }
    }
    else
    {
      if (!function_exists('wp_handle_upload'))
      {
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
      } 
      $slide = sanitize_mime_type($_FILES['file']['name'][$i]);
      $allowed = array('gif', 'png', 'jpg');
      $extension = pathinfo($slide, PATHINFO_EXTENSION);   
      $flag = 0;
      if (($slide != "") && (in_array($extension,$allowed)))
      {
      if (!function_exists('wp_handle_upload'))
      {
      require_once(ABSPATH . 'wp-admin/includes/file.php');
      require_once(ABSPATH . 'wp-admin/includes/image.php');
      require_once(ABSPATH . 'wp-admin/includes/media.php');
      } 
        $flag = 1;
        $target = $base . basename($slide);
        move_uploaded_file( $_FILES['file']['tmp_name'][$i], "$target");
        chmod($target, 0755);
      }
      else
      {
        $msg =wp_kses_post('<div class="alert alert-success mt-5" id="deldata" role="alert">Please upload file having extensions .jpeg/.jpg/.png/.gif only.</div>');
      }
    }
    if($flag)
    {
       $wpdb->insert($table_name, array(
      'file' => $slide,
      'title' => $title,
      'content' => $content,
      'link' => $link
    ));
    $lastid = $wpdb->insert_id;
    if ($lastid) 
    {
      $msg = wp_kses_post('<div class="alert alert-success mt-5" id="removedata" role="alert">Success! Record added successfully!</div>');
    }
     else 
    {
      $msg = wp_kses_post('<div class="alert alert-danger mt-5" id="deldata" role="alert">Something wents wrong!</div>');
    }
   
    $wpdb->query($wpdb->prepare("UPDATE $table_name SET image_order=$lastid WHERE id= %d",$lastid));
  }
  }
   echo wp_kses_post($msg);
 }
 ?>
  <div id="wrapper">
    <div id='menu_div' class="row">
      <?php
      global $wpdb;
      $table=$wpdb->prefix.'slider';
      $base =  plugin_dir_url(__FILE__) . '/upload/';
      $result = $wpdb->get_results('SELECT * FROM '.$table.' ORDER BY `image_order` ASC');?>
      <div class="container tech-slideshow-outer-wrapper">
        <h2>Web Slider</h2>
        <div class="alert alert-warning" style="color: red;">
          <strong>This slider supports 1920x500 image/video.</strong></br>
          <strong>To change the position of image/video, just drag that record and drop to the required position.</strong> </br> 
          <strong> You can drop the short code [Webslider] as per your needs.</strong></div>
        <input type="button" id="btn" class="add_btn" value="Add"> 
        <form method="post" id="file_form" enctype="multipart/form-data" class="myform">
          <div id="file_div">
            <div class="imgbanner">
              <br><input type="submit" name="submit_file" value="SUBMIT" id="submit_file" class="btn_submit"
                style="display: none;">
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
              <ul id="image-list" class="ui-sortable">
                <?php foreach ($result as $item) { 
                ?>
                <li id="image_<?php echo intval($item->id); ?>">
                  <?php 
                 $allowed = array('gif', 'png', 'jpg');
                 $extension = pathinfo($base.$item->file, PATHINFO_EXTENSION);   
                  if(!in_array($extension,$allowed))
                  {?>
                  <video autoplay="autoplay"  controls muted>
                    <source src="<?php  echo esc_url($base . $item->file) ?>" id="preview-vid">
                    Your browser does not support HTML5 video.
                  </video>
                  <?php 
                  }
                   else 
                   {
                    ?>
                  <img src="<?php echo esc_url($base.$item->file) ?>" width="100px" height="100px">
                  <div class="input_list">
                    <input type="hidden" name="old_file[]" value="<?php echo  esc_url($item->file); ?>">
                    <input type="text" name="title[]" value="<?php echo wp_kses_post($item->title); ?>">
                    <input type="text" name="content[]" value="<?php echo wp_kses_post($item->content); ?>">
                    <input type="text" name="link[]" value="<?php echo esc_url($item->link);?>">
                   </div>
                  <?php  } ?>
                  <input type="button" value="REMOVE" name="remove" class="del_btn video_btn" rid="<?php echo intval($item->id); ?>"
                    onclick="return remove_file();">
                </li>
                <?php } ?>
              </ul>
              <input type='button' class="btn-submit" value='Submit' id='submit' style="display:none ;">
            </div>
          </div>
        </form>
      </div>
      </div>
      <!-- Add Footer--->
     <?php function  webslider_script_footer()
     { ?>
     <script type="text/javascript">
        var x = 0;
       
        jQuery('#btn').on('click', function() {
          x++;
          if (jQuery('#submit_file').css('display') == 'none') 
          {
            jQuery('#submit_file').css('display', 'block');
          }
          jQuery("#file_div").prepend(

            "<div class='img_sec'><div class='imgsection'><input type='radio' onclick='return media_type1("+ x +")' name='radiobtn" + x +
            "' id='img_radiobtn" + x + "' checked='checked' value='Image'> <label for='Image'>Image</label><input type='radio' onclick='return media_type2("+ x +")' name='radiobtn" +x+
            "' id='video_radiobtn" + x + "'  value='Video' ><label for='video'>Video</label><br/> <div class='videocontent' style='display:none'> <input type='file'  onchange='return vfileValidation()'  id='vfile' name='vfile[]' accept='.MPG, .MP2, .MPEG, .MPE, .MPV,.OGG ,.MP4, .M4P, .M4V '><div class='videovalidation'></div></div> <div class='imgcontent'><div class='chooseimg'> <input type='file' id='file' name='file[]' onchange='return fileValidation()'  accept='.jpg,.jpeg,.png'><div class='validation'></div></div></br></br></br><input type='text'  name='img_title[]' placeholder='Enter Title'><br><input type='text'  name='img_content[]'  placeholder='Enter Content'><br><input type='text' name='img_link[]'  placeholder='Enter Link'></div><input type='button' value='REMOVE'  onclick='remove_file(this);'></div></div>"
          );
        });
        jQuery('.imgsection input:radio').click(function ()
           {
            if (jQuery(this).val() === 'Image') {

              jQuery(this).parent('.imgsection').find('.imgcontent').css('display', 'flex');
              jQuery(this).parent('.imgsection').find('.videocontent').css('display', 'none');
              jQuery('#inp-img-id').attr('required','true');
              jQuery('#inp-img-vid').removeAttr('required');
            }
             else if (jQuery(this).val() === 'Video')
             {
              jQuery(this).parent('.imgsection').find('.imgcontent').css('display', 'none');
              jQuery(this).parent('.imgsection').find('.videocontent').css('display', 'flex');
              jQuery('#inp-img-id').removeAttr('required');
              jQuery('#inp-img-vid').attr('required','true');
            }
          });
          function media_type1(id) 
          {
            
            jQuery('#img_radiobtn'+id).parent('.imgsection').find('.imgcontent').css('display', 'flex');
            jQuery('#img_radiobtn'+id).parent('.imgsection').find('.videocontent').css('display', 'none');
            jQuery('#inp-img-id').attr('required','true');
            jQuery('#inp-img-vid').removeAttr('required'); 
          }
          function media_type2(id) 
          {
           
            jQuery('#video_radiobtn'+id).parent('.imgsection').find('.imgcontent').css('display', 'none');
            jQuery('#video_radiobtn'+id).parent('.imgsection').find('.videocontent').css('display', 'flex');
            jQuery('#inp-img-id').removeAttr('required');
            jQuery('#inp-img-vid').attr('required','true');
          }
        function remove_file(ele) {
          var len = jQuery('.imgsection').length;
          if (len == 1) 
          {
            jQuery('#submit_file').css('display', 'none');
          }
          jQuery(ele).parent().remove();
        }

       
        jQuery(document).ready(function() {  
          let adminAjaxpath = '<?php echo admin_url(); ?>/admin-ajax.php';
          jQuery(".del_btn").click(function () {
            var rid = jQuery(this).attr('rid'); 
            var x = confirm("Are you sure to remove this record?");
            if (x) { 
              jQuery.ajax({
              type: "POST",
              url: adminAjaxpath,
              data: {
                id: rid,
                action: 'deleteImage'
              },
              success: function (msg) {
                jQuery('#image_'+rid).css('display', 'none');
                window.scrollTo({
                                  top: 0,
                                  left: 100,
                                  behavior: 'smooth'
                              });
                jQuery('#image-list').prepend('<div class="alert alert-success mt-5"id="removedata" role="alert">Success! Section deleted successfully!</div>');
                jQuery("#removedata").delay(4000).fadeOut("fast");
              },
              error: function () {
                alert("failure");
              }
            });
          }
          });
          var dropIndex;
          jQuery("#image-list").sortable({
            update: function (event, ui) {
              dropIndex = ui.item.index();
              jQuery("#submit").trigger("click");
            } 
          });
          jQuery('#submit').click(function (e) {   
            var imageIdsArray = [];
            var difference = [];
            var allimageIdsArray = [];
            jQuery('#image-list li').each(function (index) 
            {
              var id = jQuery(this).attr('id');
              var split_id = id.split("_");
              allimageIdsArray.push(split_id[1]);
            });
            jQuery('#image-list li').each(function (index) 
            {
              if (index <= dropIndex) {
                var id = jQuery(this).attr('id');
                var split_id = id.split("_");
                imageIdsArray.push(split_id[1]);
              }
            });
            difference = jQuery(allimageIdsArray).not(imageIdsArray).get();
            let adminAjaxpath = '<?php echo admin_url(); ?>/admin-ajax.php';
            jQuery.ajax({
              url: adminAjaxpath,
              type: 'post',
              data: {
                difference: difference,
                imageIds: imageIdsArray,
                action: 'webslider_sortimage'
              },
              success: function (response) {   
                window.scrollTo({
                                  top: 0,
                                  left: 100,
                                  behavior: 'smooth'
                              });
                  if(response)
                  {   
                  jQuery('#image-list').prepend('<div class="alert alert-success mt-5"id="removedata" role="alert">Success! Position changed successfully!</div>');
                  jQuery("#removedata").delay(4000).fadeOut("fast");
                  location.reload();
                }
                else
                {
                 jQuery('#image-list').prepend('<div class="alert alert-success mt-5"id="deldata" role="alert">Something wents wrong!</div>');
                  jQuery("#deldata").delay(6000).fadeOut("fast");
                  location.reload();
                }
              },
            });
            e.preventDefault();
          });
          jQuery("#img_sec").validate({
                  rules: {
                      'title_new': {
                          required: true,
                      }
                  },
                  messages: {
                      'title_new': "<br>Please enter a title.",
                  }
              });
          jQuery("#deldata").delay(4000).fadeOut("fast");
        });
        /* image validation */   
		function fileValidation() 
    {
			var fileInput =
				document.getElementById('file');
			var filePath = fileInput.value;
      var allowedExtensions =
                  /(\.jpg|\.jpeg|\.png|\.gif)$/i;
			if (!allowedExtensions.exec(filePath)) {
        jQuery(".validation").html("Invalid file type").addClass("error-msg").attr("style", "color: red;");
				fileInput.value = '';
				return false;
			}
		}
    /* video validation */
    function vfileValidation() 
    {
			var fileInput =
				document.getElementById('vfile');
			var filePath = fileInput.value;
      var allowedExtensions =
                    /(\.MPG|\.MP2|\.MPEG|\.MPE|\.MPV|\.OGG|\.MP4|\.M4P|\.M4V)$/i;
			if (!allowedExtensions.exec(filePath)) {
        jQuery(".videovalidation").html("Invalid file type").addClass("error-msg").attr("style", "color: red;");
				fileInput.value = '';
				return false;
			}
		}
  </script>
<?php } 
add_action('admin_footer', 'webslider_script_footer'); ?>