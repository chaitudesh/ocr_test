<?php

session_start(); // Start the session

if (isset($_POST['submit'])) {
    // Get form data
    $name = $_POST['name'];
    $idType = $_POST['idType'];
    $idNumber = $_POST['idNumber'];
    $dob = $_POST['dob'];

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

    // Check if the ID type and number already exist
    $query1 = mysqli_query($conn, "SELECT * FROM user WHERE idtype = '$idType' AND idnumber = '$idNumber'");

    if (mysqli_num_rows($query1) > 0) {
        // Set session variable and redirect
        $_SESSION['message'] = 'ID Type and Number already exist.';
        header("Location: user_form.php"); // Redirect to the next file
        exit(); // Make sure to exit after redirecting
    } else {
        // If no record exists, insert the new user
        $insertQuery = "INSERT INTO user (name, idtype, idnumber, dob) VALUES ('$name', '$idType', '$idNumber', '$dob')";



        if (mysqli_query($conn, $insertQuery)) {
            $_SESSION['message'] = "New record created successfully.";
        } else {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
        }
        header("Location: list.php"); // Redirect to the next file
        exit(); // Make sure to exit after redirecting
    }

    // Close the connection
    mysqli_close($conn);
}
?>