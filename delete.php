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

// SQL query to delete user
$sql = "DELETE FROM user WHERE no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $no);

if ($stmt->execute()) {
    $_SESSION['message'] = "User  deleted successfully.";
    header("Location: list.php"); // Redirect to user list
    exit();
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}

// Close the connection
mysqli_close($conn);
?>