<?php

require_once "response.php";
require_once "header.php";

function SessionCheck() {

    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $headers = getallheaders();

    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
    if (!$authHeader) {
        http_response_code(401);
        jsonResponse(false, null, "Session Expired","Authorization Error!", 401);
        return false;
        exit;
    }
    else{

        $token = str_replace('Bearer ', '', $authHeader);
        
        if (isset($_SESSION['user_id'])) {
            if ($token != $_SESSION['user_id']) {
                jsonResponse(false, null, "Authorization failed","Authorization Error!", 401);
                return false;
                exit;
            }
            else{
                return true;
                exit;
            }
        }
        else{
            jsonResponse(false, null, "Session Mismatch","Authorization Error!", 401);
            return false;
            exit;
        }
    }
}

?>