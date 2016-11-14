<?php
/**
 * Created by PhpStorm.
 * User: student
 * Date: 11/10/16
 * Time: 11:43 AM
 */

function route($is_admin) {
    if ($is_admin) {
        $_SESSION['admin'] = true;
        header('location: admin/');
    } else {
        header('location: user/');
    }
}

// determine whether a matching record exists in the database
function authenticate($database, $username, $password)
{
    // encrypt the password to match what is stored in DB
    $password_hash = encrypt($password);

    echo $password_hash;

    // defined admin permissions string for clarity
    $admin_permissions = '1';

    // find all records which match the username; should be only one
    $valid_username = $database->query("
        SELECT * FROM Users WHERE username='" . $username . "'
     ");

    // find all records which match the encrypted password; should be the same record as above
    $valid_password = $database->query("
        SELECT * FROM Users WHERE password='" . $password_hash . "'
    ");

    if (mysqli_num_rows($valid_username) > 0) {
        if (mysqli_num_rows($valid_password) > 0) {

            $record = mysqli_fetch_array($valid_username);

            $permissions = $record[5];

            $is_admin = strcmp($permissions, $admin_permissions) == 0;

            $_SESSION['is_valid'] = true;


            route($is_admin);
        } else {
            header('location: index.php');
        }
    } else {
        header('location: index.php');
    }
}