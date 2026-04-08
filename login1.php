<?php
require_once 'db_connection.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get and decode input
$postdata = file_get_contents("php://input");
if (empty($postdata)) {
    echo json_encode(['error' => 'Missing request body']);
    exit;
}

$request = json_decode($postdata);
if (!isset($request->email, $request->password)) {
    echo json_encode(['error' => 'Email and password required']);
    exit;
}

$email = trim($request->email);
$password = $request->password;

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}
if (strlen($password) < 8) {
    echo json_encode(['error' => 'Password too short']);
    exit;
}

// Perform login
$result = loginUser($connection, $email, $password);

// Close connection if needed (optional, PHP will close it anyway)
// $connection->close();

echo json_encode($result);
exit;

function loginUser($connection, $email, $password)
{
    // Prepare statement to fetch user by email
    $stmt = $connection->prepare("SELECT id, first_name, last_name, email, phone, user_type, reg_date, password FROM tbl_registration WHERE email = ?");
    if (!$stmt) {
        return ['error' => 'Database error: ' . $connection->error];
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        return ['error' => 'No account found with this email'];
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['error' => 'Invalid email or password'];
    }

    // // Remove password hash from output
    // unset($user['password']);


    // session_start();
    // $_SESSION['user_id'] = $user['id'];
    // $_SESSION['login_time'] = time();

    // $sessionId = session_id();

    return [
        'success' => true,
        'user' => $user,
        // 'session_id' => $sessionId
    ];
}
?>