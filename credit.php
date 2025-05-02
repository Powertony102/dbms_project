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
    else if($_SESSION['user_type'] == 'receptionist'){
        header("Location: receptionist.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>DBMS Project Group 6</title>
</head>
<body>
    <!-- title -->
    <header class="header">
        <h1>Restaurant Reservation Management System</h1>
        <nav class="nav-bar">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="credit.php">My Wallet</a></li>
                <li><a href="order.php">My Orders</a></li>
                <!-- <li><a href="settings.html">Settings</a></li> -->
                <li><a href="logout.php" style="color: red;">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- main -->
    <main class="main-content" style="max-width: 550px;">
        <section class="content-header">
            <h2>My Wallet</h2>
            <p>You can check your balance or recharge here</p>
        </section>

        <section class="content-body">
            <div class="card">
                <h3>Current Balance</h3>
                <p>Your current wallet balance is:</p>
                <div class="balance-display">
                    <?php
                        //connect to the database
                        include('connect_DB.php');

                        // get the user's balance
                        $userID = $_SESSION['userID'];
                        $sql = "SELECT balance FROM User WHERE uID = '$userID';";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo "<span>￥" . $row['balance'] . "</span>";
                    ?>
                </div>
            </div>
        
            <div class="card">
                <h3>Recharge Your Wallet</h3>
                <p>Add funds to your wallet.</p>
                <form action="charge-check.php" method="post">
                    <label for="recharge-amount">Enter Amount:</label>
                    <input type="number" id="recharge-amount" name="amount" placeholder="e.g., 50.00" min="1" step="0.01" required>
                    <br><button type="submit">Recharge Now</button>
                </form>
            </div>

            
        </section>
    </main>

    <!-- footer -->
    <footer class="footer">
        <p>&copy; DBMS Project Group 6</p>
    </footer>
</body>
</html>