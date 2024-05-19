<?php
session_start();
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$email = isset($_GET['email']) ? $_GET['email'] : '';
if ($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $userDataJson = $redis->get('user:' . $email);
    if ($userDataJson) {
        $userData = json_decode($userDataJson, true);
        echo json_encode($userData);
    } else {
        echo json_encode(['error' => 'User data not found in Redis']);
    }
} else {
    echo json_encode(['error' => 'An Error Occured']);
}
?>
