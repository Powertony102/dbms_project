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
        exit();
    }
    else if($_SESSION['user_type'] == 'cleaner'){
        header("Location: cleaner.php");
        exit();
    }
    else if($_SESSION['user_type'] == 'receptionist'){
        header("Location: receptionist.php");
        exit();
    }

    // get the get parameter ttID
    $ttID = $_GET['ttID'];

    // connect to the database
    include('connect_DB.php');

    // get the user id
    $userID = $_SESSION['userID'];

    // get the Table type information
    $sql = "SELECT * FROM Table_type WHERE ttID = $ttID;";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    // add the booking to the database
    $sql = "INSERT INTO Ord (oID, check_in_status, order_time, price) VALUES (NULL, 'pending', NOW(), " . $row['price'] . ");";
    $conn->query($sql);

    // get the order id
    $sql = "SELECT MAX(oID) FROM Ord;";
    $result = $conn->query($sql);
    $oID = $result->fetch_assoc()['MAX(oID)'];

    // add information to Customer_Order
    $sql = "INSERT INTO Customer_Order (customer_id, oID) VALUES ($userID, $oID);";
    $conn->query($sql);

    // add information to Order_Table
    $conn->begin_transaction();

    try {
        // 查找符合条件的房间
        $stmt = $conn->prepare("
            SELECT `Table`.tID 
            FROM `Table` 
            JOIN Table_Table_type ON `Table`.tID = Table_Table_type.tID
            LEFT JOIN Order_Table ON `Table`.tID = Order_Table.tID
            LEFT JOIN Ord ON Order_Table.oID = Ord.oID
            WHERE Table_Table_type.ttID = ? 
            AND `Table`.clean_status = 'clean'
            AND (Ord.oID IS NULL OR Ord.check_in_status IN ('completed', 'cancelled'))
            LIMIT 1
        ");
        $stmt->bind_param("i", $ttID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $tID = $row['tID'];

            // insert Order_Table
            $stmt = $conn->prepare("INSERT INTO Order_Table (oID, tID) VALUES (?, ?)");
            $stmt->bind_param("ii", $oID, $tID);
            $stmt->execute();

            $conn->commit();
            echo "Room assigned successfully!";
        } else {
            echo "No suitable room available.";
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // if successful, redirect to order page
    header("Location: order-detail.php?orderID=$oID");
    exit();
?>
