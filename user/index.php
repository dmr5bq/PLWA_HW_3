<?php

session_start();

if (isset($_SESSION['username']))
    $username = $_SESSION['username'];
else {
    header('location: http://localhost/PLWA_HW_3/');
}

?>

<!-- style block -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="../style/master.css">
<!-- /style block -->

<h1 class="dark-header">Welcome, <span class='highlighted-username'><?php echo $_SESSION['username']; ?></span></h1>
<table>
    <tr>
        <td>
            <a href="../index.php">
                <button type="button" class="btn btn-sm btn-danger"><span class="glyphicon glyphicon-remove-sign"></span> Log Out</button>
                <hr>
            </a>
        </td>
    </tr>
    <tr>
        <td>
            <button class="btn btn-md btn-primary" id="my_ticket_btn" onclick="my_tickets('<?php echo $_SESSION['username'] ?>')">
                <span class="glyphicon glyphicon-user"></span> View My Tickets
            </button>
        </td>
        <td>
            <button class="btn btn-md btn-primary" id="submit_ticket_btn" onclick="ticket_form()">
                <span class="glyphicon glyphicon-open"></span> Submit a New Ticket
            </button>
        </td>
        <td>
            <button class="btn btn-md btn-primary" id="change_password_btn" onclick="password_form()">
                <span class="glyphicon glyphicon-edit"></span> Change Password
            </button>
        </td>
    </tr>
</table>
<hr>
<div id="display-frame"><!-- Will fill with selection --></div>

<!-- Begin Script -->
<script type="text/javascript">

    var clear_button = "" +
     "<button class=\"btn btn-md btn-warning\" onclick=\"clear_frame()\"><span class=\"glyphicon glyphicon-remove\"></span></button>";


        function password_form() {
        var output = "" +
            "<form method=\"post\" id=\"password-form\">" +
            "<br>" +
            "<h3>Change Your Password</h3>" +
            '<br>' +
            '<table>' +
            '<tr>' +
            '<td>New Password: </td>' +
            '<td><input type="password" name="password"></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Confirm Password:</td>' +
            '<td><input type="password" name="password_confirm"></td>' +
            '</tr>' +
            '<tr>' +
            '<td><button type="button" class="btn btn-md btn-primary" onclick=\"submit_new_password(\'<?php echo $_SESSION['username']?>\')\">Change Password</button></td>' +
            "</tr>" +
            "</table>" +
            "</form>";

            document.getElementById('display-frame').innerHTML = output + clear_button;
    }

    function submit_new_password(username) {

        confirm();
        var form = document.getElementById('password-form');
        var password = form.elements[0].value;
        var password_confirm = form.elements[1].value;

        if (password == password_confirm) {

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

            httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/update_password.php', true);
            httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var json_obj = new Object();
            json_obj.username = username;
            json_obj.password = password;

            var json_data = JSON.stringify(json_obj);

            httpRequest.send(json_data);

            httpRequest.onreadystatechange = function() {
                httpRequest.onreadystatechange = function() {
                    if (httpRequest.readyState == 4)
                    {
                        if (httpRequest.status == 200)
                        {
                            var output = httpRequest.responseText;
                            document.getElementById('display-frame').innerHTML = output + clear_button;
                        }
                    }
                }
            }
        } else {
            alert('Your passwords did not match, please try again.')
        }
    }

    function my_tickets(username) {
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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/get_tickets.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = new Object();
        json_obj.username = username;

        var json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {
                    var output = httpRequest.responseText;
                    document.getElementById('display-frame').innerHTML = output + clear_button;
                }
            }
        }
    }

    function ticket_form() {
        var output = "" +
            "<form method=\"post\" id=\"ticket-form\">" +
            "<h3>Submit a New Support Ticket</h3>" +
            '<br>' +
            '<table>' +
            '<tr>' +
            '<td>Subject: </td>' +
            '<td><input type="text" name="subject" placeholder=\"Enter a brief subject\" maxlength="50" required></td>' +
            '</tr>' +
            '<tr>' +
            '<td>Description:</td>' +
            '<td><textarea name="description" cols="60" rows="15" placeholder="Please enter a more detailed description of your problem. Be as specific as possible."></textarea></td>' +
            '</tr>' +
            '<tr>' +
            '<td><button type="button" class="btn btn-md btn-primary" onclick=\"submit_new_ticket(\'<?php echo $_SESSION['username']?>\')\">Submit Ticket</button></td>' +
            "</tr>" +
            "</table>" +
            "</form>";
        document.getElementById('display-frame').innerHTML = document.getElementById('display-frame').innerHTML = output + clear_button;
    }

    function submit_new_ticket(username) {

        var form = document.getElementById('ticket-form');

        var subject = form.elements[0].value;
        var description = form.elements[1].value;

        var subject_filled = subject != null || subject != "";
        var description_filled = description != null || description != "";

        var invalid_form = !subject_filled || !description_filled;


        if ( invalid_form ) {
            alert("You must fill out both a subject and description so that our technicians can address your support ticket effectively.");
            return;
        }


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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/submit_ticket.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = new Object();
        json_obj.username = username;
        json_obj.subject = subject;
        json_obj.description = description;

        var json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {
                    var output = httpRequest.responseText;
                    document.getElementById('display-frame').innerHTML = output + clear_button;
                }
            }
        }
    }

    function clear_frame() {
        document.getElementById('display-frame').innerHTML = "";
    }

</script>


