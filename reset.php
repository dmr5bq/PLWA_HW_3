<?php

/**
 * Created by PhpStorm.
 * User: dmr5bq
 * Date: 10/17/16
 * Time: 8:50 PM
 */
require "modules/php/functions.php";

session_start();

if (strcmp($_SERVER['QUERY_STRING'], '') != 0) {
    $key_ = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    $_SESSION['key'] = explode('=', $key_)[1];
}

$key = '';

if (isset($_SESSION['key'])) {
    $key = $_SESSION['key'];
}

$db_user = 'root';
$db_pass = '';
$db_name = 'PLWA_HW_3';
$db_host = 'localhost';

// establish connection
$database = new mysqli($db_host, $db_user, $db_pass, $db_name);

/*
if (strcmp($_SERVER['QUERY_STRING'], '') == 0 || !is_valid_url_key($_SERVER['REQUEST_URI'], $database)) {
    header('location: index.php');
}*/

$url = $_SERVER['REQUEST_URI'];

$check_0 = isset($_POST['username']);
$check_1 = isset($_POST['pass1']);
$check_2 = isset($_POST['pass2']);

$was_submitted = $check_0 && $check_1 && $check_2;

// execute this after form submission
if ( $was_submitted ) {
    // locals from POST data
    $username = $_POST['username'];
    $password = $_POST['pass1'];
    $password_conf = $_POST['pass2'];

    // make sure the passwords match
    if (strcmp($password, $password_conf) == 0):

        // check if the key in the URL matches the associated unique_id in the database
        if (is_valid_key_for_username($username, $key, $database)):

            // set the password in the database to match the encrypted form of the new password
            update_password_for_user($username, $password, $database);

            // destroy session records
            session_destroy();

            // redireact to home
            header('location: index.php');
        endif;
    endif;
} ?>



<h3>Reset your password</h3>
<br/>
<form action="reset.php" method="post">
    <p>Username</p>
    <br/>
    <input name="username" type="text" required>
    <br/><br/>
    <p>New Password</p>
    <br/>
    <input name="pass1" type="password" required>
    <br/><br/>
    <p>Confirm Password</p>
    <br/>
    <input name="pass2" type="password" required>
    <br/><br/>
    <br/>
    <button type="submit" class="btn btn-md btn-primary">Reset Your Password</button>
</form>



<!-- style block -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="style/master.css">
<!-- /style block -->

