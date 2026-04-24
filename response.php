<?php

function jsonResponse($success, $data = null, $error = null, $message = "", $statusCode = 200) {
    http_response_code($statusCode);

    require_once "header.php";

    echo json_encode([
        "success" => $success,
        "data" => $data,
        "error" => $error,
        "message" => $message
    ]);
    exit;
}