<?php
function jsonResponse($success, $data = null, $error = null, $message = "", $statusCode = 200) {
    http_response_code($statusCode);

    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    echo json_encode([
        "success" => $success,
        "data" => $data,
        "error" => $error,
        "message" => $message
    ]);
    exit;
}