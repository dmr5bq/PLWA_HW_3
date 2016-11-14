<?php

require_once "functions.php";

$data = file_get_contents('php://input');

$data = json_decode($data, true);

$ticket_id = $data['ticket_id'];

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'PLWA_HW_3';

$database = new mysqli($db_host, $db_user, $db_pass, $db_name);

delete_ticket($ticket_id, $database);