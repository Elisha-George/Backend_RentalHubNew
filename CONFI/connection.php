<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bye";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else{
   //echo('connection successful');
}

// echo "Connected successfully";
?>






