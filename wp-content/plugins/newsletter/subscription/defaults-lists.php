<?php

$options = array();

for ($i = 1; $i <= NEWSLETTER_LIST_MAX; $i++) {
    $options['list_' . $i] = '';
    $options['list_' . $i . '_status'] = 0;
    $options['list_' . $i . '_checked'] = 0;
    $options['list_' . $i . '_forced'] = 0;
    $options['list_' . $i . '_subscription'] = 0;
    $options['list_' . $i . '_profile'] = 0;
}
