<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost:3000");
// header("Access-Control-Allow-Origin: https://millioncliq.com");
header("Access-Control-Allow-Methods: OPTIONS,POST,GET");
header("Access-Control-Allow-Headers:Origin, X-Api-Key, X-Requested-With, Content-Type, Accept, Authorization");
header("Access-Control-Allow-Credentials:true");
require_once 'db_connection.php';

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);

    $email = $request->email;
   
    $msg_arr = [];
    $user_data = [];



    gettingEmailAndUserID($connection, $email);
}



function gettingEmailAndUserID($connection, $email)
{
    if (!($stmt = $connection->prepare("SELECT id,email FROM tbl_registration WHERE email=?"))) {
        $msg = "Prepare failed for checking user : (" . $connection->errno . ") " . $connection->error;

        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("s", $email))) {
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
        if  ($result !== null && $result->num_rows > 0){
        $data = $result->fetch_assoc();
       
        $msg_arr[0]['identify'] = 'success';
        $msg_arr[1]['id'] = $data['id'];
        $msg_arr[2]['email'] = $data['email'];
       
    }else{
        $msg ="Can not find the email!";
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
    }


        $stmt->close();
        
        echo json_encode($msg_arr);
    }
}
$connection->close();
?>