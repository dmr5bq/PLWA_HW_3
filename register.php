<?php

    require_once "modules/php/User.php";

    $was_submitted = isset($_POST['username']);


    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'PLWA_HW_3';

    $database = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($was_submitted) {

        $u = $_POST['username'];
        $p = $_POST['password'];
        $p_c = $_POST['password_confirm'];
        $f = $_POST['first'];
        $l = $_POST['last'];
        $e = $_POST['email'];

        if (strcmp($p, $p_c) != 0) {
            echo "<div class='alert alert-danger'>Your passwords did not match. Try again.</div>";
        } else {

            $user = new User($u, $p, $f, $l, $e, '0');

            $user->store_record($database);

            header('location: http://localhost/PLWA_HW_3/');
        }

    }


?>

<!-- style block -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="style/master.css">
<!-- /style block -->

<h2>Welcome! Add a new profile.</h2>
<form action="register.php" method="post">
    <table>
        <tr>
            <td>Username </td>
            <td><input type="text" name="username" required></td>
        </tr>
        <tr>
            <td>Password </td>
            <td><input type="password" name="password" required></td>
        </tr>
        <tr>
            <td>Confirm Password </td>
            <td><input type="password" name="password_confirm" required></td>
        </tr>
        <tr>
            <td>First Name </td>
            <td><input type="text" name="first" required></td>
        </tr>
        <tr>
            <td>Last Name </td>
            <td><input type="text" name="last" required></td>
        </tr>
        <tr>
            <td>Email Address </td>
            <td><input type="email" name="email" required></td>
        </tr>
        <tr>
            <td><button class="btn btn-lg btn-success">Register Account</button></td>
            <td><a href="http://localhost/PLWA_HW_3"><button class="btn btn-md btn-info" button type="button">Go Back</button></a></td>
        </tr>
    </table>
</form>
