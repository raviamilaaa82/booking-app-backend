<?php
require_once "header.php";

// HANDLE PRE-FLIGHT REQUEST
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


require_once 'db_connection.php';
require_once "response.php";


// Get and decode input
$postdata = file_get_contents("php://input");
if (empty($postdata)) {
    // echo json_encode(['error' => 'Missing request body']);
    jsonResponse(false, null, "Missing request body", "Login Error", 200);
    exit;
}

$request = json_decode($postdata);
if (!isset($request->email, $request->password)) {
    // echo json_encode(['error' => 'Email and password required']);
    jsonResponse(false, null, "Email and password required", "Login Error", 200);
    exit;
}

$email = trim($request->email);
$password = $request->password;

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // echo json_encode(['error' => 'Invalid email format']);
    jsonResponse(false, null, "Invalid email format", "Login Error", 200);
    exit;
}
if (strlen($password) < 8) {
    // echo json_encode(['error' => 'Password too short']);
    jsonResponse(false, null, "Password too short", "Login Error", 200);
    exit;
}

// Perform login
$result = loginUser($connection, $email, $password);

function loginUser($connection, $email, $password)
{
    // Prepare statement to fetch user by email
    $stmt = $connection->prepare("SELECT id, first_name, last_name, email, phone, user_type, reg_date,password FROM tbl_registration WHERE email = ?");
    if (!$stmt) {
        return ['error' => 'Database error: ' . $connection->error];
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        // return ['error' => 'No account found with this email'];
        jsonResponse(false, null, "No account found with this email", "Login Error", 200);
        
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        // return ['error' => 'Invalid email or password'];
        jsonResponse(false, null, "Invalid email or password", "Login Error", 200);
    }

  
    $_SESSION = [];
    
    $_SESSION['user_id'] = $user['id'];
    $sessionId = session_id();
    $user['sessiontoken'] = $sessionId;
    jsonResponse(true, $user, "Sucessful login", "Login Successful", 200);
}
?>