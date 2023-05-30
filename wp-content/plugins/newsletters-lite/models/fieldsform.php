<?php

if (!class_exists('wpmlFieldsForm')) {
    class wpmlFieldsForm extends wpmlDbHelper
    {
        var $model = 'FieldsForm';
        var $controller = 'fieldsforms';

        var $tv_fields = array(
            'id'					=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
            'form_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
            'field_id'				=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
            'label'					=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
            'caption'				=>	array("TEXT", "NOT NULL"),
            'placeholder'			=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
            'required'				=>	array("INT(1)", "NOT NULL DEFAULT '0'"),
            'errormessage'			=>	array("VARCHAR(255)", "NOT NULL DEFAULT ''"),
            'settings'				=>	array("TEXT", "NOT NULL"),
            'order'					=>	array("INT(11)", "NOT NULL DEFAULT '0'"),
            'created'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'modified'				=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'key'					=>	"PRIMARY KEY (`id`), INDEX(`field_id`), INDEX(`order`)"
        );

        var $indexes = array('form_id', 'field_id', 'order');

        function __construct($data = null)
        {
            parent::__construct();

            global $Db, $Field;

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

                    switch ($dkey) {
                        case 'field_id'						:
                            $Db -> model = $Field -> model;
                            $this -> field = $Db -> find(array('id' => $dval), null, null, false);
                            break;
                    }
                }
            }

            $Db -> model = $this -> model;
            return;
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
            global $Html;
            $this -> errors = array();
            $defaults = $this -> defaults();

            $data = (empty($data[$this -> model])) ? $data : $data[$this -> model];
            $r = wp_parse_args($data, $defaults);
            extract($r, EXTR_SKIP);

            if (!empty($data)) {
                // Check for empty or invalid values
                if (empty($field_id)) {
                    $this -> errors['field_id'] = __('No field was specified', 'wp-mailinglist');
                }

                if (empty($required)) {
                    $this -> data -> required = 0;
                }
            } else {
                $this -> errors[] = __('No data was provided', 'wp-mailinglist');
            }

            return $this -> errors;
        }

        function save($data = array(), $validate = true)
        {

            if ($this -> language_do()) {
                $languagefields = array('label', 'caption', 'placeholder', 'errormessage', 'settings');

                foreach ($data as $key => $value) {
                    if (!empty($key) && in_array($key, $languagefields) && !empty($value)) {
                        switch ($key) {
                            case 'settings'				:
                                $value = maybe_unserialize($value);
                                if (!empty($value) && is_array($value)) {
                                    foreach ($value as $language => $settings) {
                                        $value[$language] = maybe_serialize($settings);
                                    }
                                }

                                $data[$key] = $this -> language_join($value);
                                break;
                            default 					:
                                $data[$key] = $this -> language_join($value);
                                break;
                        }
                    }
                }
            }

            return parent::save($data, $validate);
        }
    }
}

?>