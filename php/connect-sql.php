<?php
$servername = "fdb33.awardspace.net";
$username = "3879700_skate";
$password = "SWm1234fergany";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>