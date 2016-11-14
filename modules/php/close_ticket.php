<?php

require_once "functions.php";
require_once "Ticket.php";
require_once "PHPMailer/PHPMailerAutoload.php";

$mailer = new PHPMailer();
init_mailer($mailer);

$data = file_get_contents('php://input');

$data = json_decode($data, true);

$ticket_id = $data['ticket_id'];

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'PLWA_HW_3';

$database = new mysqli($db_host, $db_user, $db_pass, $db_name);

$email = get_email_from_ticket($ticket_id, $database);

close_ticket($ticket_id, $database);
send_ticket_closed($mailer, $email);

$ticket = get_ticket($ticket_id, $database);

$output_ticket = array();

$output_ticket[] = $ticket;

echo json_encode($output_ticket);
