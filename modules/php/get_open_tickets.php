<?php


require_once 'functions.php';

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'PLWA_HW_3';

$database = new mysqli($db_host, $db_user, $db_pass, $db_name);

$tickets = get_open_tickets($database);

$output_tickets = array();

foreach ($tickets as $ticket) {


    $tmp = array();
    $len = count($ticket);

    $tmp['id'] = $ticket[0];
    $tmp['username'] = $ticket[1];
    $tmp['first'] = $ticket[2];
    $tmp['last'] = $ticket[3];
    $tmp['email'] = $ticket[4];
    $tmp['subject'] = $ticket[5];
    $tmp['description'] = $ticket[6];
    $tmp['tech_username'] = $ticket[7];
    $tmp['open'] = $ticket[8];
    $tmp['timestamp'] = $ticket[9];

    $output_tickets[] = $tmp;
}

echo json_encode($output_tickets);