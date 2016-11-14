<?php

require_once "functions.php";

$data = file_get_contents('php://input');

$data = json_decode($data, true);

$ticket_id = $data['ticket_id'];
$username = $data['username'];

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'PLWA_HW_3';

$database = new mysqli($db_host, $db_user, $db_pass, $db_name);
if (is_assigned_to_user($ticket_id, $username, $database) && is_open($ticket_id, $database)) {
    assign_to_user($ticket_id, 'none', $database);
}

$ticket = get_ticket($ticket_id, $database);

$output_ticket = array();

$output_ticket[] = $ticket;

echo json_encode($output_ticket);

