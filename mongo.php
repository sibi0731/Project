<?php
session_start();
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\Exception\Exception;
$mongoConnectionString = "mongodb://localhost:27017";
$mongoManager = new Manager($mongoConnectionString);
$mongoDbName = "guvitask";
$mongoCollectionName = "profileinfo";
if (isset($_GET['email'])) {
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
    try {
        $filter = ['email' => $email];
        $options = [];
        $query = new Query($filter, $options);
        $namespace = "$mongoDbName.$mongoCollectionName"; 
        $cursor = $mongoManager->executeQuery($namespace, $query);
        $user = current($cursor->toArray());
        if ($data) {
            $response = [
                'email' => $data->email,
                'name' => $data->name
            ];
        } else {            
            $response = ['error' => 'User not found'];
        }
    } catch (Exception $e) {       
        $response = ['error' => 'An error occurred: ' . $e->getMessage()];
    }
} else {    
    $response = ['error' => 'An error occured'];
}
echo json_encode($response);
?>
