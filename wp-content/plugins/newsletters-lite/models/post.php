<?php

if (!class_exists('wpmlPost')) {
class wpmlPost extends wpmlDbHelper
{
	var $model = 'Post';
	var $controller = "posts";
	var $table_name = 'wpmlposts';
	
	var $table_fields = array(
		'id'			=>	"INT(11) NOT NULL AUTO_INCREMENT",
		'post_id'		=>	"INT(11) NOT NULL DEFAULT '0'",
		'sent'			=>	"ENUM('Y','N') NOT NULL DEFAULT 'N'",
		'created'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'modified'		=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
		'key'			=>	"PRIMARY KEY (`id`), INDEX(`post_id`), INDEX(`sent`)"
	);
	
	var $tv_fields = array(
		'id'			=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
		'post_id'		=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
		'sent'			=>	array("ENUM('Y','N')", "NOT NULL DEFAULT 'N'"),
		'created'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'modified'		=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
		'key'			=>	"PRIMARY KEY (`id`), INDEX(`post_id`), INDEX(`sent`)"					   
	);
	
	var $indexes = array('post_id', 'sent');
	
	function __construct($data = array())
    {
		parent::__construct();
		
		$this -> table = $this -> pre . $this -> controller;
	
		if (!empty($data)) {
			foreach ($data as $key => $val) {
				$this -> {$key} = stripslashes_deep($val);
			}
		}
		
		return true;
	}
	
	function get_by_post_id($postid = null)
    {
		global $wpdb;
	
		if (!empty($postid)) {
			$query = "SELECT * FROM `" . $wpdb -> prefix . "" . $this -> table_name . "` WHERE `post_id` = '" . esc_sql($postid) . "' LIMIT 1";
			
			$query_hash = md5($query);
			if ($ob_post = $this -> get_cache($query_hash)) {
				$post = $ob_post;
			} else {
				$post = $wpdb -> get_row($query);
				$this -> set_cache($query_hash, $post);
			}
		
			if (!empty($post)) {
				return true;
			}
		}
		
		return false;
	}
	
	function save($data = array(), $validate = true)
    {
		global $wpdb;
		
		if (!empty($data)) {
			if ($validate == true) {
                    if (empty($data['post_id'])) {
                        $this -> errors[] = __('No post was specified', 'wp-mailinglist');
                    }
                    if (empty($data['sent'])) {
                        $this -> errors[] = __('No sent status was specified', 'wp-mailinglist');
                    }
			}
		
			if (empty($this -> errors)) {
				$nowdate = $this -> gen_date();
			
				if (!empty($data['id'])) {
					$query = "UPDATE `" . $wpdb -> prefix . "" . $this -> table_name . "` SET `post_id` = '" . esc_sql($data['post_id']) . "', `sent` = '" . $data['sent'] . "', `modified` = '" . $nowdate . "'";
				} else {
					$query = "INSERT INTO `" . $wpdb -> prefix . "" . $this -> table_name . "` (`post_id`, `sent`, `created`, `modified`) VALUES ('" . esc_sql($data['post_id']) . "', '" . esc_sql($data['sent']) . "', '" . $nowdate . "', '" . $nowdate . "');";
				}
				
				if ($wpdb -> query($query)) {
					$this -> insertid = (empty($data['id'])) ? $wpdb -> insert_id : $data['id'];
					return true;
				}
			}
		}
		
		return false;
	}
}
}

?>