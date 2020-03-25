<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/wp-config.php';

// First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // register
    wp_create_user(htmlspecialchars($_POST['username']), htmlspecialchars($_POST['password']), htmlspecialchars($_POST['email']));
    // login
    $info = array();
    $info['user_login'] = htmlspecialchars($_POST['username']);
    $info['user_password'] = htmlspecialchars($_POST['password']);
    $info['remember'] = true;

    // login
    $user_signon = wp_signon( $info, false );
    die();
