<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: OPTIONS,POST,GET");
header("Access-Control-Allow-Headers:Origin, X-Api-Key, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Credentials:true");

require_once 'db_connection.php';

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);

    $mobile = $request->mobile;
    $userpassword = $request->userpassword;
    $msg_arr = [];
    $user_data = [];

    gettingUserPw($connection, $mobile, $userpassword);
}

function gettingUserPw($connection, $mobile, $userpassword)
{
    if (!($stmt = $connection->prepare("SELECT password FROM tbl_registrations WHERE mobile=?"))) {
        $msg = "Prepare failed for checking user : (" . $connection->errno . ") " . $connection->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("s", $mobile))) {
        $msg = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->execute())) {
        $msg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else {

        $usertype = [];
        $hashpw = '';
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($result !== null && $result->num_rows > 0) {
            $hashpw = $data['password'];


            if (password_verify($userpassword, $hashpw)) {

                checkingClientLogin($connection, $mobile, $hashpw);
            } else {
                $msg = "User name or password error!";

                $msg_arr[0]['identify'] = 'error';
                $msg_arr[1]['msg'] = $msg;
                echo json_encode($msg_arr);
            }
        } else {
            $msg = "You are not registered user. Please register!";

            $msg_arr[0]['identify'] = 'error';
            $msg_arr[1]['msg'] = $msg;
            echo json_encode($msg_arr);
        }

        $stmt->close();
    }
}



function checkingClientLogin($connection, $mobile, $hashpw)
{
    if (!($stmt = $connection->prepare("SELECT COUNT(id) as check_user_count FROM tbl_registrations WHERE mobile=? AND password=?"))) {
        $msg = "Prepare failed for checking user : (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;

        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("ss", $mobile, $hashpw))) {
        $msg = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;


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
        $count = $result->fetch_assoc();

        $user_count = $count['check_user_count'];
        $user_data = [];
        $user_dates = [];
        $user_types = [];
        $user_validity = [];
        $clientuid = "";

        if ($user_count == 1) {

            $user_types = checkingTypeOfUser($connection, $mobile, $hashpw);

            if ($user_types[0] !== "head" && $user_types[0] !== "admin") {

                $user_validity = checkingValidity($connection, $mobile, $hashpw);

                if ($user_validity[0] == 1) {

                    $user_dates = gettingExpDate($connection, $mobile, $hashpw);
                    $current_date = date("Y-m-d");

                    $exp_date = new DateTime($user_dates[1]);
                    $today = new DateTime($current_date);


                    if ($exp_date >= $today) {
                        $clientuid = creatingUniqId();
                        $_SESSION['uniquid'] = $clientuid;

                        $user_data = gettingClientDetails($mobile, $hashpw, $connection);

                        $msg_arr[0]['identify'] = 'success';
                        $msg_arr[2]['id'] = $user_data[0];
                        $msg_arr[3]['name'] = $user_data[1];
                        $msg_arr[4]['usertype'] = $user_data[2];
                        $msg_arr[5]['uniqid'] =  $clientuid;
                    } else {

                        updateValidity($connection, $mobile, $hashpw);
                        $msg = "Pending!";

                        $msg_arr[0]['identify'] = 'error';
                        $msg_arr[1]['msg'] = $msg;
                    }
                } else {
                    $msg = "Pending!";

                    $msg_arr[0]['identify'] = 'error';
                    $msg_arr[1]['msg'] = $msg;
                }
            } else {
                $clientuid = creatingUniqId();
                $_SESSION['uniquid'] = $clientuid;
                $user_data = gettingClientDetails($mobile, $hashpw, $connection);

                $msg_arr[0]['identify'] = 'success';
                $msg_arr[2]['id'] = $user_data[0];
                $msg_arr[3]['name'] = $user_data[1];
                $msg_arr[4]['usertype'] = $user_data[2];
                $msg_arr[5]['uniqid'] =  $clientuid;
            }
        } else {
            $msg = "User name or password error!";

            $msg_arr[0]['identify'] = 'error';
            $msg_arr[1]['msg'] = $msg;
        }

        $stmt->close();

        echo json_encode($msg_arr);
    }
}

function checkingTypeOfUser($connection, $mobile, $hashpw)
{
    if (!($stmt = $connection->prepare("SELECT user_type FROM tbl_registrations WHERE mobile=? AND password=?"))) {
        $msg = "Prepare failed for checking user : (" . $connection->errno . ") " . $connection->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("ss", $mobile, $hashpw))) {
        $msg = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->execute())) {
        $msg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else {

        $usertype = [];
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $usertype[0] = $data['user_type'];




        $stmt->close();

        return $usertype;
    }
}


function checkingValidity($connection, $mobile, $hashpw)
{
    if (!($stmt = $connection->prepare("SELECT valid FROM tbl_registrations WHERE mobile=? AND password=?"))) {
        $msg = "Prepare failed for checking user : (" . $connection->errno . ") " . $connection->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("ss", $mobile, $hashpw))) {
        $msg = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->execute())) {
        $msg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else {

        $validity = [];
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        $validity[0] = $data['valid'];



        $stmt->close();

        return $validity;
    }
}

function updateValidity($connection, $mobile, $hashpw)
{
    if (!($stmt = $connection->prepare("UPDATE tbl_registrations SET valid=0 WHERE mobile=? AND password=?"))) {
        $msg = "Prepare failed: (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("ss", $mobile, $hashpw))) {
        $msg = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->execute())) {
        $msg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else {
    }
    $stmt->close();
}


function gettingExpDate($connection, $mobile, $hashpw)
{
    if (!($stmt = $connection->prepare("SELECT id,exp_date FROM tbl_registrations WHERE mobile=? AND password=?"))) {
        $msg = "Prepare failed for checking user : (" . $connection->errno . ") " . $connection->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("ss", $mobile, $hashpw))) {
        $msg = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->execute())) {
        $msg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else {

        $dates = [];
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $dates[0] = $data['id'];
        $dates[1] = $data['exp_date'];



        $stmt->close();

        return $dates;
    }
}
function gettingClientDetails($mobile, $hashpw, $connection)
{
    if (!($stmt = $connection->prepare("SELECT id,full_name,user_type FROM tbl_registrations WHERE mobile=? AND password=?"))) {
        $msg = "Prepare failed for checking user : (" . $connection->errno . ") " . $connection->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("ss", $mobile, $hashpw))) {
        $msg = "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->execute())) {
        $msg = "Execute failed: (" . $stmt->errno . ") " . $stmt->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else {

        $user_data = [];
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $user_data[0] = $data['id'];
        $user_data[1] = $data['full_name'];
        $user_data[2] = $data['user_type'];


        $stmt->close();

        return $user_data;
    }
}

function creatingUniqId($data = null)
{
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);


    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);

    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);


    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
if ($connection !== null) {
    $connection->close();
}
