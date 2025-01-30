<?php
// Start the session (if needed)
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

// SQL query to fetch user data
$sql = "SELECT no, name, idType, idNumber, dob FROM user";
$result = mysqli_query($conn, $sql);

// Initialize an array to hold user data
$users = [];

// Check if the query was successful and if there are results
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row; // Add each row to the users array
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <h2 class="mb-4">User List</h2>

        <div class="d-flex justify-content-end">
            <a href="user_form.php" class="btn btn-primary mb-3">Add User</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>User ID</th>
                    <th>Name</th>

                    <th>Action</th> <!-- New Action Column -->
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($user['no']); ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td class="text-center">
                            <!-- Action Buttons -->
                            <a href="view.php?id=<?php echo $user['no']; ?>" class="btn btn-info btn-sm">View</a>
                            <a href="update.php?id=<?php echo $user['no']; ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="delete.php?id=<?php echo $user['no']; ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>