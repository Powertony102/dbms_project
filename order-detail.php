<?php
// Initialize session
session_start();

// Authentication and authorization functions
function checkAuthentication() {
    if (!isset($_SESSION['userID'])) {
        header("Location: login.html");
        exit();
    }
}

function checkUserType() {
    $redirects = [
        'admin' => 'admin.php',
        'cleaner' => 'cleaner.php',
        'receptionist' => 'receptionist.php'
    ];
    
    if (isset($_SESSION['user_type']) && isset($redirects[$_SESSION['user_type']])) {
        header("Location: " . $redirects[$_SESSION['user_type']]);
        exit();
    }
}

// Order retrieval function with corrected price calculation
function getOrderDetails($conn, $userID, $orderID) {
    $userID = (int)$userID;
    $orderID = (int)$orderID;
    
    $sql = "SELECT DISTINCT
            Ord.oID AS OrderID,
            Table_type.ttID AS TableTypeID,
            Table_type.name AS TableTypeName,
            `Table`.tID AS TableID,
            Ord.order_time AS OrderDate,
            COALESCE(
                (SELECT SUM(Menu.price * Orders_Dishes.quantity) 
                 FROM Orders_Dishes 
                 JOIN Menu ON Orders_Dishes.dID = Menu.dID 
                 WHERE Orders_Dishes.oID = Ord.oID), 0
            ) + Table_type.price AS TotalPrice,
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
                User.uID = ? AND Ord.oID = ?";
                
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userID, $orderID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows == 0) {
        return null;
    }
    
    return $result->fetch_assoc();
}

// Display error message function
function displayErrorMessage($orderID, $userID) {
    echo '<div class="error-container">';
    echo '<p>Error: Order not found or you don\'t have permission to view this order.</p>';
    echo '<p>Order ID: ' . htmlspecialchars($orderID) . ' - User ID: ' . htmlspecialchars($userID) . '</p>';
    echo '<p><a href="order.php" class="btn-back">Go back to your orders</a></p>';
    echo '</div>';
}

// Render order details function with updated price field name
function renderOrderDetails($order) {
    echo '<div class="order-details">';
    echo '<h3>Order #' . htmlspecialchars($order['OrderID']) . '</h3>';
    echo '<p><strong>Table Type:</strong> ' . htmlspecialchars($order['TableTypeName']) . '</p>';
    echo '<p><strong>Date:</strong> ' . htmlspecialchars($order['OrderDate']) . '</p>';
    echo '<p><strong>Total Price:</strong> $' . htmlspecialchars($order['TotalPrice']) . '</p>';
    echo '<p><strong>Table ID:</strong> ' . htmlspecialchars($order['TableID']) . '</p>';
    echo '<p><strong>Status:</strong> ' . htmlspecialchars($order['OrderStatus']) . '</p>';
    echo '<p><strong>Comment:</strong> ' . htmlspecialchars($order['Comment'] ?? 'No comment yet') . '</p>';
    echo '</div>';
}

// Main execution
checkAuthentication();
checkUserType();

// Only process if we have an order ID
$orderID = isset($_GET['orderID']) ? $_GET['orderID'] : 0;
$userID = $_SESSION['userID'];
$orderDetails = null;

// Connect to the database
require_once('connect_DB.php');

if ($orderID) {
    $orderDetails = getOrderDetails($conn, $userID, $orderID);
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Order Details - Restaurant Reservation System</title>
</head>

<body>

    <!-- Header Section -->
    <header class="header">
        <h1>Restaurant Reservation Management System</h1>
        <nav class="nav-bar">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="credit.php">My Wallet</a></li>
                <li><a href="order.php">My Orders</a></li>
                <li><a href="logout.php" style="color: red;">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <section class="content-header">
            <h2>Order Information</h2>
            <p>Details of the order.</p>
        </section>

        <section class="content-body">
            <?php 
            if ($orderDetails) {
                renderOrderDetails($orderDetails);
            } else {
                displayErrorMessage($orderID, $userID);
            }
            ?>
        
            <?php if ($orderDetails): ?>
            <div class="review-section">
                <!-- Dish Ordering Section -->
                <div>
                    <?php
                    if ($orderDetails['OrderStatus'] === 'occupying') {
                        echo "<h3>Add Dishes to Your Order</h3>";
                        echo '<form method="get" action="add-dish-to-order.php">';
                        echo '<label class="form-label"><strong>You can continue ordering dishes here:</strong></label>';
                        echo '<input type="hidden" name="order_id" value="' . $orderDetails['OrderID'] . '">';
                        echo '<button type="submit" class="btn btn-primary">Order Dishes</button>';
                        echo '</form><br><br>';
                    }
                    ?>
                </div>

                <!-- Reviewing Section -->
                <div>
                    <h3>Leave a Review</h3>
                    <form action="submit-review.php" method="post">
                    <label for="review"><strong>Your Review:</strong></label>
                    <textarea id="review" name="review" rows="5" placeholder="Write your experience here..." required></textarea>
                    <input type="hidden" name="oID" value="<?php echo htmlspecialchars($orderID); ?>">
                    <button type="submit">Submit Review</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; DBMS Project Group 6</p>
    </footer>
</body>
</html>