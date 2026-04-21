<?php

require_once "header.php";
require_once 'db_connection.php';
require_once "response.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// $headers = getallheaders();

// $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

// if (!$authHeader) {
//   http_response_code(401);
//   echo json_encode(["message" => "No token provided"]);
//   exit;
// }
// else{

//      $token = str_replace('Bearer ', '', $authHeader);
//     // $userId = decodeToken($token);
//      echo($token);
//      exit;
// }

// $token = str_replace('Bearer ', '', $authHeader);

// // decode token → get userId
// $userId = decodeToken($token);
// // echo($userId);

$msg_arr = [];


if (!($stmt = $connection->prepare("SELECT `id`,`sport_name`,`rate`,`icon` FROM `tbl_sports` WHERE `status`=1"))) {
    $msg = "Prepare failed for getting announcemnt user : (" . $connection->errno . ") " . $connection->error;
    $msg_arr[0]['identify'] = 'error';
    $msg_arr[1]['msg'] = $msg;
    echo json_encode($msg_arr);
} else if (!($stmt->execute())) {
    $msg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    $msg_arr[0]['identify'] = 'error';
    $msg_arr[1]['msg'] = $msg;
    echo json_encode($msg_arr);
} else {


    $result = $stmt->get_result();
    // $data = $result->fetch_assoc();
    $cr = 0;
    if ($result !== null && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $msg_arr[$cr]['id'] = $row['id'];
            $msg_arr[$cr]['sportname'] = $row['sport_name'];
            $msg_arr[$cr]['rate'] = $row['rate'];
            $msg_arr[$cr]['icon'] = $row['icon'];
            $cr++;
        }
    } else {
        $msg_arr[0]['identify'] = 'error';
        jsonResponse(false, null, null,"fetch error", 200);
    }
    // echo json_encode($msg_arr);
    jsonResponse(true, $msg_arr, null,"Sport data", 200);
    $stmt->close();
    $connection->close();
}




?>