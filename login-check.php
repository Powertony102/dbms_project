<?php

//connect to the database
include('connect_DB.php');

// get the post data
$userID = $_POST['userID'];
$password = $_POST['password'];

// check if the user is in the database
$sql = "SELECT * FROM User WHERE uID = '$userID' AND password = '$password';";
$result = $conn->query($sql);

if($result->num_rows > 0){
    // user is in the database
    // start the session
    session_start();
    $_SESSION['userID'] = $userID;

    // get the user's user_type
    $row = $result->fetch_assoc();
    $_SESSION['user_type'] = $row['user_type'];

    // if user is admin, redirect to admin page
    if($_SESSION['user_type'] == 'admin'){
        header("Location: admin.php");
    }
    // if user is cleaner, redirect to cleaner page
    else if($_SESSION['user_type'] == 'cleaner'){
        header("Location: cleaner.php");
    }
    // if user is receptionist, redirect to receptionist page
    else if($_SESSION['user_type'] == 'receptionist'){
        header("Location: receptionist.php");
    }
    // if user is customer, redirect to home page
    else{
        header("Location: home.php");
    }
    
}else{
    // user is not in the database
    echo "Invalid username or password";
    echo "<br>";
    echo "<a href='login.html'>Go back to login page</a>";
    // header("Location: login.html");
}

?>