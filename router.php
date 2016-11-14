<?php

require_once 'modules/php/PHPMailer/PHPMailerAutoload.php';
require_once 'modules/php/login_functions.php';
require_once 'modules/php/functions.php';

session_start();

if (!isset($_POST['username'])):
    header('location: location: http://localhost/PLWA_HW_3/'); endif;

// initialize singleton mailer
$_SESSION['mailer'] = new PHPMailer();
init_mailer($_SESSION['mailer']);

// variables
$database_name  = 'PLWA_HW_3';
$database_login = 'root';

// create local copies of the post variables
$username = $_POST['username'];
$password = $_POST['password'];

// access the database to compare
$database = new mysqli('localhost', $database_login,'',$database_name);

// set session variable for username
$_SESSION['username'] = $username; // will be stored in tickets table

// check authentication and route based on acceptance
authenticate($database, $username, $password);






