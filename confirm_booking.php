<?php
// echo json_encode("reached here");
require_once "header.php";

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once "response.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'db_connection.php';

// echo json_encode("reached here");
$postdata = file_get_contents("php://input");
$stmt = null;
$msg_arr = [];
if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);
    
    $id = $request->bookingId;
    $status = 2;
    updateBookingData($connection,$id, $status);
}
function updateBookingData($connection, $id, $status)
{  
    if (!($stmt = $connection->prepare("UPDATE tbl_booking_master SET status=? WHERE id=?"))) {
        $msg = "Prepare failed: (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("ii",$status, $id))) {
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
        // $lastId = $stmt->insert_id;
        if(updateBookingDetails($connection, $id)){
            jsonResponse(true, null, "Booking Confirmed Successfull", "Booking confirmed Successfull", 200);
        }
        else{
            jsonResponse(false, null, "Booking confirmation Failed", "Booking Error", 200);
        }
       
    }
}

function updateBookingDetails($connection, $id) {
    $msg_arr = [];  // initialize
    $isbooked_status = 1;
    if (!($stmt = $connection->prepare("UPDATE tbl_booking_details SET is_booked=? WHERE booking_mast_id=?"))) {
        $msg = "Prepare failed: (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("ii", $isbooked_status, $id))) {
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
        $stmt->close();
        return true;
    }
}
?>