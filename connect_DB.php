<?php
//connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbms_table";

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn -> connect_error){
    die("Connection failed: " . $conn -> connect_error);
} else {
    //echo "Connected successfully";
}
?>