<?php
require_once "header.php";

session_start(); // MUST be here

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Destroy the session cookie (important)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}