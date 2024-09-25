<?php
$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = 'form';
 
$conn = new mysqli($servername, $db_username, $db_password,$db_name, 3306);
 
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}