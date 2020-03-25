<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
global $wpdb;

$select = file_get_contents('php://input');
$decode = json_decode($select, true);

$ID = filter_var(
    $decode["ID"],
    FILTER_VALIDATE_INT
);
$animal = filter_var(
    $decode["animal"],
    FILTER_SANITIZE_STRING
);

echo get_post_meta( $ID, 'interval_'.$animal, true );
