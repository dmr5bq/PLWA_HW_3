<?php

/**
 * Created by PhpStorm.
 * User: dmr5bq
 * Date: 10/8/16
 * Time: 11:13 AM
 */
require 'functions.php';

class User
{
    public $username;
    private $password;
    public $first;
    public $last;
    public $email;
    public $privileges;


    public function __construct($u, $p, $f, $l, $e, $pr)
    {
        $this->username = $u;
        $this->password = $p;
        $this->first = $f;
        $this->last = $l;
        $this->email = $e;
        $this->privileges = $pr;
    }

    public function get_password_hash()
    {
        return encrypt($this->password);
    }

    public function set_password($password)
    {
        $this->password = $password;
    }

    public function store_record($database)
    {
        $u = trim($this->username);
        $p_hash = trim($this->get_password_hash());
        $f = trim($this->first);
        $l = trim($this->last);
        $e = trim($this->email);
        $pr = trim($this->privileges);

        $key = encrypt($p_hash);

        $database->query("
            INSERT INTO Users (username, password, first, last, email, privileges) 
            VALUES ('$u', '$p_hash', '$f', '$l', '$e', '$pr');
        ") or die($database->error);

        $database->query("
            INSERT INTO UserKeys (username, email, unique_id)
            VALUES ('$u' ,'$e', '$key')
        ") or die($database->error);

    }
}