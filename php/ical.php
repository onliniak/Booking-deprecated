<?php
header('Content-type: text/calendar');
header('Content-Disposition: attachment; filename=psiakosc.ics');
require '../libs/icalendar/zapcallib.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
global $wpdb;

$sql = $wpdb->get_results("
  SELECT time_Start, time_End, Animal_name
  FROM calendar
  ");

  $c = count($sql);

  $icalobj = new ZCiCal();

  for ($i=0; $i < $c; $i++) {
    ${"yxc".$i} = new ZCiCalNode("VEVENT", $icalobj->curnode);

    $event_start = strtotime($sql[$i]->time_Start);
    $event_end = strtotime($sql[$i]->time_End);

    ${"yxc".$i}->addNode(new ZCiCalDataNode("SUMMARY:" . $sql->Animal_name));
    ${"yxc".$i}->addNode(new ZCiCalDataNode("DTSTART:" . ZCiCal::fromSqlDateTime($event_start)));
    ${"yxc".$i}->addNode(new ZCiCalDataNode("DTEND:" . ZCiCal::fromSqlDateTime($event_end)));

    ${"yxc".$i}->addNode(new ZCiCalDataNode("UID:" . wp_generate_uuid4()));
    ${"yxc".$i}->addNode(new ZCiCalDataNode("DTSTAMP:" . ZCiCal::fromSqlDateTime()));

    ${"yxc".$i}->addNode(new ZCiCalDataNode("Description:" . ZCiCal::formatContent(
        "Event")));

  }
echo $icalobj->export();
