<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST,GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials:true");

require_once 'db_connection.php';
require_once "response.php";

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);

    $code = $request->code;
    $email = $request->email;
    // $userpassword = $request->userpassword;
    $msg_arr = [];
    // $user_data = [];
    if (checkingCode($connection, $email, $code) == 1) {
        jsonResponse(true, null, "Your email is verified.", "Email Verified", 200);
    } else {
        jsonResponse(false, null, "Your OTP verification failed, Please Check your OTP", "Email Verification Failed", 200);
    }
}

function checkingCode($connection, $email, $code)
{
    // $random_num=0;
    if (!($stmt = $connection->prepare("SELECT COUNT(id) as code_count FROM tbl_temp_email WHERE email=? AND random_num=? ORDER BY ID DESC LIMIT 1"))) {
        $msg = "Prepare failed for checking user : (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("si", $email, $code))) {
        $msg = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;;
        echo json_encode($msg_arr);
    } else if (!($stmt->execute())) {
        $msg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else {
        $random_num_result = $stmt->get_result();
        $random_count = $random_num_result->fetch_assoc();

        $random_num_count = $random_count['code_count'];
    }
    if ($stmt !== null) {
        $stmt->close();
    }

    $connection->close();
    return $random_num_count;
}
