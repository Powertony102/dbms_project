<?php
// add-dish-to-order.php
session_start();
require_once("connect_DB.php");

if (!isset($_GET['order_id'])) {
    die("Order ID not provided.");
}

$orderID = intval($_GET['order_id']);
$userID = $_SESSION['userID'] ?? null;
if (!$userID) {
    header("Location: login.html");
    exit();
}

// Get order info including status and balance
$stmt = $conn->prepare("
    SELECT Ord.oID, Ord.check_in_status, Ord.price AS total_price, User.balance
    FROM Ord
    JOIN Customer_Order ON Ord.oID = Customer_Order.oID
    JOIN User ON Customer_Order.customer_id = User.uID
    WHERE Ord.oID = ? AND User.uID = ?
");
$stmt->bind_param("ii", $orderID, $userID);
$stmt->execute();
$orderInfo = $stmt->get_result()->fetch_assoc();

if (!$orderInfo || $orderInfo['check_in_status'] !== 'occupying') {
    die("You can only order dishes while the table is occupying.");
}

// Fetch menu
$menuResult = $conn->query("SELECT dID, Dname, price FROM Menu");

// Fetch already ordered dishes
$orderedResult = $conn->prepare("
    SELECT Menu.Dname, Orders_Dishes.quantity, Menu.price
    FROM Orders_Dishes
    JOIN Menu ON Orders_Dishes.dID = Menu.dID
    WHERE Orders_Dishes.oID = ?
");
$orderedResult->bind_param("i", $orderID);
$orderedResult->execute();
$orderedItems = $orderedResult->get_result();

// Calculate total
$total = 0;
$itemsHTML = "";
while ($row = $orderedItems->fetch_assoc()) {
    $lineTotal = $row['price'] * $row['quantity'];
    $total += $lineTotal;
    $itemsHTML .= "<li>{$row['Dname']} x {$row['quantity']} - ¥{$lineTotal}</li>";
}

// Handle insufficient balance
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["dish_id"], $_POST["quantity"])) {
    $dishID = intval($_POST["dish_id"]);
    $qty = intval($_POST["quantity"]);
    if ($qty <= 0) $qty = 1;

    // Get price
    $stmt = $conn->prepare("SELECT price FROM Menu WHERE dID = ?");
    $stmt->bind_param("i", $dishID);
    $stmt->execute();
    $priceResult = $stmt->get_result()->fetch_assoc();
    $dishPrice = $priceResult['price'];
    $newTotal = $total + $qty * $dishPrice;

    if ($newTotal > $orderInfo['balance']) {
        echo "<script>alert('Insufficient balance! You have to charge first.');</script>";
    } else {
        // Add dish
        $insert = $conn->prepare("INSERT INTO Orders_Dishes (oID, dID, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
        $insert->bind_param("iii", $orderID, $dishID, $qty);
        $insert->execute();
        header("Location: add-dish-to-order.php?order_id=$orderID");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Order Dishes</title>
    <link rel="stylesheet" href="main.css">
</head>

<body>

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

<main class="main-content">
<div class="container">
<div class = "card">
    <h2 style="margin-top: 20px;">Order Dishes</h2>
    <form method="post" style="margin-bottom: 30px;">
        <label for="dish_id"><strong>Select Dish:</strong></label>
        <select name="dish_id" required class="form-control" style="height: 45px; font-size: 16px;">
            <?php while ($menu = $menuResult->fetch_assoc()) {
                echo "<option value='{$menu['dID']}'>{$menu['Dname']} - ¥{$menu['price']}</option>";
            } ?>
        </select>
        <label for="quantity" style="margin-top:10px;"><strong>Quantity:</strong></label>
        <input type="number" name="quantity" value="1" min="1" class="form-control" required>
        <br>
        <button type="submit" class="btn btn-primary">Add Dish</button>
    </form>

    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Current Order</h3>
            <ul>
                <?php echo $itemsHTML ?: "<li>No dishes added yet.</li>"; ?>
            </ul>
            <p><strong>Total:</strong> ¥<?php echo $total; ?> / Balance: ¥<?php echo $orderInfo['balance']; ?></p>
        </div>
    </div>
</div>
</div>
</main>

<!-- Footer -->
<footer class="footer">
    <p>&copy; DBMS Project Group 6</p>
</footer>
</body>
</html>