<?php

require_once 'similarity.php';

// returns a DES3 encrypted string
function encrypt($string) {
    // this WILL hash the password in a deterministic way
    $salt = 'zyxwabcdegfh';
    return crypt($string,$salt);
}

// prints a 1D array
function print_array($array) {
    foreach ($array as $entry):
        echo $entry."<br/>";
    endforeach;
}

// sets up a mailer for this project's settings
function init_mailer($mailer) {

    $mailer->IsSMTP(); // telling the class to use SMTP
    $mailer->SMTPAuth = true; // enable SMTP authentication
    $mailer->SMTPSecure = "tls"; // sets tls authentication
    $mailer->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server; or your email service
    $mailer->Port = 587; // set the SMTP port for GMAIL server; or your email server port
    $mailer->Username = "cs4640homework2@gmail.com"; // email username
    $mailer->Password = "8782Dom!!"; // email password

}

// uses a ticket object to send an email to the submitted
function send_email_confirmation($ticket, $mailer) {

    // will handle mailing to client
    $sender = strip_tags($mailer->Username);
    $recipient = strip_tags($ticket->email);
    $subject = strip_tags("Your support ticket: $ticket->subject");
    $msg = generate_email_confirmation_body($ticket);

    // Put information into the message
    $mailer->addAddress($recipient);
    $mailer->SetFrom($sender);
    $mailer->Subject = "$subject";
    $mailer->Body = "$msg";

    $mailer->send()
    or die($mailer->ErrorInfo);

}

// notify a submitter their ticket has been opened
function send_ticket_open($mailer, $submitter_email) {

    $sender = strip_tags($mailer->Username);
    $subject = strip_tags("Your support ticket is now OPEN");
    $msg = "Notice: Your ticket has been opened.";

    // Put information into the message
    $mailer->addAddress($submitter_email);
    $mailer->SetFrom($sender);
    $mailer->Subject = "$subject";
    $mailer->Body = "$msg";

    $mailer->send()
    or die($mailer->ErrorInfo);

}

// notify a submitter their ticket has been closed
function send_ticket_closed($mailer, $submitter_email) {

    $sender = strip_tags($mailer->Username);
    $subject = strip_tags("Your support ticket is now CLOSED");
    $msg = "Notice: Your ticket has been closed.";

    // Put information into the message
    $mailer->addAddress($submitter_email);
    $mailer->SetFrom($sender);
    $mailer->Subject = "$subject";
    $mailer->Body = "$msg";

    $mailer->send()
    or die($mailer->ErrorInfo);

}



function send_email_administrators($ticket, $mailer, $database) {

    // pull all administrators' emails from DB
    $email_list = get_administrator_emails($database);

    $mailer->SetFrom($mailer->Username);
    $mailer->Subject = "New support ticket: $ticket->subject";

    // generate an email body to send to admins
    $mailer->Body = generate_email_administrator_body($ticket);

    // takes an array of emails and adds them to the mailer
    add_all_email_recipients($mailer, $email_list);

    $mailer->send()
    or die($mailer->ErrorInfo);

}


function generate_email_confirmation_body($ticket) {

    return "Hello, $ticket->first,\nThis is a confirmation of your ticket submission. You submitted a ticket with the following message: \n
        $ticket->description";

}


function generate_email_administrator_body($ticket) {

    return
        "Hello, a new ticket has been submitted by user '$ticket->username'.
        The ticket has the following content:
            User: $ticket->username
            Name: $ticket->first $ticket->last
            Email: $ticket->email
            
            Subject: $ticket->subject
            Message: $ticket->description
           
        Log in to check out this ticket or another.";

}


function get_administrators($database) {

    $result = $database->query("
            SELECT * FROM Users WHERE privileges='1'
        ");

    // initialize a blank array;
    $records = array();

    // go through array of results and store them
    while($record = mysqli_fetch_array($result)) {
        $records[] = $record;
    }

    // hand back the records array
    return $records;

}


function get_administrator_emails($database) {

    // pull all administrators from DB
    $administrator_records = get_administrators($database);

    // put email records into an array
    $email_list = array();


    foreach ($administrator_records as $record) {

        // save only emails from admin files
        $email_list[] = $record[4];

    }

    // return list of emails
    return $email_list;

}

function add_all_email_recipients($mailer, $list) {

    // run through a list of emails and insert them into the mailer
    foreach ($list as $email):

        $mailer->addAddress($email);

    endforeach;

}


function produce_array($sql_result) {

    $rows = array();

    // find out the size of the result
    $count = mysqli_num_rows($sql_result);

    for ($i = 0; $i < $count; $i++) {

        // get the 1D array repr of the result row
        $row = mysqli_fetch_array($sql_result);

        //append to output array
        $rows[] = $row;

    }

    // hand back the array of rows
    return $rows;
}

function generate_sort_button($attr) {

    // return a sort button which holds the value of the attribute name
    return "
        <td>
            <i>Sort by</i>
            <input type='radio' name='sort-by' value='$attr'>
        </td>
        ";

}


function generate_select_button($ticket_id) {

    // return a select button which holds the value of the attribute parameter
    return "
        <td>
            <input type='radio' name='select-ticket' value='$ticket_id'>
        </td>
    ";

}


function get_open_tickets($database) {

    // returns all open tickets in DB
    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.open='1';
    ");

    return produce_array($sql_result);

}

function get_closed_tickets($database) {

    // returns all open tickets in DB
    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.open='0';
    ");

    return produce_array($sql_result);

}


function get_all_tickets($database) {

    $sql_result = $database->query("
        SELECT * FROM Tickets;
    ");

    return produce_array($sql_result);

}

function get_user_tickets($username, $database) {

    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.tech_username='$username';
    ");

    return produce_array($sql_result);

}

function get_unassigned_tickets($database) {

    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.open='1' and Tickets.tech_username='none';
    ");

    return produce_array($sql_result);

}


function get_open_tickets_by_username($database) {

    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.open='1' ORDER BY Tickets.username;
    ");

    return produce_array($sql_result);

}

function get_open_tickets_by_last($database) {

    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.open='1' ORDER BY Tickets.last;
    ");

    return produce_array($sql_result);

}


function get_open_tickets_by_email($database) {

    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.open='1' ORDER BY Tickets.email;
    ");

    return produce_array($sql_result);

}


function get_open_tickets_by_subject($database) {

    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.open='1' ORDER BY Tickets.subject;
    ");

    return produce_array($sql_result);

}


function get_open_tickets_by_timestamp($database) {

    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.open='1' ORDER BY Tickets.timestamp;
    ");

    return produce_array($sql_result);

}

function sort_op($attr, $database) {

    switch($attr):

        case 'id':

            return get_open_tickets($database);
            break;


        case 'username':

            return get_open_tickets_by_username($database);
            break;


        case 'last':

            return  get_open_tickets_by_last($database);
            break;


        case 'email':

            return  get_open_tickets_by_email($database);
            break;


        case 'subject':

            return  get_open_tickets_by_subject($database);
            break;


        case 'timestamp':

            return  get_open_tickets_by_timestamp($database);
            break;


        default:

            return get_open_tickets($database);
            break;

    endswitch;

}


function toggle_open($ticket_id, $submitter_email, $mailer, $database) {

    if (is_open($ticket_id, $database)) {

        // sends an email telling the submitter their ticket is closed
        send_ticket_closed($mailer, $submitter_email);

        // close the ticket in the database
        close_ticket($ticket_id, $database);

    } else {

        // send an email telling the submitter their ticket is opened
        send_ticket_open($mailer, $submitter_email);

        // open the ticket in the database
        open_ticket($ticket_id, $database);
    };

}


function get_username_from_ticket($ticket_id, $database) {

    // ticket id -> username of submitter
    $sql_result = $database->query("
        SELECT Tickets.username FROM Tickets WHERE Tickets.id='$ticket_id';
    ");

    return produce_array($sql_result)[0][0]; // only one entry

}


function get_email_from_ticket($ticket_id, $database) {

    // ticket id -> email address of submitter
    $sql_result = $database->query("
        SELECT Tickets.email FROM Tickets WHERE Tickets.id='$ticket_id';
    ");

    return produce_array($sql_result)[0][0];

}


function get_email_from_user($username, $database) {

    // username -> email address
    $sql_result = $database->query("
        SELECT Users.email FROM Users WHERE Users.username='$username';
    ");

    return produce_array($sql_result)[0][0];

}

function get_ticket($ticket_id, $database) {

    // gets a single ticket record by ID
    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.id='$ticket_id';
    ");

    return produce_array($sql_result)[0];

}


function close_ticket($ticket_id, $database) {

    // set open field to 0
    $database->query("
        UPDATE Tickets SET open='0' WHERE Tickets.id='$ticket_id';
    ");

}

function open_ticket($ticket_id, $database) {

    // set open field to 1
    $database->query("
        UPDATE Tickets SET open='1' WHERE Tickets.id='$ticket_id';
    ");

}


function is_open($ticket_id, $database) {

    // returns true if open field is 1
    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.id='$ticket_id';
    ");

    return (produce_array($sql_result)[0][8] == '1'); // selects the open field

}


function is_assigned($ticket_id, $database) {

    // returns true if ticket assignment field is not none
    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.id='$ticket_id';
    ");

    return (strcmp(produce_array($sql_result)[0][7],'none') != 0); // selects the open field

}


function is_assigned_to_user($ticket_id, $admin_username, $database) {

    // returns true if the ticket id matches the input username
    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.id='$ticket_id';
    ");

    return (strcmp(produce_array($sql_result)[0][7], $admin_username) == 0);

}


function assign_to_user($ticket_id, $admin_username, $database) {
    $database->query("
        UPDATE Tickets SET tech_username='$admin_username' WHERE Tickets.id='$ticket_id';
    ");
}

function remove_from_user($ticket_id, $admin_username, $database) {
    $database->query("
        UPDATE Tickets SET tech_username='none' WHERE Tickets.id='$ticket_id';
    ");
}

function delete_ticket($ticket_id, $database) {
    $database->query("
        DELETE FROM Tickets WHERE Tickets.id='$ticket_id';
    ");
}

function get_tickets_from_user($submitter_username, $database) {
    $sql_result = $database->query("
        SELECT * FROM Tickets WHERE Tickets.username='$submitter_username';
    ");

    return produce_array($sql_result);
}

function get_user_key($email, $database) {
    $sql_result = $database->query("
        SELECT UserKeys.unique_id FROM UserKeys WHERE UserKeys.email='$email';
    ");

    return produce_array($sql_result)[0][0];
}

function generate_url_with_key($email, $database) {
    $domain = 'localhost';
    $page = '/PLWA_HW_3/reset.php';
    $key = get_user_key($email, $database);
    return 'http://'.$domain.$page."?key=$key";
}

function is_valid_url_key($url, $database) {
    $key = parse_url($url, PHP_URL_QUERY);
    $key = explode('=',$key)[1];

    $sql_result = $database->query("
        SELECT UserKeys.unique_id FROM UserKeys WHERE UserKeys.unique_id='$key';
    ");

    return count(produce_array($sql_result)) > 0;
}

function update_password_for_user($username, $password, $database) {
    $new_password = encrypt($password);
    $unique_id = encrypt($new_password);
    $database->query("
        UPDATE Users SET password='$new_password' WHERE Users.username='$username';
    ");
    $database->query("
        UPDATE UserKeys SET unique_id='$unique_id' WHERE UserKeys.username='$username';
    ");
}

function is_valid_key_for_username($username, $key, $database) {

    $sql_result = $database->query("
        SELECT UserKeys.unique_id FROM UserKeys WHERE username='$username';
    ");

    $key_in_db = produce_array($sql_result)[0][0];
    echo $key;
    echo ' '.$key_in_db;

    return strcmp($key, $key_in_db) == 0;
}

function match_username_email($username, $email, $database) {
    $sql_result = $database->query("
        SELECT * FROM Users WHERE Users.username='$username' and Users.email='$email'
    ");

    $n = count(produce_array($sql_result));

    return $n > 0;
}

function send_forgot_password_email($email, $mailer, $database) {
    $sender = strip_tags($mailer->Username);
    $subject = strip_tags("Forgot password reset");

    // Put information into the message
    $mailer->addAddress($email);
    $mailer->SetFrom($sender);
    $mailer->Subject = "$subject";
    $mailer->Body = "Follow this link to reset your password: ".generate_url_with_key($email, $database);

    $mailer->send()
    or die($mailer->ErrorInfo);
}


// choose a location based on whether or not the user is an admin



function generate_null_cell() {
    echo "<td></td>";
}


function generate_ticket_sort_row() {
    echo generate_sort_button('id');
    echo generate_sort_button('username');
    echo generate_null_cell();
    echo generate_sort_button('last');
    echo generate_sort_button('email');
    echo generate_sort_button('subject');
    echo generate_null_cell();
    echo generate_null_cell();
    echo generate_null_cell();
    echo generate_sort_button('timestamp');
    echo generate_null_cell();
}


function get_all_ticket_descriptions($database) {
    $sql_result = $database->query("
        SELECT Tickets.description FROM Tickets;
    ");

    return produce_array($sql_result);
}


function get_ticket_descriptions_with_id ( $database ) {

    $sql_result = $database->query ("

        SELECT * FROM Tickets;
    
    ");

    $array = produce_array ( $sql_result );

    $ret_array = array ( );

    foreach ($array as $ticket) {
        $entry = array( $ticket[0] , $ticket[6] );
        $ret_array[] = $entry;
    }

    return $ret_array;
}


function get_description_from_ticket( $ticket_id , $database ) {

    $sql_result = $database->query("

        SELECT Tickets.description FROM Tickets WHERE Tickets.id='$ticket_id';
        
    ");

    return produce_array($sql_result)[0][0];

}


function get_all_similar_tickets($ticket_id, $database) {

    $similarity_array = array( );

    $this_description = get_description_from_ticket( $ticket_id , $database );

    $all_descriptions = get_ticket_descriptions_with_id( $database );

    foreach ( $all_descriptions as $desc_item ):

        $other_ticket_id =   $desc_item[0];
        $other_description = $desc_item[1];

        $similarity_value = get_similarity($this_description, $other_description);

        $entry = array($similarity_value, $other_ticket_id);

        if ($ticket_id != $other_ticket_id)
            $similarity_array[] = $entry;

    endforeach;

    array_multisort($similarity_array, SORT_DESC);

    $output = array();

    foreach ($similarity_array as $item):

        $output[] = $item[1];

    endforeach;

    return $output;

}

function construct_ticket_array_from_ids( $array_of_ids , $database ) {

    $output = array();

    foreach ( $array_of_ids as $ticket_id ):

        $output[] = get_ticket($ticket_id, $database);

    endforeach;

    return $output;
}

function get_first_from_user($username, $database) {
    // username -> email address
    $sql_result = $database->query("
        SELECT Users.first FROM Users WHERE Users.username='$username';
    ");

    return produce_array($sql_result)[0][0];
}

function get_last_from_user($username, $database) {
    // username -> email address
    $sql_result = $database->query("
        SELECT Users.last FROM Users WHERE Users.username='$username';
    ");

    return produce_array($sql_result)[0][0];
}

