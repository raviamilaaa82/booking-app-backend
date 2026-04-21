<?php
function jsonResponse($success, $data = null, $error = null, $message = "", $statusCode = 200) {
    http_response_code($statusCode);

    header("Content-Type: application/json");

    echo json_encode([
        "success" => $success,
        "data" => $data,
        "error" => $error,
        "message" => $message
    ]);
    exit;
}