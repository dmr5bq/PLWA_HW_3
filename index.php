<?php

session_start();
if (session_id() != '' && session_id() != null) {
    session_destroy();
}

?>

<!-- style block -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="style/master.css">
<!-- /style block -->

<form action="router.php" method="post">
    <table class="padded-table">
        <tr>
            <td>
                <h1>Log in</h1>
            </td>
        </tr>
        <tr>
            <td>Username: </td>
            <td><input type="text" name="username"></td>
        </tr>
        <tr>
            <td>Password: </td>
            <td><input type="password" name="password"></td>
        </tr>
        <tr>
            <td><button type="submit" class="btn btn-md btn-success"> Log In <span class="glyphicon glyphicon-log-in"></span></button></td>
        </tr>
        <tr>
            <td><a href="forgot_password.php"><button type="button" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-question-sign"></span> Forgot Password</button></a></td>
            <td><a href="register.php"><button type="button" class="btn btn-sm btn-primary"><span class="glyphicon glyphicon-user"></span> Sign Up</button></a></td>
        </tr>
    </table>
</form>

<hr>

<table>
    <tr id="alert-frame">

    </tr>
    <tr><td><h4>Initialize the Database</h4></td></tr>
    <tr>
        <td>

                <button type="button" class="btn btn-sm btn-warning" onclick="init_default()"><span class="glyphicon glyphicon-tasks"></span> Initialize as Programmer</button>

        </td>
    </tr>
    <tr>
        <td>
                <button type="button" class="btn btn-sm btn-warning" onclick="init_TA()"><span class="glyphicon glyphicon-education"></span> Initialize as Instructor</button>

        </td>
    </tr>
</table>

<script type="text/javascript">
    function init_default() {

        var httpRequest;

        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
            }
        }
        else if (window.ActiveXObject) { // IE
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) {}
            }
        }
        if (!httpRequest) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/init/initME.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');


        httpRequest.send(null);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {
                    document.getElementById('alert-frame').innerHTML = "" +
                        "<div class=\"alert alert-success alert-dismissible\" role='alert'>" +
                            "<button type=\"button\" class=\"close\" data-dismiss='alert' aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                                "<strong>Success!</strong> Initialized the database as the programmer." +
                        "</div>";
                }
            }
        }
    }

    function init_TA() {

        var httpRequest;

        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
            }
        }
        else if (window.ActiveXObject) { // IE
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) {}
            }
        }
        if (!httpRequest) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/init/initTA.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');


        httpRequest.send(null);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {
                    document.getElementById('alert-frame').innerHTML = "" +
                        "<div class=\"alert alert-success alert-dismissible\" role='alert'>" +
                            "<button type=\"button\" class=\"close\" data-dismiss='alert' aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>" +
                            "<strong>Success!</strong> Initialized the database as a teaching assistant." +
                        "</div>";
                }
            }
        }
    }
</script>

