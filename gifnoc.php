<?php
// connection to MYSQL

// Code to Create connection
$conn =  mysqli_connect($h, $u, $p, $dbname);
// Check connection
if (!$conn){
    die("\nConnection failed: " . mysqli_connect_error());
} else {
    echo "\nSuccessfully connected to the database \n";
}
?>