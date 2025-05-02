<?php
// get the user id
session_start();
$userID = $_SESSION['userID'];

// connect to the database
include('connect_DB.php');

// get the post data
$amount = $_POST['amount'];

// recharge
$sql = "UPDATE User SET balance = balance + $amount WHERE uID = '$userID';";

if($conn->query($sql) === TRUE){
    echo "Recharge successful!";
    header("Location: credit.php");
}else{
    echo "Error, Please try again.";
    echo "<br>";
    echo "<a href='credit.php'>Back to My Wallet</a>";
}
$conn->close();
?>