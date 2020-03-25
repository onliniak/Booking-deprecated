<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
global $wpdb;

if (is_user_logged_in()) {
  $user = get_current_user_id();
    $sql = "SELECT Service_ID, Animal_name, Salon_ID, time_Start, time_End FROM `calendar` WHERE `User_ID` = '$user' ";
$result = $wpdb->get_results($sql);

    foreach( $result as $results ) {
        $salonID = $results->Salon_ID;
        $sql = "SELECT * FROM `calendar_salon` WHERE `Salon_ID` = '$salon' ";
$salon = $wpdb->get_results($sql);
    }
}else {
  wp_login_form();
}