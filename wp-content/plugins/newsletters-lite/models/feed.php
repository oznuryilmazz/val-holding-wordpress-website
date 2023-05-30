<?php

if (!class_exists('wpmlFeed')) {
    class wpmlFeed extends wpMailPlugin
    {
        var $id = '';
        var $title = '';
        var $url = '';

        function __construct()
        {
            parent::__construct();

        }
    }
}

?>