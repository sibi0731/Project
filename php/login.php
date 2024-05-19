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
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM reginfo WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc(); 
        if (password_verify($password, $row["password"])) {
            $response = array(
                'success' => true,
                'id' => $row['id'], 
                'email' => $row['email'], 
            ); 
        } 
        else {         
            $response = array(
                'success' => false,
                'message' => "Invalid email or password"
            );
        }
    } else {
        $response = array(
            'success' => false,
            'message' => "User not found"
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}

$conn->close();
?>
