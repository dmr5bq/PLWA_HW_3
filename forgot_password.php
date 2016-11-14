<?php
?>
<!-- style block -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="style/master.css">
<!-- /style block -->

<?php
require "modules/php/functions.php";
require "modules/php/PHPMailer/PHPMailerAutoload.php";

// verifying POST data was submitted
$check_0 = isset($_POST['username']);
$check_1 = isset($_POST['email']);

$was_submitted = $check_0 && $check_1;

if ($was_submitted) {
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'PLWA_HW_3';
    $db_host = 'localhost';

    // establish connection
    $database = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // create a mailer
    $mailer = new PHPMailer();
    init_mailer($mailer);

    // clean and set user details
    $username = rtrim($_POST['username']);
    $email = rtrim($_POST['email']);

    // validate input email against that of username record in the database
    if (match_username_email($username, $email, $database)):

        // use the mailer to send an email to the user with reset link
        // pulls the unique_id from the database to insert into the url
        send_forgot_password_email($email, $mailer, $database);

        //sure-facing display
        echo "We've emailed $email with the link to reset your password.";
        echo "<br/><br/><a href='index.php'><button type=\"button\" class=\"btn btn-md btn-warning\">Go Back</button></a>";
    endif;
} else {
    // print form if first load
    echo "
    <!-- This is the form that is used to submit info for reset -->
    <form method=\"post\" action=\"forgot_password.php\">
    <table>
        <h2>Forgot your password?</h2>
        <tr>
            <td>Username </td>
            <td><input type=\"text\" name=\"username\" placeholder=\"Ex: example1\"></td>
        </tr>
        <tr>
            <td>Email Address </td>
            <td><input type=\"text\" name=\"email\" placeholder=\"Ex: john@example.com\"></td>
        </tr>
        <tr>
            <td><button type=\"submit\" class=\"btn btn-md btn-primary\">Reset</button></td>
            <td><a href='index.php'><button type=\"button\" class=\"btn btn-sm btn-info\">Go Back</button></a></td>
        </tr>
    </table>
</form> ";
}

?>



