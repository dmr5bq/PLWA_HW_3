<?php
// import the User class
require_once '../modules/php/User.php';
require_once '../modules/php/Ticket.php';

// variables declarations
$database_name = "PLWA_HW_3";
$init_u = 'root';


// access the database TL directory via root
$database_root = new mysqli('localhost', $init_u);

// if the database already exists when the script is run, kill it
$database_root->query("DROP DATABASE $database_name");

// initialize the new PLWA_HW_2 database
$database_root->query("CREATE DATABASE $database_name");

// set the $database variable to a new database reference to PLWA_HW_2
$database = new mysqli('localhost', $init_u , "" ,$database_name);

// build the Users table
$database->query("
            CREATE TABLE Users (username varchar(100), password varchar(100), first varchar(100), last varchar(100), email varchar(100), privileges varchar(1));
     ") or die($database->error);

// build the Users table
$database->query("
            CREATE TABLE Tickets (id int NOT NULL AUTO_INCREMENT , username varchar(100), first varchar(100), last varchar(100), email varchar(100), subject varchar(100), description varchar(1000), tech_username varchar(30), open varchar(5), timestamp varchar(30), PRIMARY KEY(id)
            );
     ")or die($database->error);

// build the Keys table
$database->query("
            CREATE TABLE UserKeys (username varchar(40), email varchar(100), unique_id varchar(100));
     ") or die($database->error);

// begin initialization

// constants
$init_file = 'data/init.txt';

// put the entire flat file in one string
$file_str = file_get_contents($init_file);

// separate the users section and the
$file_str = explode('#', $file_str);

$user_str = $file_str[0];
$ticket_str = $file_str[1];

// separate users in the flat file
$user_array = explode('/',$user_str);
$ticket_array = explode('/',$ticket_str);

foreach ($ticket_array as $ticket_entry):
    // separate the user fields into an array
    $ticket_entry = explode(':', $ticket_entry);


    foreach($ticket_entry as $item) {
        $item = rtrim($item);
    }
    // instantiate a new user class
    $ticket = new Ticket($ticket_entry[0], $ticket_entry[1], $ticket_entry[2], $ticket_entry[3], $ticket_entry[4], $ticket_entry[5]);

    // use the User store method to write to the database
    $ticket->store_record($database);
endforeach;

// save every user to the database
foreach ($user_array as $user_entry):

    // separate the user fields into an array
    $user_entry = explode(':', $user_entry);

    foreach($user_entry as $item) {
        $item = rtrim($item);
    }

    // instantiate a new user class
    $user = new User($user_entry[0], $user_entry[1], $user_entry[2], $user_entry[3], $user_entry[4], $user_entry[5]);

    // use the User store method to write to the database
    $user->store_record($database);

endforeach;

?>

<a href="../index.php">Go to the login page.</a>
