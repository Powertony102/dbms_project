<?php
include 'connect_DB.php';

// Set Content-Type header for JSON response
header('Content-Type: application/json');

// Get action type
$action = $_GET['action'] ?? $_POST['action'] ?? null;

// Check action type
if (!$action) {
    echo json_encode(['error' => 'No action specified']);
    exit;
}

switch ($action) {
    case 'fetch_user':
        fetchUser($conn);
        break;
    case 'update_user':
        updateUser($conn);
        break;
    case 'fetch_Table':
        fetchTable($conn);
        break;
    case 'update_Table':
        updateTable($conn);
        break;
    case 'fetch_Table_type':
        fetchTableType($conn);
        break;
    case 'update_Table_type':
        updateTableType($conn);
        break;
    default:
        error_log("Invalid action: $action", 3, "/var/log/php_errors.log");
        echo json_encode(['error' => 'Invalid action']);
        break;
}

// Fetch user information
function fetchUser($conn) {
    $userID = $_GET['userID'] ?? null;
    if (!$userID) {
        echo json_encode(['error' => 'User ID is required']);
        exit;
    }

    $sql = "SELECT * FROM User WHERE uID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
}

// Update user information
function updateUser($conn) {
    $userID = $_POST['userID'] ?? null;
    $name = $_POST['name'] ?? null;
    $balance = $_POST['balance'] ?? null;
    $user_type = $_POST['user_type'] ?? null;

    if (!$userID || !$name || !$balance || !$user_type) {
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    $sql = "UPDATE User SET name = ?, balance = ?, user_type = ? WHERE uID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsi", $name, $balance, $user_type, $userID);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update user']);
    }
}

// Fetch Table information
function fetchTable($conn) {
    try {
        $tableID = filter_input(INPUT_GET, 'tableID', FILTER_VALIDATE_INT);
        
        if ($tableID === false || $tableID === null) {
            throw new Exception('Table ID is required');
        }

        $stmt = $conn->prepare("SELECT * FROM `Table` WHERE tID = ?");
        $stmt->bind_param("i", $tableID);
        
        if (!$stmt->execute()) {
            throw new Exception('Query execution failed');
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (!$row) {
            throw new Exception('Table not found');
        }
        
        echo json_encode($row);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Update Table information
function updateTable($conn) {
    $tableID = $_POST['tableID'] ?? null;
    $clean_status = $_POST['clean_status'] ?? null;

    if (!$tableID || !$clean_status) {
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    $sql = "UPDATE `Table` SET clean_status = ? WHERE tID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $clean_status, $tableID);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update Table']);
    }
}

// Fetch Table type information
function fetchTableType($conn) {
    $TableTypeID = $_GET['TableTypeID'] ?? null;
    if (!$TableTypeID) {
        echo json_encode(['error' => 'Table Type ID is required']);
        exit;
    }

    $sql = "SELECT * FROM Table_type WHERE ttID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $TableTypeID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Table Type not found']);
    }
}

// Update Table type information
function updateTableType($conn) {
    $TableTypeID = $_POST['TableTypeID'] ?? null;
    $introduction = $_POST['introduction'] ?? null;
    $price = $_POST['price'] ?? null;
    $remain = $_POST['remain'] ?? null;

    if (!$TableTypeID || !$introduction || !$price || !$remain) {
        echo json_encode(['error' => 'All fields are required']);
        exit;
    }

    $sql = "UPDATE Table_type SET introduction = ?, price = ?, remain = ? WHERE ttID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdii", $introduction, $price, $remain, $TableTypeID);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update Table type']);
    }
}
?>
