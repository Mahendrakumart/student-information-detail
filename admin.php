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

$students = [];
$user_details = null;

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

$order_by = 'name'; 
$order_direction = 'ASC'; 

if (isset($_POST['sort_by'])) {
    $order_by = $_POST['sort_by'];
    $order_direction = $_POST['order_direction'] === 'DESC' ? 'DESC' : 'ASC'; 
} else if (isset($_GET['sort_by'])) { 
    $order_by = $_GET['sort_by'];
    $order_direction = $_GET['order_direction'] === 'DESC' ? 'DESC' : 'ASC'; 
}

if (isset($_POST['delete_rollno']) && isset($_POST['delete_pas'])) {
    $delete_rollno = $_POST['delete_rollno'];
    $delete_pas = $_POST['delete_pas'];

    $sql = "SELECT * FROM login1 WHERE rollno = ? AND pas = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $delete_rollno, $delete_pas);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sql = "DELETE FROM login1 WHERE rollno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $delete_rollno);
        $stmt->execute();
    } else {
        echo "<script>alert('Invalid Roll No or Password');</script>";
    }
    $stmt->close();
}

$sql = "SELECT rollno, pas, name, image FROM login1 ORDER BY $order_by $order_direction"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row; 
    }
}

if (isset($_GET['rollno'])) {
    $rollno = $_GET['rollno'];
    $sql = "SELECT * FROM login1 WHERE rollno = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rollno);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_details = $result->fetch_assoc();
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
    <title>Student Details - Table2 User</title>
    <link rel="stylesheet" href="admin.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
           
        }

        .container {
            max-width: 1200px; 
            margin: 0 auto;
            padding: 20px;
            display: flex;
            height: auto; 
            
        }

        .profile {
            flex: 2;
            margin-right: 20px;
            background: white;
            padding: 70px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .forms {
            flex: 1;
            
            flex-direction: column;
            position: sticky;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        li a {
            text-decoration: none;
            color: #0066cc;
        }

        li a:hover {
            text-decoration: underline;
        }

        .profile-details {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start; 
        }

        .details {
            flex: 1;
            padding-right: 20px; 
        }

        .image-container {
            flex-shrink: 0;
        }

        .image-container img {
            width: 200px;
            height: 200px;
            border-radius: 5px;
        }

        .sorting-form,
        .deleting-form,
        .login-form {
            margin-bottom: 20px; 
            padding: 20px; 
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #D3D3D3; 
        }

        .sorting-form button,
        .deleting-form button,
        .login-form button {
            padding: 15px 20px; 
            font-size: 16px; 
            width: 100%; 
            cursor: pointer; 
            background-color: #091A5C; 
            color: white; 
            border: none; 
            border-radius: 5px; 
        }

        .sorting-form button:hover,
        .deleting-form button:hover,
        .login-form button:hover {
            background-color: #042194; 
        }

        .sorting-form label,
        .deleting-form label {
            margin-right: 10px;
        }

        .sorting-form select,
        .deleting-form input,
        .login-form input {
            width: calc(100% - 20px); 
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px; 
        }

        .sorting-form select {
            height: 40px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile">
            <h1>Students List</h1>

            <ul>
                <?php foreach ($students as $student): ?>
                    <li>
                        <a href="?rollno=<?php echo htmlspecialchars($student['rollno']); ?>&sort_by=<?php echo htmlspecialchars($order_by); ?>&order_direction=<?php echo htmlspecialchars($order_direction); ?>">
                            <?php echo htmlspecialchars($student['name']); ?> (Roll No: <?php echo htmlspecialchars($student['rollno']); ?> || Password: <?php echo htmlspecialchars($student['pas']); ?>)
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($user_details): ?>
                <div class="profile-details">
                    <div class="details">
                        <h2>Details for <?php echo htmlspecialchars($user_details['name']); ?></h2>
                        <p><strong>Roll No:</strong> <?php echo htmlspecialchars($user_details['rollno']); ?></p>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user_details['name']); ?></p>
                        <p><strong>Age:</strong> <?php echo htmlspecialchars($user_details['age']); ?></p>
                        <p><strong>Course:</strong> <?php echo htmlspecialchars($user_details['course']); ?></p>
                        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user_details['dob']); ?></p>
                        <p><strong>Batch:</strong> <?php echo htmlspecialchars($user_details['batch']); ?></p>
                        <p><strong>12th Marks:</strong> <?php echo htmlspecialchars($user_details['twelthmark']); ?></p>
                        <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($user_details['bloodgroup']); ?></p>
                        <p><strong>CGPA:</strong> <?php echo htmlspecialchars($user_details['cgpa']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                        <p><strong>Gender:</strong> <?php echo htmlspecialchars($user_details['gender']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user_details['phone']); ?></p>
                        <p><strong>College:</strong> <?php echo htmlspecialchars($user_details['college']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($user_details['address']); ?></p>
                    </div>
                    <div class="image-container">
                        <?php if (!empty($user_details['image'])): ?>
                            <img src="<?php echo htmlspecialchars($user_details['image']); ?>" alt="Student Image">
                        <?php else: ?>
                            <p>No image available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="forms">
            <div class="sorting-form">
                <h3>Sort Students</h3>
                <form method="POST" action="">
                    <label for="sort_by">Sort By:</label>
                    <select name="sort_by" id="sort_by">
                        <option value="name">Name</option>
                        <option value="rollno">Roll No</option>
                        <option value="cgpa">CGPA</option>
                        <option value="age">Age</option>
                        <option value="twelthmark">12th Mark</option>
                    </select>
                    <label for="order_direction">Order:</label>
                    <select name="order_direction" id="order_direction">
                        <option value="ASC">Ascending</option>
                        <option value="DESC">Descending</option>
                    </select>
                    <button type="submit">Sort</button>
                </form>
            </div>

            <div class="deleting-form">
                <h3>Delete Student</h3>
                <form method="POST" action="">
                    <input type="text" name="delete_rollno" placeholder="Enter Roll No" required>
                    <input type="password" name="delete_pas" placeholder="Enter Password" required>
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                </form>
            </div>

            <div class="login-form">
                <h3>Login As Student</h3>
                <form method="POST" action="ash.php">
                    <input type="text" name="username" placeholder="Roll No" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
