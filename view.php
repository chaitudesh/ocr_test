<?php
session_start();

// Database connection parameters
$servername = "localhost"; // Usually 'localhost'
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "test"; // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get user ID from query parameter
$no = $_GET['id'];

// SQL query to fetch user data
$sql = "SELECT * FROM user WHERE no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User  not found.";
    exit();
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2>User Details</h2>
        <table class="table table-bordered">
            <tr>
                <th>User ID</th>
                <td><?php echo htmlspecialchars($user['no']); ?></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
            </tr>
            <tr>
                <th>ID Type</th>
                <td><?php echo htmlspecialchars($user['idtype']); ?></td>
            </tr>
            <tr>
                <th>ID Number</th>
                <td><?php echo htmlspecialchars($user['idnumber']); ?></td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td><?php echo htmlspecialchars($user['dob']); ?></td>
            </tr>
        </table>
        <a href="list.php" class="btn btn-primary">Back to User List</a>
    </div>

</body>

</html>