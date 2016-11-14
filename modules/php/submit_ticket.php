<?php

require_once "functions.php";
require_once "Ticket.php";
require_once "PHPMailer/PHPMailerAutoload.php";

$mailer = new PHPMailer();
init_mailer($mailer);

$data = file_get_contents('php://input');

$data = json_decode($data, true);

$username = $data['username'];
$subject = $data['subject'];
$description = $data['description'];

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'PLWA_HW_3';

$database = new mysqli($db_host, $db_user, $db_pass, $db_name);

$email = get_email_from_user($username, $database);
$first = get_first_from_user($username, $database);
$last = get_last_from_user($username, $database);

$ticket = new Ticket($username, $first, $last, $email, $subject, $description);

send_email_administrators($ticket, $mailer, $database);
send_email_confirmation($ticket, $mailer);

$ticket->store_record($database);

echo "
    <div class='alert alert-success'>Your ticket has been added to the database, and a confirmation has been sent to $email</div>
";


