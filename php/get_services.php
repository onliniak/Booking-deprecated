<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';
global $post;

$args = [
        'posts_per_page' => -1,
        'post_type' => 'services'
];

$myposts = get_posts( $args );
foreach ( $myposts as $service ){
  $i++;
  $arrayName = array('id' => $i ,
                     'calendarId' => 1 ,
                     'title' => apply_filters( 'the_title' , $service->post_title ) ,
                     'category' => 'default',
                     'dueDateClass' => '' ,
                     'start' => time(),
                     'end' => time() + 40*60
                    );
};

echo json_encode($arrayName, true);
