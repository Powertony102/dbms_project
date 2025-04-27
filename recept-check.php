<?php

session_start();



// check if the user is logged in

if (!isset($_SESSION['userID'])) {

    // user is not logged in

    header("Location: login.html");

    exit();

}



// check if the user is admin, cleaner, or receptionist





//connect to the database

include('connect_DB.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // get the POST data

    $oID = $_POST['orderID'];

    $action = $_POST['action'];



    if (empty($oID) || empty($action)) {

        echo "Error: Missing required fields.";

        exit;

    }



    // check in or check out by checking action

    if ($action === 'checkin') {

        // check in

        // update the order status to occupying

        $query = "UPDATE Ord

        SET 

        check_in_status = 'occupying', 

        order_time = NOW(),

        price = (SELECT price FROM `Table` WHERE rID = (SELECT rID FROM Order_Table WHERE oID = ? LIMIT 1))

        WHERE oID = ?";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("ss", $oID, $oID);

        if ($stmt->execute()) {

            echo "Check-in successful.";

        } else {

            echo "Error during check-in.";

        }

    } elseif ($action === 'checkout') {

        // check out

        // update the order status to complete

        $query = "UPDATE Ord

        SET 

        check_in_status = 'completed', 

        order_time = NOW(),

        price = (SELECT price FROM `Table` WHERE rID = (SELECT rID FROM Order_Table WHERE oID = ? LIMIT 1))

        WHERE oID = ?";



        $stmt = $conn->prepare($query);

        $stmt->bind_param("ss", $oID, $oID);


        // update the Table status to clean

        $query = "UPDATE `Table`

        SET clean_status = 'dirty'

        WHERE rID = (SELECT rID FROM Order_Table WHERE oID = ? LIMIT 1)";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("s", $oID);

        if ($stmt->execute()) {

            echo "Check-out successful.";

        } else {

            echo "Error during check-out.";

        }

    } else {

        echo "Invalid action.";

    }

}



$conn->close();

?>

