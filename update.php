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

// Check if the form is submitted
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $idType = $_POST['idType'];
    $idNumber = $_POST['idNumber'];
    $dob = $_POST['dob'];

    // Update user data
    $sql = "UPDATE user SET name = ?, idtype = ?, idnumber = ?, dob = ? WHERE no = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $idType, $idNumber, $dob, $no);

    if ($stmt->execute()) {
        $_SESSION['message'] = "User  updated successfully.";
        header("Location: list.php"); // Redirect to user list
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// Fetch user data for the form
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
    <meta name="viewport" content="width=device-width, initial -scale=1.0">
    <title>Update User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2>Update User</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="idType">ID Type</label>
                <input type="text" class="form-control" id="idType" name="idType"
                    value="<?php echo htmlspecialchars($user['idtype']); ?>" required>
            </div>
            <div class="form-group">
                <label for="idNumber">ID Number</label>
                <input type="text" class="form-control" id="idNumber" name="idNumber"
                    value="<?php echo htmlspecialchars($user['idnumber']); ?>" required>
            </div>
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob"
                    value="<?php echo htmlspecialchars($user['dob']); ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-primary">Update</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

</body>

</html>