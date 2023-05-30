<?php

if (!class_exists('wpmlGroup')) {
	class wpmlGroup extends wpmlDbHelper
    {
		var $model = 'Group';
		var $controller = 'groups';
		var $errors = array();
		var $table;

		var $fields = array(
			'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
			'title'				=>	"VARCHAR(255) NOT NULL DEFAULT ''",
			'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
			'key'				=>	"PRIMARY KEY(`id`)",
		);

		var $tv_fields = array(
			'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
			'title'				=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
			'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
			'key'				=>	"PRIMARY KEY(`id`)",
		);

		function __construct($data = array())
        {
			parent::__construct();

			global $Db;

			$this -> table = $this -> pre . $this -> controller;

			foreach ($this -> tv_fields as $field => $attributes) {
				if (is_array($attributes)) {
					$this -> fields[$field] = implode(" ", $attributes);
				} else {
					$this -> fields[$field] = $attributes;
				}
			}

			if (!empty($data)) {
				foreach ($data as $dkey => $dval) {
					$this -> {$dkey} = stripslashes_deep($dval);
				}
			}

			$Db -> model = $this -> model;
			return true;
		}

		function defaults()
        {
			global $Html;

			$defaults = array(
				'created'			=>	$Html -> gen_date(),
				'modified'			=>	$Html -> gen_date(),
			);

			return $defaults;
		}

		function validate($data = array())
        {
			$this -> errors = array();
            $defaults = $this -> defaults();
			$data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
			$r = wp_parse_args($data, $defaults);
			extract($r, EXTR_SKIP);

			if (!empty($data)) {
                if (empty($title)) {
                    $this -> errors['title'] = __('Please fill in a title for this group.', 'wp-mailinglist');
                }
			} else {
				$this -> errors[] = __('No data was posted', 'wp-mailinglist');
			}

			return $this -> errors;
		}

		function select()
        {
			global $wpdb, $Html;
	        $query = "SELECT `id`, `title` FROM `" . $wpdb -> prefix . "" . $this -> table . "` ORDER BY `title` ASC";

	        $query_hash = md5($query);
	        if ($ob_groups = $this -> get_cache($query_hash)) {
		        return $ob_groups;
	        }

			if ($groups = $wpdb -> get_results($query)) {
				if (!empty($groups)) {
					$groupsselect = array();

					foreach ($groups as $group) {
						$groupsselect[$group -> id] = esc_html($group -> title);
					}

					$this -> set_cache($query_hash, $groupsselect);
					return $groupsselect;
				}
			}

			return false;
		}

		function save($data = array(), $validate = true)
        {
			if ($this -> language_do()) {
				$data['Group']['title'] = $this -> language_join($data['Group']['title']);
			}

			return parent::save($data);
		}
	}
}

?>