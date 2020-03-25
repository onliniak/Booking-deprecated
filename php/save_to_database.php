<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
global $wpdb;

// Set timezone
date_default_timezone_set('Europe/Warsaw');

$select = file_get_contents('php://input');
$decode = json_decode($select, true);

// timestamp
$time = time();
// dzień tygodnia
$day = idate('w', time());
// javascript używa 13 znakowego timestampu.
$time_client = substr($decode["it's.me"], 0, 3);
$time_server = substr($time, 7,10);
$travelTime = $time_server - $time_client;

if (strlen($decode["it's.me"]) == 14 && $travelTime <= 10 && substr($decode["it's.me"], 3, 1) == $day){
      //to token jest prawidłowy
      $wpdb->insert(
          'calendar',
          array(      'Session_ID' => substr($decode["it's.me"], 4, 14),
                      'Service_ID' => $decode['Service_ID'],
                      'Salon_ID'  =>  $decode['Salon_ID'],
                      'Animal_name' => $decode['Animal_name'],
                      'time_Start'  =>  $decode['timestamp_Start'],
                      'time_End'  =>  $decode['timestamp_End'],
                    )
      );
      var_dump($wpdb->last_error);
}else{
  // Albo żeton nie pochodzi z frontendu albo ktoś tu używa Internet Explorera, czy innej Safari.
  die('martwy żeton');
}
