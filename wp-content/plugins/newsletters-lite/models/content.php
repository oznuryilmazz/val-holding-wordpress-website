<?php

if (!class_exists('wpmlContent')) {
    class wpmlContent extends wpmlDbHelper
    {
        var $model = 'Content';
        var $controller = 'contents';
        var $errors = array();

        var $tv_fields = array(
            'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
            'number'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
            'history_id'			=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
            'content'				=>	array("TEXT", "NOT NULL"),
            'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'key'					=>	"PRIMARY KEY (`id`), INDEX(`history_id`)"
        );

        var $indexes = array('history_id');

        function __construct($data = null)
        {
            parent::__construct();

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
            return;
        }

        function defaults()
        {
            global $Html;

            $defaults = array(
                'created'			=>	$Html -> gen_date(),
                'modified'			=>	$Html -> gen_date(),
            );

            $defaults = apply_filters('newsletters_content_defaults', $defaults);
            return $defaults;
        }

        function validate($data = array())
        {
            global $Html;
            $this -> errors = array();
            $defaults = isset($defaults) ? $defaults : $this->defaults();

            $data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
            $r = wp_parse_args($data, $defaults);
            extract($r, EXTR_SKIP);

            if (!empty($data)) {
                if (empty($number)) {
                    $this -> errors['number'] = __('Please specify a number', 'wp-mailinglist');
                }
                if (empty($history_id)) {
                    $this -> errors['history_id'] = __('Please specify a history email', 'wp-mailinglist');
                }
                if (empty($content)) {
                    $this -> errors['content'] = __('Please specify content', 'wp-mailinglist');
                }
            } else {
                $this -> errors[] = __('No data was provided', 'wp-mailinglist');
            }

            if (empty($this -> errors)) {
                if ($contentarea = $this -> find(array('number' => $number, 'history_id' => $history_id), null, null, false)) {
                    $this -> data -> id = $contentarea -> id;
                }
            }

            $this -> errors = apply_filters('newsletters_content_validation', $this -> errors, $data);
            return $this -> errors;
        }
    }
}

?>