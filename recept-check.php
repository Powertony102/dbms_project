<?php
// Initialize session
session_start();

/**
 * Authentication check function
 * Verifies if the user is logged in
 */
function checkAuthentication() {
    if (!isset($_SESSION['userID'])) {
        header("Location: login.html");
        exit();
    }
}

/**
 * Process check-in action
 * Updates order status to 'occupying' and sets price
 */
function processCheckIn($conn, $orderID) {
    $query = "UPDATE Ord
              SET 
                check_in_status = 'occupying', 
                order_time = NOW(),
                price = (SELECT price FROM `Table` WHERE tID = (SELECT tID FROM Order_Table WHERE oID = ? LIMIT 1))
              WHERE oID = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $orderID, $orderID);
    
    if ($stmt->execute()) {
        return "Check-in successful.";
    } else {
        return "Error during check-in: " . $conn->error;
    }
}

/**
 * Process check-out action
 * Updates order status to 'completed' and marks table as dirty
 * Deducts the total cost from the user's balance
 */
function processCheckOut($conn, $orderID) {

    // Start transaction
    $conn->begin_transaction();
    
    try {
        // 1. Calculate total cost
        $stmt = $conn->prepare("SELECT SUM(Menu.price * Orders_Dishes.quantity) AS total
                                FROM Orders_Dishes
                                JOIN Menu ON Orders_Dishes.dID = Menu.dID
                                WHERE Orders_Dishes.oID = ?");
        $stmt->bind_param("i", $orderID);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $totalCost = $row['total'] ?? 0;

        // 2. Deduct from user balance
        $stmt = $conn->prepare("UPDATE User u
                                JOIN Customer_Order co ON u.uID = co.customer_id
                                SET u.balance = u.balance - ?
                                WHERE co.oID = ?");
        $stmt->bind_param("di", $totalCost, $orderID);
        $stmt->execute();

        // 3. Update order status
        $stmt = $conn->prepare("UPDATE Ord SET check_in_status = 'completed' WHERE oID = ?");
        $stmt->bind_param("i", $orderID);
        $stmt->execute();

        $conn->commit();
        return "Check-out successful.";
    } catch (Exception $e) {
        $conn->rollback();
        return "Error during check-out: " . $e->getMessage();
    }

    try {
        // Update order status to completed
        $query1 = "UPDATE Ord
                  SET 
                    check_in_status = 'completed', 
                    order_time = NOW(),
                    price = (SELECT price FROM `Table` WHERE tID = (SELECT tID FROM Order_Table WHERE oID = ? LIMIT 1))
                  WHERE oID = ?";
                  
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("ss", $orderID, $orderID);
        $stmt1->execute();
        
        // Update table status to dirty
        $query2 = "UPDATE `Table`
                  SET clean_status = 'dirty'
                  WHERE tID = (SELECT tID FROM Order_Table WHERE oID = ? LIMIT 1)";
                  
        $stmt2 = $conn->prepare($query2);
        $stmt2->bind_param("s", $orderID);
        $stmt2->execute();
        
        // Commit transaction
        $conn->commit();
        return "Check-out successful.";
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        return "Error during check-out: " . $e->getMessage();
    }
}

/**
 * Validates input parameters
 */
function validateInput($orderID, $action) {
    if (empty($orderID) || empty($action)) {
        return "Error: Missing required fields.";
    }
    
    if ($action !== 'checkin' && $action !== 'checkout') {
        return "Invalid action.";
    }
    
    return null;
}

/**
 * Main execution
 */
function main() {
    // Check authentication
    checkAuthentication();
    
    // If not a POST request, return early
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return "This script only accepts POST requests.";
    }
    
    // Extract and validate input
    $orderID = $_POST['orderID'] ?? '';
    $action = $_POST['action'] ?? '';
    
    $validationError = validateInput($orderID, $action);
    if ($validationError) {
        return $validationError;
    }
    
    // Connect to database
    include('connect_DB.php');
    
    // Process the action
    $result = '';
    if ($action === 'checkin') {
        $result = processCheckIn($conn, $orderID);
    } else if ($action === 'checkout') {
        $result = processCheckOut($conn, $orderID);
    }
    
    // Close database connection
    $conn->close();
    
    return $result;
}

// Run the script and output the result
echo main();
?>