<?php 
session_start();

$servername = "localhost"; 
$username_db = "root";     
$password_db = "";         
$dbname = "student";      

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

$message = '';

if (isset($_POST['alt'])) {
    $alt = $_POST['alt'];
    $altit = $_POST['alt_item'];
    $rollno = $_POST['rollno'];
    $password = $_POST['password'];

    $check_sql = "SELECT * FROM login1 WHERE rollno = ? AND pas = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $rollno, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sql = "UPDATE login1 SET $alt = ? WHERE rollno = ?";
        $update_stmt = $conn->prepare($sql);
        $update_stmt->bind_param("ss", $altit, $rollno);

        if ($update_stmt->execute()) {
            $updated_sql = "SELECT * FROM login1 WHERE rollno = ?";
            $updated_stmt = $conn->prepare($updated_sql);
            $updated_stmt->bind_param("s", $rollno);
            $updated_stmt->execute();
            $user_data = $updated_stmt->get_result()->fetch_assoc();
            $_SESSION['user_data'] = $user_data;
            
           header("Location: ash.php");
            
            exit;
        } else {
            $message = "Error updating field: " . $conn->error;
        }
        $update_stmt->close();
    } else {
        $message = "Invalid Roll No or Password";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alter Student Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .alter-form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: auto;
        }
        h3 {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #091A5C;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background:#0056B3;
        }
        .message {
            margin: 20px 0;
            color: #d9534f; 
        }
    </style>
</head>
<body>
    <div class="alter-form">
        <h3>Alter Student Profile</h3>
        <form method="POST" action="">
            <input type="text" name="rollno" placeholder="Enter Roll No" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <label for="alt">Alter field:</label>
            <select name="alt" id="alt" required>
                <option value="name">Name</option>
                <option value="age">Age</option>
                <option value="course">Course</option>
                <option value="phone">Phone</option>
                <option value="cgpa">CGPA</option>
                <option value="address">Address</option>
            </select>
            <input type="text" name="alt_item" placeholder="New Value" required>
            <button type="submit">Update</button>
        </form>
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
