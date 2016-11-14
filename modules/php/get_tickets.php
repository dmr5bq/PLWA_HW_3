<?php

require_once 'functions.php';

$data = file_get_contents('php://input');

$data = json_decode($data, true);

$username = $data['username'];

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'PLWA_HW_3';

$database = new mysqli($db_host, $db_user, $db_pass, $db_name);

$tickets = get_tickets_from_user($username, $database);

echo "
<h3>Support Tickets Submitted By <span class='highlighted-username'>$username</span></h3>
<br>
<table class='padded-table table-colored'>
    <tr style='border-bottom: 1px solid black;'>
        <td align='center'>ID</td>
        <td align='center'>Username</td>
        <td align='center'>First</td>
        <td align='center'>Last</td>
        <td align='center'>Email</td>
        <td align='center'>Subject</td>
        <td align='center'>Description</td>
        <td align='center'>Assigned To</td>
        <td align='center'>Open (0/1)</td>
        <td align='center'>Submitted</td>
    </tr>
";

foreach ($tickets as $ticket):

    echo "<tr>";

    $len = count($ticket);

    for ($i = 0 ; $i < $len/2 ; $i++) {
        echo "<td> $ticket[$i] </td>";
    }

    echo "</tr>";

endforeach;

echo "</table>";