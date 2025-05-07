<?php
    // 启用错误报告以便调试
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

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
    <title>My Orders - Restaurant System</title>
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
                // connect to the database
                include('connect_DB.php');

                // 检查数据库连接
                if ($conn->connect_error) {
                    die("Database connection failed: " . $conn->connect_error);
                }

                // 获取用户ID并进行安全处理
                $userID = (int)$_SESSION['userID'];
                
                // 修改SQL查询，使用Table_type表中的价格
                $sql = "SELECT DISTINCT
                        Ord.oID AS OrderID,
                        Table_type.ttID AS TableTypeID,
                        Table_type.name AS TableTypeName,
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
                            Customer_Order ON User.uID = Customer_Order.customer_id
                        JOIN 
                            Ord ON Customer_Order.oID = Ord.oID
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
                            User.uID = ?
                        ORDER BY OrderDate DESC";
                
                // 使用预处理语句防止SQL注入
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    die("SQL prepare failed: " . $conn->error);
                }
                
                $stmt->bind_param("i", $userID);
                $stmt->execute();
                
                if ($stmt->error) {
                    die("SQL execution failed: " . $stmt->error);
                }
                
                $result = $stmt->get_result();
                
                // 检查是否有订单
                if ($result->num_rows == 0) {
                    echo "<p>You have no orders</p>";
                } else {
                    // 显示用户订单
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='card'>";
                        echo "<h3>Order #" . htmlspecialchars($row['OrderID']) . "</h3>";
                        echo "<p><strong>Table Type:</strong> " . htmlspecialchars($row['TableTypeName']) . "</p>";
                        echo "<p><strong>Date:</strong> " . htmlspecialchars($row['OrderDate']) . "</p>";
                        echo "<p><strong>Total Price:</strong> $" . htmlspecialchars($row['TotalPrice']) . "</p>";
                        echo "<p><strong>Status:</strong> " . htmlspecialchars($row['OrderStatus']) . "</p>";
                        echo "<button onclick=\"window.location.href='order-detail.php?orderID=" . htmlspecialchars($row['OrderID']) . "'\">View Details</button>";
                        echo "</div>";
                    }
                }
                
                // 关闭预处理语句和数据库连接
                $stmt->close();
                $conn->close();
            ?>
        </section>
    </main>

    <!-- footer -->
    <footer class="footer">
        <p>&copy; DBMS Project Group 6</p>
    </footer>
</body>
</html>