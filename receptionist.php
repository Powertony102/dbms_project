<?php
    // check if the user is logged in
    session_start();
    if(!isset($_SESSION['userID'])) {
        header("Location: login.html");
        exit();
    }

    // check if the user is admin, cleaner, or receptionist
    if($_SESSION['user_type'] == 'admin'){
        header("Location: admin.php");
    }
    else if($_SESSION['user_type'] == 'cleaner'){
        header("Location: cleaner.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>DBMS Project Team 6</title>
    <script>
        // deal with check-in
        function handleCheckIn() {
            var orderID = document.getElementById('orderID').value;

            if (orderID === "") {
                alert("Order ID cannot be empty.");
                return;
            }

            // create a FormData object to encapsulate data
            var formData = new FormData();
            formData.append('orderID', orderID);
            formData.append('action', 'checkin');

            // use Fetch API to submit form data to recept-check.php
            fetch('recept-check.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);  // show the information returned by the server
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // deal with check-out
        function handleCheckOut() {
            var orderID = document.getElementById('orderID').value;

            if (orderID === "") {
                alert("Order ID cannot be empty.");
                return;
            }

            // create a FormData object to encapsulate data
            var formData = new FormData();
            formData.append('orderID', orderID);
            formData.append('action', 'checkout');

            // use Fetch API to submit form data to recept-check.php
            fetch('recept-check.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())  // convert the response to text
            .then(data => {
                alert(data);  // show the information returned by the server
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</head>
<body>
    <!-- title -->
    <header class="header">
        <h1>Restaurant Reservation Management System</h1>
        <nav class="nav-bar">
            <ul>
                <!-- <li><a href="home.html">Home</a></li> -->
                <!-- <li><a href="credit.html">My Wallet</a></li> -->
                <!-- <li><a href="order.html">My Orders</a></li> -->
                <!-- <li><a href="settings.html">Settings</a></li> -->
                <li><a href="logout.php" style="color: red;">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- main -->
    <main class="main-content" style="max-width: 350px;">
        <section class="content-header">
            <h2>Receptionist Page</h2>
            <p>Manage check-ins and check-outs for guests.</p>
        </section>

        <section class="content-body">
            <form id="receptionForm">
                <label for="orderID">Order ID:</label>
                <input type="text" id="orderID" name="orderID" placeholder="Enter Order ID" required>
                
                <div class="buttons">
                    <button type="button" class="checkin-button" onclick="handleCheckIn()">Check-in</button>
                    <button type="button" class="checkout-button" onclick="handleCheckOut()">Check-out</button>
                </div>
            </form>

        </section>
    </main>

    <!-- footer -->
    <footer class="footer">
        <p>&copy; DBMS Project Team 6</p>
    </footer>
</body>

</html>
