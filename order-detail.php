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
    <title>DBMS Project Team 6</title>
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
            <h2>Order Information</h2>
            <p>Details of the order.</p>
        </section>

        <section class="content-body">
            <!-- <div class="order-details">
                <h3>Order #12345</h3>
                <p><strong>Table Type:</strong> Deluxe King Table</p>
                <p><strong>Check-in Date:</strong> 2024-11-20</p>
                <p><strong>Check-out Date:</strong> 2024-11-25</p>
                <p><strong>Total Price:</strong> $750.00</p>
                <p><strong>Status:</strong> Completed</p>
                <p><strong>Payment Method:</strong> Credit Card</p>
            </div> -->

            <?php
                //connect to the database
                include('connect_DB.php');

                // get the user's orders
                $userID = $_SESSION['userID'];
                $sql = "SELECT DISTINCT
                        Ord.oID AS OrderID,
                        Table_type.rtID AS TableTypeID,
                        Table_type.name AS TableTypeName,
                        `Table`.rID AS TableID,
                        Ord.order_time AS OrderDate,
                        Ord.price AS Price,
                        Ord.check_in_status AS OrderStatus,
                        Comment.comment AS Comment
                        FROM 
                            User
                        JOIN 
                            Customer_Order ON User.uID = customer_order.customer_id
                        JOIN 
                            Ord ON customer_order.oID = Ord.oID
                        JOIN 
                            Order_Table ON Ord.oID = Order_Table.oID
                        JOIN 
                            `Table` ON Order_Table.rID = `Table`.rID
                        JOIN 
                            Table_Table_type ON `Table`.rID = Table_Table_type.rID
                        JOIN 
                            Table_type ON Table_Table_type.rtID = Table_type.rtID
                        LEFT JOIN 
                            Order_Comment ON Ord.oID = Order_Comment.oID
                        LEFT JOIN 
                            Comment ON Order_Comment.cID = Comment.cID
                        WHERE 
                            User.uID = $userID AND Ord.oID = ".$_GET['orderID'].";";
;
                $result = $conn->query($sql);
                
                // check if the query was successful and has results
                if (!$result || $result->num_rows == 0) {
                    echo "<p>Error: Order not found or you don't have permission to view this order.</p>";
                    echo "<p>Order ID: ".$_GET['orderID']." - User ID: ".$userID."</p>";
                    echo "<p><a href='order.php'>Go back to your orders.</a></p>"; // Add a link to go back
                    exit();
                }                

                // display the user's orders
                $row = $result->fetch_assoc();
                echo "<div class='order-details'>";
                echo "<h3>Order #".$row['OrderID']."</h3>";
                echo "<p><strong>Table Type:</strong> ".$row['TableTypeName']."</p>";
                echo "<p><strong>Date:</strong> ".$row['OrderDate']."</p>";
                echo "<p><strong>Total Price:</strong> $".$row['Price']."</p>";
                echo "<p><strong>Table ID:</strong> ".$row['TableID']."</p>";
                echo "<p><strong>Status:</strong> ".$row['OrderStatus']."</p>";
                echo "<p><strong>Comment:</strong> ".$row['Comment']."</p>";
                echo "</div>";

            ?>
        
            <div class="review-section">
                <h3>Leave a Review</h3>
                <form action="submit-review.php" method="post">
                    <label for="review">Your Review:</label>
                    <textarea id="review" name="review" rows="5" placeholder="Write your experience here..." required></textarea>
                    <!-- pass the oID through POST -->
                    <input type="hidden" name="oID" value="<?php echo $_GET['orderID']; ?>">
                    <button type="submit">Submit Review</button>
                </form>
            </div>
        </section>
    </main>

    <!-- footer -->
    <footer class="footer">
        <p>&copy; DBMS Project Team 6</p>
    </footer>
</body>

</html>
