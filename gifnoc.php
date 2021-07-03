<?php
// connection to MYSQL
$servername = "localhost"; // name of server
$username = "root"; // username
$password = "root"; // password
$dbname = "catalyst_test"; // name of the database

// Code to Create connection
$conn =  mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn){
    die("\nConnection failed: " . mysqli_connect_error());
}else{
    echo "\nSuccessfully connected to the database \n";
}
?>