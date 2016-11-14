<?php

session_start();

require_once "functions.php";

$data = file_get_contents('php://input');

$data = json_decode($data, true);

$ticket_id = $data['ticket_id'];

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'PLWA_HW_3';

$database = new mysqli($db_host, $db_user, $db_pass, $db_name);

$ticket = get_ticket($ticket_id, $database);

$output_ticket = array();

$tmp['id'] = $ticket_id;
$tmp['username'] = $ticket['username'];
$tmp['first'] = $ticket['first'];
$tmp['last'] = $ticket['last'];
$tmp['email'] = $ticket['email'];
$tmp['subject'] = $ticket['subject'];
$tmp['description'] = $ticket['description'];
$tmp['tech_username'] = $ticket['tech_username'];
$tmp['open'] = $ticket['open'];
$tmp['timestamp'] = $ticket['timestamp'];

$output_ticket[] = $tmp;

$_SESSION['ticket_id'] = $ticket_id;

echo json_encode($output_ticket);