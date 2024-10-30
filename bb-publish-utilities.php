<?php

function xBBPBGetPage($url, $postData = null)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	if(!ini_get('safe_mode')) {
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.9.0.6; .NET CLR 3.0; ffco7) Gecko/2009011913 Firefox/3.0.6");

	if ($postData != null) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_POST, true);
	}

	$response = curl_exec($ch);
	curl_close($ch);
	unset($ch);

	return $response;
}

function check_message($option_name, $div_class)
{
  $bb_message = get_option($option_name);
  if ($bb_message != '')
  {
	add_action('admin_notices', create_function( '', "echo '<div class=\"$div_class\"><p>$bb_message</p></div>';" ) );
	delete_option($option_name);
  }
}

function bb_publish_do_publish($post_ID)
{
  $bb_publisher_token = get_option('xBBPBPToken');
  $bb_nickname = get_option('xBBPBNickname');
  
  if ($_POST['xBBPBPublishThis'] != 'on') return;
  
  $bb_publish_blog = $_POST["xBBPBBlogName"];
  
  if ($bb_publisher_token == '')
  {
	update_option("xBBPBErrorMessage", "Please set your BloggersBase publisher token in the <i>Settings -> BB Publisher</i>");
	return;
  }
  
  if ($bb_nickname == '')
  {
	update_option("xBBPBErrorMessage", "Please set your BloggersBase username in the <i>Settings -> BB Publisher</i>");
	return;
  }
  
  if ($bb_publish_blog == '')
  {
	update_option("xBBPBErrorMessage", "Please set your BloggersBase publish blog in the <i>Settings -> BB Publisher</i>");
	return;
  }
  
  $bb_publish_result = xBBPBGetPage("http://www.bloggersbase.com/api/publish_post.ashx", "version=2&wp_post_id=$post_ID&nickname=$bb_nickname&token=$bb_publisher_token&blogname=".urlencode($bb_publish_blog)."&body=".urlencode(stripslashes($_POST["content"]))."&subject=".urlencode($_POST["post_title"])."&tags=".$_POST['tax_input']['post_tag']."&permalink=".urlencode(get_permalink($post_ID)));
  $result_token = explode('|', $bb_publish_result);
  
  if ($result_token[1] != 3)
  {
	update_option("xBBPBUpdateMessage", $result_token[2]);
	
	if ($result_token[1] == 1)
	{
		global $wpdb;
		global $bb_posts_table_name;
		
		$wpdb->query("INSERT INTO $bb_posts_table_name (wordpress_post_id, bb_post_id)
					  VALUES ($post_ID, '".$result_token[0]."');");
	}
  }
  else
  {
	update_option("xBBPBErrorMessage", $result_token[2]);
  }   
}

function bb_publish_display_rating($content)
{
  echo $content;
	
  if (get_option('xBBPBShowRating') == 'on')
  {
	global $wpdb;
	global $bb_posts_table_name;
		
	$bb_post_id = $wpdb->get_var("SELECT bb_post_id FROM $bb_posts_table_name WHERE wordpress_post_id = ".get_the_ID());
	
	if ($bb_post_id != '')
	{
		$bb_rating_reply = explode('|', xBBPBGetPage("http://www.bloggersbase.com/api/get_wordpress_post_rating.ashx", "post_id=".urlencode($bb_post_id)));
		if ($bb_rating_reply[0] != 0)
		{
			echo "Rated <b>".round($bb_rating_reply[0]/1000, 1)."</b> on BloggersBase <a href='".$bb_rating_reply[2]."#rating_section'>[rate]</a>.";
			
			if ($bb_rating_reply[1] != 0)
			{
				echo " <a href='".$bb_rating_reply[2]."#comments_section'>Read ".$bb_rating_reply[1]." comment".($bb_rating_reply[1] == 1 ? "" : "s")."</a>.";
			}
		}
	}
	
  }
}

function bb_publish_add_box()
{
  echo "<input type='checkbox' name='xBBPBPublishThis' checked='checked'/>";
  echo "Publish this post on BloggersBase in the ";
  
  $bb_publish_blog = get_option('xBBPBBlogName');
  
  $scope = explode("|", xBBPBGetPage("http://www.bloggersbase.com/api/get_all_blogs.ashx"));			

  echo "<select name='xBBPBBlogName'>";
  
  foreach ($scope as $key => $value)
  {
	echo "<option value='$value'".($value == $bb_publish_blog ? " selected='selected'" : "").">$value </option>";
  }

  echo "</select>blog.";
}


function bb_publish_outer_box()
{
  add_meta_box('bb_publish_div','Publish on BloggersBase', 'bb_publish_add_box', 'post', 'side');
  add_options_page('BloggersBase Publisher Options', 'BB Publisher', 8, 'bb-publish-options.php', 'bb_publish_build_options');
}

?>