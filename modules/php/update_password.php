<?php

require_once 'functions.php';

$data = file_get_contents('php://input');

$data = json_decode($data, true);

$username = $data['username'];
$password = $data['password'];

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'PLWA_HW_3';

$database = new mysqli($db_host, $db_user, $db_pass, $db_name);

update_password_for_user($username, $password, $database);

echo "
    <div class='alert alert-success'>Your password has been changed</div>
";