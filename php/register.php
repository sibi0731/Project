<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "guvitask";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmpassword"];

    $check_sql = "SELECT * FROM reginfo WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
           echo "<div class='alert alert-danger'>Email already exists. Please use a different email address.</div>";
        } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO reginfo (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $passwordHash);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>You are registered successfully. <a href='login.html'>Click here to login</a>.</div>";
        } else {
            echo "<div class='alert alert-danger'>Registration Failed</div>";
        }
    }
}

$conn->close();
?>
