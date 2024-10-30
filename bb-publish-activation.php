<?php

function bb_publish_install ()
{
  global $wpdb;
  global $bb_posts_table_name;
  
  $bb_posts_table_name = $wpdb->prefix."published_on_bb";
   
  if($wpdb->get_var("show tables like '$table_name'") != $table_name)
  {
    $sql = "CREATE TABLE $table_name (
		    wordpress_post_id bigint NOT NULL ,
			bb_post_id varchar(30) NOT NULL ,
			PRIMARY KEY (wordpress_post_id));";

    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}

?>