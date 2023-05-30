<?php

if (!class_exists('wpmlCountry')) {
    class wpmlCountry extends wpmlDbHelper
    {
        var $model = 'Country';
        var $controller = 'countries';
        var $table = '';

        var $fields = array(
            'id'				=>	"INT(11) NOT NULL AUTO_INCREMENT",
            'value'				=>	"VARCHAR(150) NOT NULL DEFAULT ''",
            'code'				=>	"VARCHAR(20) NOT NULL DEFAULT ''",
            'isocode'			=>	"VARCHAR(20) NOT NULL DEFAULT ''",
            'created'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'modified'			=>	"DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'key'				=>	"PRIMARY KEY (`id`)",
        );

        var $tv_fields = array(
            'id'				=>	array("INT(11)", "NOT NULL AUTO_INCREMENT"),
            'value'				=>	array("VARCHAR(150)", "NOT NULL DEFAULT ''"),
            'code'				=>	array("VARCHAR(20)", "NOT NULL DEFAULT ''"),
            'isocode'			=>	array("VARCHAR(20)", "NOT NULL DEFAULT ''"),
            'created'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'modified'			=>	array("DATETIME", "NOT NULL DEFAULT '0000-00-00 00:00:00'"),
            'key'				=>	"PRIMARY KEY (`id`)",
        );

        var $indexes = array('value');

        function __construct($data = array())
        {
            parent::__construct();

            global $wpdb, $Db;

            $this -> table = $this -> pre . $this -> controller;

            if (is_admin()) {
                $query = "SELECT `id` FROM `" . $wpdb -> prefix . $this -> table . "` LIMIT 1";
                $query_hash = md5($query);
                if ($ob_countries = $this -> get_cache($query_hash, 'query')) {
                    $countries = $ob_countries;
                } else {
                    $countries = $wpdb -> get_var($query);
                    $this -> set_cache($query_hash, 'query');
                }

                if ($this -> get_option('countriesinserted') == "N" || empty($countries)) {
                    global $wpmlsql;

                    $this -> tables[$this -> pre . $this -> controller] = $this -> fields;
                    $this -> check_table($this -> pre . $this -> controller);

                    include($this -> plugin_base() . DS . 'vendors' . DS . 'sql.countries.php');
                    $query = "TRUNCATE TABLE `" . $wpdb -> prefix . $this -> Country() -> table . "`";
                    $wpdb -> query($query);
                    $wpdb -> query($countriesquery);

                    $this -> update_option('countriesinserted', "Y");
                }
            }

            if (!empty($data)) {
                foreach ($data as $dkey => $dval) {
                    $this -> {$dkey} = stripslashes_deep($dval);
                }
            }

            $Db -> model = $this -> model;
        }

        function select()
        {
            global $Db;

            $select = array();

            $Db -> model = $this -> model;
            if ($countries = $Db -> find_all(false, false, array('value', "ASC"))) {
                foreach ($countries as $country) {
                    $select[$country -> id] = $country -> value;
                }
            }

            return $select;
        }

        function select_code()
        {
            global $Db;

            $select = array();

            $Db -> model = $this -> model;
            if ($countries = $Db -> find_all(false, false, array('value', "ASC"))) {
                foreach ($countries as $country) {
                    $select[$country -> code] = $country -> value;
                }
            }

            return $select;
        }
    }
}

?>