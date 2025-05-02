<?php
// Initialize session and error reporting
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Authentication check
if(!isset($_SESSION['userID'])) {
    header("Location: login.html");
    exit();
}

// Authorization check - redirect based on user type
if($_SESSION['user_type'] == 'admin'){
    header("Location: admin.php");
    exit();
} else if($_SESSION['user_type'] == 'receptionist'){
    header("Location: receptionist.php");
    exit();
}

// Database connection
require_once('connect_DB.php');

// Function to get dirty tables
function getDirtyTables($conn) {
    $dirtyTables = [];
    $sql = "SELECT * FROM `Table` WHERE clean_status = 'dirty'";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            $dirtyTables[] = $row; 
        }
    }
    
    return $dirtyTables;
}

// Function to mark table as clean
function markTableAsClean($conn, $tableId) {
    $tableId = $conn->real_escape_string($tableId);
    $sql = "UPDATE `Table` SET clean_status = 'clean' WHERE rID = '$tableId'";
    
    return $conn->query($sql);
}

// Initialize variables
$errorMessage = '';
$successMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rID'])) {
    $tableId = $_POST['rID'];
    
    if (markTableAsClean($conn, $tableId)) {
        $successMessage = "Table #{$tableId} has been marked as clean.";
    } else {
        $errorMessage = "Error updating table: " . $conn->error;
    }
}

// Get all dirty tables
$tablesDirty = getDirtyTables($conn);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Cleaner Dashboard - Restaurant Reservation System</title>
</head>
<body>
    <!-- Header section -->
    <header class="header">
        <h1>Restaurant Reservation Management System</h1>
        <nav class="nav-bar">
            <ul>
                <li><a href="logout.php" style="color: red;">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main content -->
    <main class="main-content">
        <!-- Status messages -->
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        
        <section class="content-header">
            <h2>Uncleaned Tables</h2>
            <p>Tables needed to clean.</p>
        </section>

        <section class="content-body">
            <?php if (count($tablesDirty) > 0): ?>
                <div class="tables-container">
                    <?php foreach ($tablesDirty as $table): ?>
                        <div class="table-item">
                            <h4>Table #<?php echo htmlspecialchars($table['rID']); ?></h4>
                            <p><strong>Status:</strong> <?php echo htmlspecialchars($table['clean_status']); ?></p>
                            <form action="cleaner.php" method="POST">
                                <input type="hidden" name="rID" value="<?php echo htmlspecialchars($table['rID']); ?>">
                                <button type="submit" class="assign-button">Mark as Clean</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data-message">No tables need cleaning at the moment.</p>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; DBMS Project Group 6</p>
    </footer>
</body>
</html>