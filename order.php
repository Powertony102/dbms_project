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
            <h2>My Orders</h2>
            <p>My History Orders.</p>
        </section>

        <section class="content-body">

            <?php
                //connect to the database
                include('connect_DB.php');

                // get the user's orders
                $userID = $_SESSION['userID'];
                $sql = "SELECT DISTINCT
                        ord.oID AS OrderID,
                        Table_type.ttID AS TableTypeID,
                        Table_type.name AS TableTypeName,
                        ord.order_time AS OrderDate,
                        ord.price AS Price,
                        ord.check_in_status AS OrderStatus,
                        Comment.comment AS Comment
                        FROM 
                            User
                        JOIN 
                            Customer_Order ON User.uID = Customer_Order.customer_id
                        JOIN 
                            Ord ON Customer_Order.oID = Ord.oID
                        JOIN 
                            Order_Table ON ord.oID = Order_Table.oID
                        JOIN 
                            `Table` ON Order_Table.tID = `Table`.tID
                        JOIN 
                            Table_Table_type ON `Table`.tID = Table_Table_type.tID
                        JOIN 
                            Table_type ON Table_Table_type.ttID = Table_type.ttID
                        LEFT JOIN 
                            Order_Comment ON Ord.oID = Order_Comment.oID
                        LEFT JOIN 
                            Comment ON Order_Comment.cID = Comment.cID
                        WHERE 
                            User.uID = $userID
                        ORDER BY OrderDate DESC;
                ";
                $result = $conn->query($sql);
                // check if the user has any orders
                if($result->num_rows == 0){
                    echo "<p>You have no orders</p>";
                    exit();
                }

                // display the user's orders
                while($row = $result->fetch_assoc()){
                    echo "<div class='card'>";
                    echo "<h3>Order #".$row['OrderID']."</h3>";
                    echo "<p><strong>Table Type:</strong> ".$row['TableTypeName']."</p>";
                    echo "<p><strong>Date:</strong> ".$row['OrderDate']."</p>";
                    echo "<p><strong>Total Price:</strong> $".$row['Price']."</p>";
                    echo "<button onclick=\"window.location.href='order-detail.php?orderID=".$row['OrderID']."'\">View Details</button>";
                    echo "</div>";
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
