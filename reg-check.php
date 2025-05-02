<?php

//connect to the database
include('connect_DB.php');

// get the post data
$name = $_POST['name'];
$phone = $_POST['phone'];
$password = $_POST['password'];
$confrim_password = $_POST['confirmPassword'];
$user_type = $_POST['usrType'];

// check if password and confrim password are the same
if($password != $confrim_password){
    // show error message
    echo "Password and confrim password are not the same";
    echo "<br>Back to <a href='register.html'>register</a>";
    exit;
}

// insert the user into the database
$sql = "INSERT INTO User (uID, name, balance, user_type, password, phone) VALUES (Null, '$name', 0, '$user_type', '$password', '$phone');";
// insert data and redict to login page
if($conn->query($sql) === TRUE){
    // get the user id
    $sql = "SELECT uID FROM User WHERE name = '$name' AND password = '$password';";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $uID = $row['uID'];
    echo "Register successful!";
    echo "<br>Your user ID is: " . $uID;
    echo "<br>Back to <a href='login.html'>login</a>";
    exit();
}else{
    echo "Please try again";
    echo "<br>Back to <a href='register.html'>register</a>";
}
$conn->close();
?>