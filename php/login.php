<?php
session_start();

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

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

            $session_token = bin2hex(random_bytes(16));
            $session_key = "session:$session_token";
            $redis->setex($session_key, 3600, $email);
            setcookie('session_token', $session_token, time() + 3600, '/');

            $response = array(
                'success' => true,
                'id' => $row['id'],
                'email' => $row['email'],
                'session_token' => $session_token
            );
        } else {
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
    echo json_encode($response);
}

$conn->close();
?>
