<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';

$select = file_get_contents('php://input');

if(empty($select)) {
    echo wp_generate_uuid4();
}else{
  wp_is_uuid( $select, 4 );
  echo 'UUID jest prawidłowy';
}
