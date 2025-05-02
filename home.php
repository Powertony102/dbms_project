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
    <main class="main-content">
        <section class="content-header">
            <h2>Available Tables</h2>
            <p>We have the following tables for you.</p>
        </section>

        <section class="content-body">
            <?php
                //connect to the database
                include('connect_DB.php');

                // get the available Tables
                $sql = "SELECT * FROM Table_type WHERE remain > 0;";
                $result = $conn->query($sql);

                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                        echo "<div class='card'>";
                        echo "<h3>".$row['name']."</h3>";
                        echo "<p>".$row['introduction']."</p>";
                        echo "<button onclick=\"window.location.href='book.php?rtID=". $row['rtID'] ."'\">Learn More</button>";
                        echo "</div>";
                    }
                }else{
                    echo "<p>No available Tables</p>";
                }

            ?>    
        </section>
    </main>

    <!-- footer -->
    <footer class="footer">
        <p>&copy; DBMS Project Group 6</p>
    </footer>
</body>
</html>
