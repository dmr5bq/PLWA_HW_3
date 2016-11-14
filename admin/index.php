<?php

session_start();

if (isset($_SESSION['username']))
    $username = $_SESSION['username'];
else {
    header('location: http://localhost/PLWA_HW_3/');
}

?>
<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="../style/master.css">
    </head>
    <body onload="show_open_tickets()">
        <h1 class="dark-header">Welcome, <span class='highlighted-username'><?php echo $_SESSION['username']; ?></span></h1>
        <table>
            <tr>
                <td>
                    <a href="../index.php">
                        <button type="button" class="btn btn-md btn-danger">
                            <span class="glyphicon glyphicon-remove-sign"></span> Log Out
                        </button>
                        <hr>
                    </a>
                </td>
            </tr>
            <tr id="back-button-frame">

            </tr>
            <tr id="button-frame">
                <!-- Will fill with appropriate buttons -->
            </tr>
            <tr id="button-frame-row-two">
                <!-- Will fill with appropriate buttons -->
            </tr>
        </table>

        <hr>

        <div id="display-frame">
            <!-- Will fill with AJAX output -->
        </div>
    </body>
</html>

<script type="text/javascript">

    /* Variables global to page */

    var state = {};

    state.ticket_id = 0;
    state.submitter_username = "";
    state.selected_tickets = null;

    var clear_button = "<button class=\"btn btn-sm btn-warning\" onclick=\"clear_frame()\"><span class=\"glyphicon glyphicon-remove\"></span></button>";

    var buttons_many_row_one = "" +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick="show_all_tickets()"><span class="glyphicon glyphicon-plus"></span> View All Tickets</button>' +
        '</td>' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick="show_my_tickets(\'<?php echo $_SESSION['username'] ?>\')"><span class="glyphicon glyphicon-user"></span> View My Tickets</button>' +
        '</td>' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick=\"show_unassigned_tickets()\"><span class="glyphicon glyphicon-star-empty"></span> View Unassigned Tickets</button>' +
        '</td>' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick=\"show_open_tickets()\"><span class="glyphicon glyphicon-eye-open"></span> Show Open Tickets</button>' +
        '</td>' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick=\"show_closed_tickets()\"><span class="glyphicon glyphicon-eye-close"></span> Show Closed Tickets</button>' +
        '</td>' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick="local_sort()"><span class="glyphicon glyphicon-random"></span> Sort By Field </span></button>' +
        '</td>' ;

    var buttons_many_row_two = '' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-primary" onclick="show_selected_ticket()"><span class="glyphicon glyphicon-expand"></span> View Ticket <span class="glyphicon glyphicon-chevron-right"></span></button>' +
        '</td>';

    var home_button = "" +
        '<td>' +
        '<button type="button" class="btn btn-md btn-default btn-info" onclick="show_open_tickets()"><span class="glyphicon glyphicon-chevron-left"></span> Back to Tickets </button>' +
        '</td>';

    var delete_button = '<td>' +
        '<button type="button" class="btn btn-md btn-danger" onclick="delete_ticket()"><span class="glyphicon glyphicon-remove-circle"></span> Delete This Ticket</button>' +
        '</td>';

    var back_button = home_button + delete_button;

    var buttons_single_row_one =   ""   +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick="open_ticket()"><span class="glyphicon glyphicon-eye-open"></span> Open This Ticket </button>' +
        '</td>' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick="close_ticket()"><span class="glyphicon glyphicon-eye-close"></span> Close This Ticket</button>' +
        '</td>' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick="assign_self_to_ticket(\'<?php echo $username; ?>\')"><span class="glyphicon glyphicon-log-in"></span> Assign Me to Ticket</button>' +
        '</td>' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-success" onclick="remove_self_from_ticket(\'<?php echo $username; ?>\')"><span class="glyphicon glyphicon-log-out"></span> Remove Me from Ticket</button>' +
        '</td>';


    var buttons_single_row_two = "" +
        '<td>' +
            '<button type="button" class="btn btn-md btn-primary" onclick="show_similar_tickets()"><span class="glyphicon glyphicon-search"></span> Similar Tickets <span class="glyphicon glyphicon-chevron-right"></span></button>' +
        '</td>' +
        '<td>' +
            '<button type="button" class="btn btn-md btn-primary" onclick="show_user_tickets()"><span class="glyphicon glyphicon-user"></span> Tickets from User <span class="glyphicon glyphicon-chevron-right"></button>' +
        '</td>';
;

    /* Function definitions */

    function show_user_tickets() {

        var username = state.submitter_username;

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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/get_user_tickets.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = {};

        json_obj.username = username;

        json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4) {
                if (httpRequest.status == 200) {

                    var output = httpRequest.responseText;

                    state.selected_tickets = output;

                    document.getElementById('display-frame').innerHTML = "<h4>Tickets from " + username + "</h4>"
                                                + display_tickets(output) + clear_button;

                    document.getElementById('back-button-frame').innerHTML = "";
                    document.getElementById('button-frame').innerHTML = buttons_many_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_many_row_two;
                }
            }
        }


    }

    function show_similar_tickets() {

        var ticket_id = state.ticket_id;

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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/get_similar_tickets.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = {};

        json_obj.ticket_id = ticket_id;

        json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4) {
                if (httpRequest.status == 200) {
                    var output = httpRequest.responseText;

                    state.selected_tickets = output;

                    document.getElementById('display-frame').innerHTML = "<h4>Tickets Assigned to <?php echo $_SESSION['username'] ?></h4>" + display_tickets(output) + clear_button;

                    document.getElementById('back-button-frame').innerHTML = "";
                    document.getElementById('button-frame').innerHTML = buttons_many_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_many_row_two;
                }
            }
        }
    }

    function delete_ticket() {

         var ticket_id = state.ticket_id;

        confirm("Are you sure that you want to delete this ticket?");

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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/delete_ticket.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = {};

        json_obj.ticket_id = ticket_id;

        json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4) {
                if (httpRequest.status == 200) {
                    show_open_tickets();
                }
            }
        }

    }

    function assign_self_to_ticket(username) {

        var ticket_id = state.ticket_id;


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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/assign_ticket.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = {};

        json_obj.ticket_id = ticket_id;
        json_obj.username = username;

        json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4) {
                if (httpRequest.status == 200) {

                    var output = httpRequest.responseText;

                    state.ticket_id = ticket_id;

                    document.getElementById('display-frame').innerHTML = "<h4>View Ticket (ID: " + ticket_id + ")</h4>"
                        + display_ticket(output);

                    document.getElementById('back-button-frame').innerHTML = back_button;
                    document.getElementById('button-frame').innerHTML = buttons_single_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_single_row_two;
                }
            }
        }

    }

    function remove_self_from_ticket(username) {

        var ticket_id = state.ticket_id;


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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/unassign_ticket.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = {};

        json_obj.ticket_id = ticket_id;
        json_obj.username = username;

        json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4) {
                if (httpRequest.status == 200) {

                    var output = httpRequest.responseText;

                    state.ticket_id = ticket_id;

                    document.getElementById('display-frame').innerHTML = "<h4>View Ticket (ID: " + ticket_id + ")</h4>"
                        + display_ticket(output);

                    document.getElementById('back-button-frame').innerHTML = back_button;
                    document.getElementById('button-frame').innerHTML = buttons_single_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_single_row_two;
                }
            }
        }
    }

    function open_ticket() {

        var ticket_id = state.ticket_id;


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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/open_ticket.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = {};

        json_obj.ticket_id = ticket_id;

        json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4) {
                if (httpRequest.status == 200) {

                    var output = httpRequest.responseText;

                    state.ticket_id = ticket_id;

                    document.getElementById('display-frame').innerHTML = "<h4>View Ticket (ID: " + ticket_id + ")</h4>"
                        + display_ticket(output);

                    document.getElementById('back-button-frame').innerHTML = back_button;
                    document.getElementById('button-frame').innerHTML = buttons_single_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_single_row_two;
                }
            }
        }
    }

    function close_ticket() {

        var ticket_id = state.ticket_id;


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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/close_ticket.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = {};

        json_obj.ticket_id = ticket_id;

        json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {

                    var output = httpRequest.responseText;

                    state.ticket_id = ticket_id;

                    document.getElementById('display-frame').innerHTML =  "<h4>View Ticket (ID: " + ticket_id + ")</h4>"
                        + display_ticket(output);

                    document.getElementById('back-button-frame').innerHTML = back_button;
                    document.getElementById('button-frame').innerHTML = buttons_single_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_single_row_two;
                }
            }
        }

    }


    function show_selected_ticket() {

        if ( document.querySelector('input[name="select-ticket"]:checked') == undefined ) {
            alert('Select a ticket using the buttons on the right to view it.');
            return;
        }

        var ticket_id = document.querySelector('input[name="select-ticket"]:checked').value;

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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/get_ticket_by_id.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = {};

        json_obj.ticket_id = ticket_id;

        json_data = JSON.stringify(json_obj);

        httpRequest.send(json_data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {

                    var output = httpRequest.responseText;

                    var selected_ticket = JSON.parse(output)[0];

                    var ticket_id = selected_ticket['id'];
                    var username = selected_ticket['username'];

                    state.ticket_id = ticket_id;
                    state.submitter_username = username;

                    document.getElementById('display-frame').innerHTML =  "<h4>View Ticket (ID: " + ticket_id + ")</h4>"
                                                                            + display_ticket(output);

                    document.getElementById('back-button-frame').innerHTML = back_button;
                    document.getElementById('button-frame').innerHTML = buttons_single_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_single_row_two;
                }
            }
        }
    }

    /* For sorting functions
    * ***********************
    * -> Attributes to sort by
    * id
    * username
    * first
    * last
    * email
    * subject
    * assigned (DB: tech_username)
    * timestamp
    * */


    function local_sort() {
        if (document.querySelector('input[name="sort-by"]:checked') == null || document.querySelector('input[name="sort-by"]:checked') == undefined) {
            alert('You must select an attribute below to sort.');
            return;
        }

        sort_tickets(state.selected_tickets);
    }

    function sort_tickets(tickets_json) {
        var attr = document.querySelector('input[name="sort-by"]:checked').value;

        var comparison_function = get_comparator(attr);

        var tickets = JSON.parse(tickets_json);

        tickets = tickets.sort(comparison_function);

        var output = JSON.stringify(tickets);


        var attr_name = get_attr_name(attr);

        document.getElementById('display-frame').innerHTML =  "<h4>Tickets Sorted By "+ attr_name +"</h4>"
            + display_tickets(output);

    }

    function get_attr_name(attr) {

        var attr_name = '';
        switch (attr) {
            case 'id':
                attr_name = "Ticket ID";
                break;
            case 'username':
                attr_name = "Submitter Username";
                break;
            case 'first':
                attr_name = "Submitter First Name";
                break;
            case 'last':
                attr_name = "Submitter Last Name";
                break;
            case 'email':
                attr_name = "Submitter Email Address";
                break;
            case 'subject':
                attr_name = "Ticket Subject";
                break;
            case 'assigned':
                attr_name = "Assigned Technician Username";
                break;
            case 'timestamp':
                attr_name = "Date Submitted";
                break;
        }

        return attr_name;
    }

    function get_comparator(attr) {

        switch(attr) {
            case 'id':
                return comparator_id;
                break;
            case 'username':
                return comparator_username;
                break;
            case 'first':
                return comparator_first;
                break;
            case 'last':
                return comparator_last;
                break;
            case 'email':
                return comparator_email;
                break;
            case 'subject':
                return comparator_subject;
                break;
            case 'description':
                return comparator_description;
                break;
            case 'assigned':
                return comparator_assigned;
                break;
            case 'timestamp':
                return comparator_timestamp;
                break;
            default:
                return comparator_id;
                break;
        }
    }

    function comparator_id(ticket_1, ticket_2) {

        var attr_1 = ticket_1['id'];
        var attr_2 = ticket_2['id'];

        if (attr_1 < attr_2) return -1;
        if (attr_1 > attr_2) return 1;
        return 0;
    }

    function comparator_username(ticket_1, ticket_2) {

        var attr_1 = ticket_1['username'];
        var attr_2 = ticket_2['username'];

        if (attr_1 < attr_2) return -1;
        if (attr_1 > attr_2) return 1;
        return 0;
    }

    function comparator_first(ticket_1, ticket_2) {

        var attr_1 = ticket_1['first'];
        var attr_2 = ticket_2['first'];

        if (attr_1 < attr_2) return -1;
        if (attr_1 > attr_2) return 1;
        return 0;
    }

    function comparator_last(ticket_1, ticket_2) {

        var attr_1 = ticket_1['last'];
        var attr_2 = ticket_2['last'];

        if (attr_1 < attr_2) return -1;
        if (attr_1 > attr_2) return 1;
        return 0;
    }

    function comparator_email(ticket_1, ticket_2) {

        var attr_1 = ticket_1['email'];
        var attr_2 = ticket_2['email'];

        if (attr_1 < attr_2) return -1;
        if (attr_1 > attr_2) return 1;
        return 0;
    }

    function comparator_subject(ticket_1, ticket_2) {

        var attr_1 = ticket_1['subject'];
        var attr_2 = ticket_2['subject'];

        if (attr_1 < attr_2) return -1;
        if (attr_1 > attr_2) return 1;
        return 0;
    }


    function comparator_description(ticket_1, ticket_2) {

        var attr_1 = ticket_1['description'];
        var attr_2 = ticket_2['description'];

        if (attr_1 < attr_2) return -1;
        if (attr_1 > attr_2) return 1;
        return 0;
    }

    function comparator_assigned(ticket_1, ticket_2) {

        var attr_1 = ticket_1['assigned'];
        var attr_2 = ticket_2['assigned'];

        if (attr_1 < attr_2) return -1;
        if (attr_1 > attr_2) return 1;
        return 0;
    }


    function comparator_timestamp(ticket_1, ticket_2) {

        var attr_1 = ticket_1['timestamp'];
        var attr_2 = ticket_2['timestamp'];

        if (attr_1 < attr_2) return -1;
        if (attr_1 > attr_2) return 1;
        return 0;
    }


    function show_all_tickets() {
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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/get_all_tickets.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');


        httpRequest.send(null);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {
                    var output = httpRequest.responseText;

                    state.selected_tickets = output;

                    document.getElementById('display-frame').innerHTML = "<h4> All Tickets </h4>" + display_tickets(output) + clear_button;

                    document.getElementById('back-button-frame').innerHTML = "";
                    document.getElementById('button-frame').innerHTML = buttons_many_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_many_row_two;
                }
            }
        }
    }

    function show_closed_tickets() {
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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/get_closed_tickets.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');


        httpRequest.send(null);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {
                    var output = httpRequest.responseText;

                    state.selected_tickets = output;

                    document.getElementById('display-frame').innerHTML = "<h4>Closed Tickets</h4>" + display_tickets(output) + clear_button;

                    document.getElementById('back-button-frame').innerHTML = "";
                    document.getElementById('button-frame').innerHTML = buttons_many_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_many_row_two;
                }
            }
        }
    }

    function show_open_tickets() {
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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/get_open_tickets.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');


        httpRequest.send(null);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {
                    var output = httpRequest.responseText;

                    state.selected_tickets = output;

                    document.getElementById('display-frame').innerHTML = "<h4>Open Tickets</h4>" + display_tickets(output) + clear_button;

                    document.getElementById('back-button-frame').innerHTML = "";
                    document.getElementById('button-frame').innerHTML = buttons_many_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_many_row_two;
                }
            }
        }
    }

    function show_my_tickets(username) {
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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/get_my_tickets.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        var json_obj = new Object();
        json_obj.username = username;

        var data = JSON.stringify(json_obj);

        httpRequest.send(data);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {
                    var output = httpRequest.responseText;

                    state.selected_tickets = output;

                    document.getElementById('display-frame').innerHTML = "<h4>Tickets Assigned to <?php echo $_SESSION['username'] ?></h4>" + display_tickets(output) + clear_button;

                    document.getElementById('back-button-frame').innerHTML = "";
                    document.getElementById('button-frame').innerHTML = buttons_many_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_many_row_two;
                }
            }
        }
    }

    function show_unassigned_tickets() {
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

        httpRequest.open('POST', 'http://localhost/PLWA_HW_3/modules/php/get_unassigned_tickets.php', true);
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');


        httpRequest.send(null);

        httpRequest.onreadystatechange = function() {
            if (httpRequest.readyState == 4)
            {
                if (httpRequest.status == 200)
                {
                    var output = httpRequest.responseText;

                    state.selected_tickets = output;

                    document.getElementById('display-frame').innerHTML = "<h4>Unassigned Tickets</h4>" + display_tickets(output) + clear_button;

                    document.getElementById('back-button-frame').innerHTML = "";
                    document.getElementById('button-frame').innerHTML = buttons_many_row_one;
                    document.getElementById('button-frame-row-two').innerHTML = buttons_many_row_two;
                }
            }
        }
    }

    function display_ticket(ticket_json) {

        var ticket = JSON.parse(ticket_json);

        var len = ticket.length;

        var ret_html = '';


        if (len > 0) {
            ret_html += "<table class='padded-table table-colored'>";
            ret_html += generate_ticket_table_header();

            for (var i = 0; i < len; i++) {
                ret_html += "<tr>";

                var description = ticket[i]['description'];

                if (description.length > 50) {
                    description = description.substr(0,50) + "...";
                }

                var open_display = "<span class=\"glyphicon glyphicon-ok\" style='font-size:30px; color: #41a83e;'></span>";

                if (ticket[i]['open'] == 0) {
                    open_display = "<span class=\"glyphicon glyphicon-remove\" style='font-size:30px; color: #ff0000;'></span>";
                }

                ret_html += "<td>" + ticket[i]['id'] + '</td>';
                ret_html += "<td>" + ticket[i]['username'] + '</td>';
                ret_html += "<td>" + ticket[i]['first'] + '</td>';
                ret_html += "<td>" + ticket[i]['last'] + '</td>';
                ret_html += "<td>" + ticket[i]['email'] + '</td>';
                ret_html += "<td>" + ticket[i]['subject'] + '</td>';
                ret_html += "<td>" + description + '</td>';
                ret_html += "<td>" + ticket[i]['tech_username'] + '</td>';
                ret_html += "<td>" + open_display + '</td>';
                ret_html += "<td>" + ticket[i]['timestamp'] + '</td>';

                ret_html += "</tr>";
            }

            ret_html += '</table>';

        } else {
            ret_html = "<p>No tickets were found.</p>";
        }

        return ret_html;
    }

    function display_tickets(tickets_json) {

        var tickets = JSON.parse(tickets_json);

        var len = tickets.length;

        var ret_html = '';


        if (len > 0) {
            ret_html += "<table class='padded-table table-colored'>";
            ret_html += generate_ticket_table_header();
            ret_html += generate_sort_radio_row();

            for (var i = 0; i < len; i++) {
                ret_html += "<tr>";

                var description = tickets[i]['description'];

                if (description.length > 50) {
                    description = description.substr(0,50) + "...";
                }

                var open_display = "<span class=\"glyphicon glyphicon-ok\" style='font-size:30px; color: #41a83e;'></span>";

                if (tickets[i]['open'] == 0) {
                   open_display = "<span class=\"glyphicon glyphicon-remove\" style='font-size:30px; color: #ff0000;'></span>";
                }

                ret_html += "<td>" + tickets[i]['id'] + '</td>';
                ret_html += "<td>" + tickets[i]['username'] + '</td>';
                ret_html += "<td>" + tickets[i]['first'] + '</td>';
                ret_html += "<td>" + tickets[i]['last'] + '</td>';
                ret_html += "<td>" + tickets[i]['email'] + '</td>';
                ret_html += "<td>" + tickets[i]['subject'] + '</td>';
                ret_html += "<td>" + description + '</td>';
                ret_html += "<td>" + tickets[i]['tech_username'] + '</td>';
                ret_html += "<td>" + open_display + '</td>';
                ret_html += "<td>" + tickets[i]['timestamp'] + '</td>';
                ret_html += "<td class='select-column'>" + generate_radio_select(tickets[i]['id']) + '</td>';

                ret_html += "</tr>";
            }

            ret_html += '</table>';

        } else {
            ret_html = "<p>No tickets were found.</p>";
        }

        return ret_html;
    }


    function generate_ticket_table_header() {
        return '<tr class=\"ticket-table-header\" >' +
                    '<td>ID</td>' +
                    '<td>Username</td>' +
                    '<td>First</td>' +
                    '<td>Last</td>' +
                    '<td>Email</td>' +
                    '<td>Subject</td>' +
                    '<td>Description</td>' +
                    '<td>Assigned</td>' +
                    '<td>Open</td>' +
                    '<td>Timestamp</td>' +
                    '<td></td>'+
                '</tr>';
    }

    function generate_sort_radio_row() {
        return  "" +
            "<form id='sort-form'>" +
                    "<tr class=\"ticket-table-header\">" +
                        "<td>" + generate_sort_radio_button('id') + "</td>" +
                        "<td>" + generate_sort_radio_button('username') + "</td>" +
                        "<td>" + generate_sort_radio_button('first') + "</td>" +
                        "<td>" + generate_sort_radio_button('last') + "</td>" +
                        "<td>" + generate_sort_radio_button('email') + "</td>" +
                        "<td>" + generate_sort_radio_button('subject') + "</td>" +
                        "<td><span class=\"glyphicon glyphicon-minus\"></span></td>" +
                        "<td>" + generate_sort_radio_button('assigned') + "</td>" +
                        "<td><span class=\"glyphicon glyphicon-minus\"></span></td>" +
                        "<td>" + generate_sort_radio_button('timestamp') + "</td>" +
                        "<td></td>" +
                "</tr>" +
            "</form>";
    }



    function generate_sort_radio_button(attr) {
        return "<input type=\'radio\' name=\'sort-by\' id=\'" + attr + "\' value=\'" + attr + "\'>";
    }

    function generate_radio_select(ticket_id) {
        return "<input type='radio' name='select-ticket' value='"+ticket_id+"'>";
    }

    function clear_frame() {
        document.getElementById('button-frame-row-two').innerHTML = "";
        document.getElementById('display-frame').innerHTML = "";
    }

</script>