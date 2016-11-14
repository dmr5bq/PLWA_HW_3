<?php

/**
 * Created by PhpStorm.
 * User: dmr5bq
 * Date: 10/8/16
 * Time: 12:19 PM
 */

class Ticket
{
    public $id;
    public $username; // foreign key to Users
    public $timestamp;
    public $first;
    public $last;
    public $email;
    public $subject;
    public $description;
    public $is_open;


    public function __construct($u, $f, $l, $e, $s, $d)
    {
        $this->username = $u;
        $this->first = $f;
        $this->last = $l;
        $this->email = $e;
        $this->subject = $s;
        $this->description = $d;
        $this->is_open = '1';
        $this->timestamp = date(DATE_RSS);
    }

    public function store_record($database) {
        $u = $this->username;
        $f = $this->first;
        $l = $this->last;
        $e = $this->email;
        $s = $this->subject;
        $d = $this->description;
        $open = $this->is_open;
        $def_t = 'none';
        $time = $this->timestamp;

        $database->query("
            INSERT INTO Tickets (username, first, last, email, subject, description, tech_username, open, TIMESTAMP) 
            VALUES ('$u', '$f', '$l', '$e', '$s', '$d', '$def_t', '$open', '$time');
        ") or die($database->error);
    }
}