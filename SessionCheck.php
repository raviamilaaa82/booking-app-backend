<?php

require_once "response.php";
require_once "header.php";

function SessionCheck() {

    
     // Handle preflight (CORS)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    session_start();

    //  Session timeout (30 mins)
    $timeout = 1800; // seconds

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
        session_unset();
        session_destroy();

        http_response_code(401);
        echo json_encode([
            "status" => false,
            "message" => "Session expired"
        ]);
        exit;
    }

    // update last activity
    $_SESSION['LAST_ACTIVITY'] = time();

    //  check login
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            "status" => false,
            "message" => "Unauthorized"
        ]);
        exit;
    }

    //  return user id
    return $_SESSION['user_id'];
}

?>