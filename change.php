<?php

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "student"; 

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$user_name = $_POST['username'];
$current_password = $_POST['oldpassword'];
$new_password = $_POST['newpassword'];


$sql = "SELECT * FROM login1 WHERE rollno = '$user_name' AND pas = '$current_password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $update_sql = "UPDATE login1 SET pas = '$new_password' WHERE rollno = '$user_name'";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "Password changed successfully!";
    } else {
        echo "Error updating password: " . $conn->error;
    }
} else {
    echo "<script>alert('Invalid username or current password! Please try again.')</script>";
}

$conn->close();
?>