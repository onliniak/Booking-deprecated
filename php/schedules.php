<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
global $wpdb;

$sql = $wpdb->get_results("
SELECT time_Start, time_End
FROM calendar
WHERE Salon_ID = 1
");

$c = count($sql);

for ($i=0; $i < $c; $i++) {
    $arrayName = array('id' => $i+1,
                       'calendarId' => 1,
                       'title' => 'title',
                       'category' => 'category',
                       'dueDateClass' => '',
                       'start' => $sql[$i]->time_Start,
                       'end' => $sql[$i]->time_End,
                       'isReadOnly' => 'true');
                     }

print_r($arrayName);
