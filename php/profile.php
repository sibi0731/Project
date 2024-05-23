<?php
session_start();
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;
use MongoDB\Driver\BulkWrite;
$mongoConnectionString = "mongodb://localhost:27017";
$mongoManager = new Manager($mongoConnectionString);
$mongoDbName = "guvitask";
$mongoCollectionName = "profileinfo";

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
function authenticate() {
    global $redis;
    if (isset($_COOKIE['session_token'])) {
        $session_token = $_COOKIE['session_token'];
        $session_key = "session:$session_token";
        $email = $redis->get($session_key);
        if ($email) {

            return $email;
        }
    }
    return false;
}

function objectToArray($obj) {
    return json_decode(json_encode($obj), true);
}
function saveProfile($manager, $data, $redis) {
    global $mongoDbName, $mongoCollectionName;
    $bulk = new MongoDB\Driver\BulkWrite();
    $filter = ['email' => $data['email']];
    $query = new MongoDB\Driver\Query($filter);
    $cursor = $manager->executeQuery("$mongoDbName.$mongoCollectionName", $query);
    $existingProfile = current($cursor->toArray());
    if ($existingProfile) {
        $bulk->update($filter, ['$set' => $data]);
        $responseMessage = "<div class='alert alert-danger'>You have already updated your profile</div>";
    } else {
        $bulk->insert($data);
        $responseMessage = "<div class='alert alert-success'>Your profile successfully saved</div>";
    }

    try {
        $manager->executeBulkWrite("$mongoDbName.$mongoCollectionName", $bulk);
        $redisKey = 'profile:' . $data['email'];
        $redis->del($redisKey);
        $redis->set($redisKey, json_encode($data));
        return ['success' => true, 'message' => $responseMessage];
    } catch (MongoDB\Driver\Exception\Exception $e) {
        return ['success' => false, 'message' => "<div class='alert alert-danger'>An error occurred: " . $e->getMessage() . "</div>"];
    } catch (RedisException $e) {
        return ['success' => false, 'message' => "<div class='alert alert-danger'>An error occurred with Redis: " . $e->getMessage() . "</div>"];
    }
}
$email = authenticate();
if ($email) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST['name'];
        $age = $_POST['age'];
        $dob = $_POST['dob'];
        $contactno = $_POST['contactno'];
        $gender = $_POST['gender'];
        $data = [
            'name' => $name,
            'email' => $email, 
            'age' => $age,
            'dob' => $dob,
            'contactno' => $contactno,
            'gender' => $gender
        ];

        $result = saveProfile($mongoManager, $data, $redis);
        echo $result['message'];
    }else {
        $query = new MongoDB\Driver\Query(['email' => $email]);
        $cursor = $mongoManager->executeQuery("$mongoDbName.$mongoCollectionName", $query);
        $userData = current($cursor->toArray());

        if ($userData) {
            $redis->set($redisKey, json_encode($userData));
            echo json_encode($userData);
        } else {
            echo json_encode(['error' => 'User data not found']);
        }
    }
} else {
    echo json_encode(['error' => 'Authentication failed']);
}
?>
