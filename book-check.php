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

    // get the get parameter rtID
    $rtID = $_GET['rtID'];

    // connect to the database
    include('connect_DB.php');

    // get the user id
    $userID = $_SESSION['userID'];

    // get the Table type information
    $sql = "SELECT * FROM Table_type WHERE rtID = $rtID;";
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
            SELECT `Table`.rID 
            FROM `Table` 
            JOIN Table_Table_type ON `Table`.rID = Table_Table_type.rID
            LEFT JOIN Order_Table ON `Table`.rID = Order_Table.rID
            LEFT JOIN Ord ON Order_Table.oID = Ord.oID
            WHERE Table_Table_type.rtID = ? 
            AND `Table`.clean_status = 'clean'
            AND (Ord.oID IS NULL OR Ord.check_in_status IN ('completed', 'cancelled'))
            LIMIT 1
        ");
        $stmt->bind_param("i", $rtID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $rID = $row['rID'];

            // insert Order_Table
            $stmt = $conn->prepare("INSERT INTO Order_Table (oID, rID) VALUES (?, ?)");
            $stmt->bind_param("ii", $oID, $rID);
            $stmt->execute();

            $conn->commit();
            echo "房间成功分配！";
        } else {
            echo "没有符合条件的房间。";
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "错误: " . $e->getMessage();
    }

    // if successful, redirect to order page
    header("Location: order-detail.php?orderID=$oID");
    exit();
?>
