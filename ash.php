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

$user_data = null;
if (isset($_SESSION['user_data'])) {
    $user_data = $_SESSION['user_data'];
    unset($_SESSION['user_data']); 
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM login1 WHERE rollno = ? AND pas = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password); 
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['username'] = $username; 
            $user_data = $result->fetch_assoc(); 
        } else {
            $sql = "SELECT * FROM table2 WHERE rollno = ? AND pas = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $password); 
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {   
                header("Location: admin.php");
                exit;
            } else {
                echo "<script type='text/javascript'>
                        alert('Invalid username or password.');
                        window.location.href='login.html'; 
                      </script>";
                exit;
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link rel="stylesheet" href="ash.css">
</head>
<body>
<div class="container">
    <h1>Student Details</h1>

    <?php if ($user_data): ?>
        <div class="profile">
            <div class="details">
                <p><strong>Roll No:</strong> <?php echo htmlspecialchars($user_data['rollno']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user_data['name']); ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($user_data['age']); ?></p>
                <p><strong>Course:</strong> <?php echo htmlspecialchars($user_data['course']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user_data['dob']); ?></p>
                <p><strong>Batch:</strong> <?php echo htmlspecialchars($user_data['batch']); ?></p>
                <p><strong>12th Marks:</strong> <?php echo htmlspecialchars($user_data['twelthmark']); ?></p>
                <p><strong>Blood Group:</strong> <?php echo htmlspecialchars($user_data['bloodgroup']); ?></p>
                <p><strong>CGPA:</strong> <?php echo htmlspecialchars($user_data['cgpa']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($user_data['gender']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user_data['phone']); ?></p>
                <p><strong>College:</strong> <?php echo htmlspecialchars($user_data['college']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($user_data['address']); ?></p>
            </div>
            <div class="image-container">
                <?php if (!empty($user_data['image'])): ?>
                    <img  src="<?php echo htmlspecialchars($user_data['image']); ?>" alt="Student Image" style="width:200px;height:200px;">
                <?php else: ?>
                    <p>No image available.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="alt.php">
        <button type="submit">Alter</button>
    </form>
</div>


</body>
</html>
