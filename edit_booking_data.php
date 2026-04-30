<?php

require_once "header.php";
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require_once "response.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'db_connection.php';

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);
    
    $id = $request->id;
    $title = $request->title;
    $sport_id = $request->sport_id;
    $client_id=$request->client_id;
    $is_booked= false;
    $booking_date = $request->booking_date;
    $is_recurr_event = $request->is_recurr_event;
    $is_all_day = $request->is_all_day;
    $status=$request->status;
    $time_slots = $request->timeSlots; 
    $colors=$request->color; 
    $booking_price = $request->booking_price;

    $current_date = date("Y-m-d");
 
    $stmt = null;
    $statement = null;
    $msg_arr = [];
    

    $response = false;


    if (boolval($is_all_day) == true && boolval($is_recurr_event) == false) {
        $response = updateBookingData($connection,$id, $title,$sport_id, $client_id,$current_date, $booking_date, $is_recurr_event,$is_all_day,$status,$colors,$time_slots,$is_booked, $booking_price);
    } else {
        $response = updateBookingData($connection,$id, $title,$sport_id, $client_id,$current_date, $booking_date, $is_recurr_event,$is_all_day,$status,$colors,$time_slots,$is_booked, $booking_price);
    }

    if ($response == true) {
        jsonResponse(true, null, "Booking Update Successfull", "Booking Updated Successfull", 200);
    } else {
        jsonResponse(false, null, "Booking updation Failed", "Booking Error", 200);
    }
}

function updateBookingData($connection, $id, $title,$sport_id, $client_id,$current_date, $booking_date, $is_recurr_event,$is_all_day,$status,$colors,$time_slots,$is_booked, $booking_price)
{
    if (!($stmt = $connection->prepare("UPDATE tbl_booking_master SET event_title=?, sport_id=?, client_id=?, date=?, booking_date=?, is_recurr_event=?, is_all_day=?,status=?,colors=?, booking_price=? WHERE id=?"))) {
        $msg = "Prepare failed: (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("siissiiisii", $title,$sport_id, $client_id,$current_date, $booking_date, $is_recurr_event,$is_all_day,$status,$colors, $booking_price, $id))) {
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
        if(boolval($is_all_day) == true) {
            return true;
        }
        else if(updateBookingDetails($connection, $id,$time_slots,$is_booked)){
            return true;
        }
        else{
            return false;
        }
       
    }
}

function updateBookingDetails($connection, $id, $time_slots,$is_booked) {
    $msg_arr = [];  // initialize
    $delete_status = 3;
    ///delete the existing detail data before inserting the new detail data 
    if (!($stmt = $connection->prepare("UPDATE tbl_booking_details SET status=? WHERE booking_mast_id=?"))) {
        $msg = "Prepare failed: (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("ii", $delete_status, $id))) {
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

    for ($i = 0; $i < count($time_slots); $i++) {
        $active_status = 1;
        $stmt = $connection->prepare("INSERT INTO `tbl_booking_details`(`time_slot`,`booking_mast_id`,`is_booked`, `status`) VALUES (?,?,?,?)");
        if (!$stmt) {
            $msg_arr[0]['identify'] = 'error';
            $msg_arr[1]['msg'] = "Prepare failed: " . $connection->error;
            echo json_encode($msg_arr);
            return false;  // stop on error
        }
        if (!$stmt->bind_param("iiii", $time_slots[$i],$id, $is_booked, $active_status)) {
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
    return true;

    }
}
