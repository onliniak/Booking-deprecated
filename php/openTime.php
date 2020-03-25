<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
global $wpdb;

$ID = file_get_contents('php://input');
$newID = filter_var(
    $ID,
    FILTER_VALIDATE_INT,
    array('options' => array('min_range' => 1, 'max_range' => 5))
);

$sql = $wpdb->get_results("
SELECT
  time_Start,
  time_End
FROM `calendar_salon`
WHERE Salon_ID = ".$newID."
");
echo json_encode($sql);
