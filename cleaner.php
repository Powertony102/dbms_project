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
else if($_SESSION['user_type'] == 'receptionist'){
    header("Location: receptionist.php");
}



//connect to the database

include('connect_DB.php');



//find the dirty Tables which need cleaning

$sql1 = "SELECT * FROM `Table` WHERE clean_status = 'dirty'";

$result1 = $conn->query($sql1);



$Tables_dirty = [];



if($result1->num_rows > 0){

    //put result1 into the array

    while ($row = $result1->fetch_assoc()){

        $Tables_dirty[] = $row; 

    }

}



//check if rID is submitted through POST

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rID'])) {

    $rID = $_POST['rID'];



    //update the Table status to 'clean'

    $sql2 = "UPDATE Table

    SET clean_status = 'clean'

    WHERE rID = '$rID'";



    if ($conn->query($sql2) === TRUE) {

        // update success, back to original web

        header("Location: cleaner.php");

        exit;

    } else {

        echo "Error: " . $sql2 . "<br>" . $conn->error;

    }

}



//end the database connection

$conn->close();

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

                <!-- <li><a href="home.html">Home</a></li> -->

                <!-- <li><a href="credit.html">My Wallet</a></li> -->

                <!-- <li><a href="order.html">My Orders</a></li> -->

                <!-- <li><a href="settings.html">Settings</a></li> -->

                <li><a href="logout.php" style="color: red;">Logout</a></li>

            </ul>

        </nav>

    </header>



    <!-- main -->

    <main class="main-content">

        <section class="content-header">

            <h2>Uncleaned Tables</h2>

            <p>Tables needed to clean.</p>

        </section>



        <section class="content-body">

            <ul>

                <?php if (count($Tables_dirty) > 0): ?>

                    <?php foreach ($Tables_dirty as $Table): ?>

                        <div class="Table-item">

                            <h4>Table #<?php echo htmlspecialchars($Table['rID']); ?></h4>

                            <p><strong>Status:</strong> <?php echo htmlspecialchars($Table['clean_status']); ?></p >

                            <form action="cleaner.php" method="POST">

                                <input type="hidden" name="rID" value="<?php echo $Table['rID']; ?>"><br>

                                <button type="submit" class="assign-button" onclick="alert('Table #<?php echo htmlspecialchars($Table['rID']); ?> cleaning assigned!')">Mark as Cleaning</button>

                            </form>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <li>No Tables need cleaning at the moment.</li>

                <?php endif; ?>

            </ul>

        </section>

    </main>



    <!-- footer -->

    <footer class="footer">

        <p>&copy; DBMS Project Team 6</p>

    </footer>

</body>



</html>

