<?php
// get the user id
session_start();
$userID = $_SESSION['userID'];

// connect to the database
include('connect_DB.php');

// get the post data
$review = $_POST['review'];
$oID = $_POST['oID'];

// submit review
// check if review already exists
$sql = "SELECT * FROM Order_Comment WHERE oID = '$oID';";
$result = $conn->query($sql);
if($result->num_rows > 0){
    // update the review
    $row = $result->fetch_assoc();
    $cID = $row['cID'];
    $sql = "UPDATE Comment SET comment = '$review' WHERE cID = '$cID';";
    $conn->query($sql);
}else{
    $sql = "INSERT INTO Comment (cID, comment) VALUES (Null, '$review');";
    $conn->query($sql);
    $cID = $conn->insert_id;
    $sql = "INSERT INTO Order_Comment (oID, cID) VALUES ('$oID', '$cID');";
    $conn->query($sql);
}

header("Location: order-detail.php?orderID=$oID");
?>