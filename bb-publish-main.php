<?php
/*
Plugin Name: BloggersBase Publisher
Plugin URI: http://www.bloggersbase.com/wordpress/
Description: Allows you to submit your posts automatically to BloggersBase
Version: 1.1.1
Author: Alex Dvorkin
Author URI: http://www.bloggersbase.com/
*/

require("bb-publish-options.php");
require("bb-publish-utilities.php");
require("bb-publish-activation.php");

global $bb_posts_table_name;
$bb_posts_table_name = $wpdb->prefix."published_on_bb";
 
 
add_action('admin_menu','bb_publish_outer_box');
add_action('publish_post', 'bb_publish_do_publish');
add_filter('the_content', 'bb_publish_display_rating');

register_activation_hook(__FILE__, 'bb_publish_install');

check_message('xBBPBErrorMessage', 'error');
check_message('xBBPBUpdateMessage', 'updated');

?>