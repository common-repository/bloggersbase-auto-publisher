<?php

function bb_publish_build_options()
{
  if (isset($_POST["xBBPBPToken"])) update_option("xBBPBPToken", $_POST["xBBPBPToken"]);
  if (isset($_POST["xBBPBNickname"])) update_option("xBBPBNickname", $_POST["xBBPBNickname"]);
  if (isset($_POST["xBBPBBlogName"])) update_option("xBBPBBlogName", $_POST["xBBPBBlogName"]);
  
  if (isset($_POST["xBBPBShowRating"]))
	update_option("xBBPBShowRating", $_POST["xBBPBShowRating"]);
  else
  {
	if (isset($_POST["xBBPBPToken"])) update_option("xBBPBShowRating", "");
  }

  $bb_publisher_token = get_option('xBBPBPToken');
  $bb_nickname = get_option('xBBPBNickname');
  $bb_publish_blog = get_option('xBBPBBlogName');
  $bb_publish_show_rating = get_option('xBBPBShowRating');
  
  echo "<form method='post' action=''>";
  echo "<h2>Configuration of BloggersBase Publisher</h2>";
  echo "<div style='border: 1px dotted; padding: 10px; width:500px;'>";
  echo "<b>This information is needed to identify you when publishing a post</b><br /><br />";
  echo "BloggersBase Nickname: <input type='text' size='15' name='xBBPBNickname' value='$bb_nickname' /><br />";
  echo "Publisher Token: <input type='text' size='5' name='xBBPBPToken' value='$bb_publisher_token' />";
  echo "&nbsp;<a href='http://bloggersbase.com/wordpress/' target='_blank'>what is it?</a><br />";
  echo "</div><br />";
  
  echo "By default, publish posts on BloggersBase in the ";

  $scope = explode("|", xBBPBGetPage("http://www.bloggersbase.com/api/get_all_blogs.ashx"));			

  echo "<select name='xBBPBBlogName'>";
  
  foreach ($scope as $key => $value)
  {
	echo "<option value='$value'".($value == $bb_publish_blog ? " selected='selected'" : "").">$value </option>";
  }

  echo "</select>blog.<br /><br />";
  echo "<input type='checkbox' name='xBBPBShowRating'".($bb_publish_show_rating == "" ? "" : " checked='checked'")."'/>";
  echo "Show ratings from BloggersBase for your posts<br /><br />";
  echo "<input type='submit' name='submit' value='Save Settings' class='button-primary' /></form>";
}

?>