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

    <?php
        //connect to the database
        include('connect_DB.php');

        // get the get parameter ttID
        $ttID = $_GET['ttID'];

        // get the Table type information
        $sql = "SELECT * FROM Table_type WHERE ttID = '$ttID';";
        $result = $conn->query($sql);
        // check if the Table type exists
        if($result->num_rows == 0){
            echo "<p>Table type not found</p>";
            exit();
        }
        $row = $result->fetch_assoc();
    ?>

    <!-- main -->
    <main class="main-content">
        <section class="content-header">
            <h2><?php echo $row['name']; ?></h2>
            <p>Table Information.</p>
        </section>

        <section class="content-body">
             <!-- Table detail -->
        <div class="Table-details">
            <h3><?php echo $row['name']; ?></h3>
            <div class="Table-image">
                <!-- <img src="Restaurant Reservation.png" alt="<?php echo $row['name']; ?>" /> -->
                <?php
                    // load image from file path
                    echo '<img src="' . $row['img'] . '" alt="' . $row['name'] . '" />';
                ?>
            </div>
            <p><strong>Description:</strong><?php echo $row['introduction'] ?></p>
            <p><strong>Price:</strong> <?php echo $row['price']; ?></p>
            <p><strong>Available Tables:</strong> <?php echo $row['remain']; ?></p>
            <?php
                // check if the user has enough balance to reserve this Table
                $userID = $_SESSION['userID'];
                $sql = "SELECT balance FROM User WHERE uID = '$userID';";
                $result = $conn->query($sql);
                $row2 = $result->fetch_assoc();
                if($row2['balance'] < $row['price']){
                    echo "<p style='color: red;'>You don't have enough balance to reserve this Table</p>";
                }
                else{
                    echo "<button class=\"reserve-button\" onclick=\"window.location.href='book-check.php?ttID=$ttID'\">Reserve This Table</button>";
                }
            ?>
        </div>

        <!-- Table comment -->
        <div class="Table-reviews">
            <h3>Customer Reviews</h3>
            <?php
                // get the comments for this Table type
                $sql = "SELECT cID, User.name, comment
                        FROM Table_type
                        JOIN Table_Table_type USING (ttID)
                        JOIN Order_Table USING (tID)
                        JOIN Customer_Order Using (oID)
                        JOIN User ON (User.uID = Customer_Order.customer_id)
                        JOIN Order_Comment USING (oID)
                        JOIN Comment USING (cID)
                        WHERE ttID = $ttID
                        ORDER BY cID DESC LIMIT 20;
                        ";
                $result = $conn->query($sql);
                if (!$result) {
                    // If the query failed
                    echo "<p>Database query failed: " . $conn->error . "</p>";
                    exit();
                }
                if($result->num_rows > 0){
                    
                    while($row = $result->fetch_assoc()){
                        echo "<div class='review'>";
                        echo "<p><strong>".$row['name'].":</strong> \"".$row['comment']."\"</p>";
                        echo "</div>";
                    }
                }
                else{
                    echo "<p>No comments yet</p>";
                }
            ?>
        </div> 
        </section>
    </main>
    <!-- footer -->
    <footer class="footer">
        <p>&copy; DBMS Project Group 6</p>
    </footer>
</body>

</html>
