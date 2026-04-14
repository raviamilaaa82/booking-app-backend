<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST,GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials:true");


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once "response.php";


require_once 'db_connection.php';

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);

 
    
    $title = $request->title;
     $sport_id = $request->sport_id;
     $client_id=$request->client_id;
    $date = $request->date;
    $is_booked= false;
    $booking_date = $request->booking_date;
    $is_recurr_event = $request->is_recurr_event;
    $is_all_day = $request->is_all_day;
    $status=$request->status;
    $time_slots = $request->timeSlots; 
    $colors=$request->color; 

  
     $current_date = date_create()->format('Y-m-d H:i:s');
    //  $current_date = date("Y-m-d");
 
    $stmt = null;
    $statement = null;
    $msg_arr = [];
    


         insertBookingData($connection,$title,$sport_id, $client_id,$current_date, $booking_date, $is_recurr_event,$is_all_day,$status,$colors,$time_slots,$is_booked);
    
}
function insertBookingData($connection, $title,$sport_id, $client_id,$current_date, $booking_date, $is_recurr_event,$is_all_day,$status,$colors,$time_slots,$is_booked)
{

    if (!($stmt = $connection->prepare("INSERT INTO tbl_booking_master (event_title, sport_id, client_id, date, booking_date, is_recurr_event, is_all_day,status,colors) VALUES (?,?,?,?,?,?,?,?,?)"))) {
        $msg = "Prepare failed: (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("siissiiis", $title,$sport_id, $client_id,$current_date, $booking_date, $is_recurr_event,$is_all_day,$status,$colors))) {
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
             $lastId = $stmt->insert_id;
   
             insertBookingDetails($connection, $lastId,$time_slots,$is_booked);
       
    }
}

function insertBookingDetails($connection, $lastId, $time_slots,$is_booked) {
    $msg_arr = [];  // initialize
    for ($i = 0; $i < count($time_slots); $i++) {

        $stmt = $connection->prepare("INSERT INTO `tbl_booking_details`(`time_slot`,`booking_mast_id`,`is_booked`) VALUES (?,?,?)");
        if (!$stmt) {
            $msg_arr[0]['identify'] = 'error';
            $msg_arr[1]['msg'] = "Prepare failed: " . $connection->error;
            echo json_encode($msg_arr);
            return false;  // stop on error
        }
        if (!$stmt->bind_param("iii", $time_slots[$i], $lastId,$is_booked)) {
            $msg_arr[0]['identify'] = 'error';
            $msg_arr[1]['msg'] = "Binding failed: " . $stmt->error;
            echo json_encode($msg_arr);
            return false;
        }
        if (!$stmt->execute()) {
            $msg_arr[0]['identify'] = 'error';
            $msg_arr[1]['msg'] = "Execute failed: " . $stmt->error;
            echo json_encode($msg_arr);
            return false;
        }
        $stmt->close();
    }
    return true;  // all inserted successfully
}
