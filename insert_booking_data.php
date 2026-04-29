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

    $title = $request->title;
    $sport_id = $request->sport_id;
    $client_id = $request->client_id;
    $is_booked = false;
    $booking_date = $request->booking_date;
    $is_recurr_event = $request->is_recurr_event;
    $is_all_day = $request->is_all_day;
    $status = $request->status;
    $time_slots = $request->timeSlots;
    $colors = $request->color;
    $booking_price = $request->booking_price;
    $recurringtDates = $request->recurringDates;

    $current_date = date("Y-m-d");

    $stmt = null;
    $statement = null;
    $msg_arr = [];
    $response = false;


    if (boolval($is_all_day) == true && boolval($is_recurr_event) == false) {
        $response = insertBookingData($connection, $title, $sport_id, $client_id, $current_date, $booking_date, $is_recurr_event, $is_all_day, $status, $colors, $time_slots, $is_booked, $booking_price);
    } elseif (boolval($is_recurr_event) == true && boolval($is_all_day) == false) {
        $response = recurringEvents($connection, $title, $sport_id, $client_id, $current_date, $is_recurr_event, $is_all_day, $status, $colors, $time_slots, $is_booked, $booking_price, $recurringtDates);
    } elseif (boolval($is_recurr_event) == true && boolval($is_all_day) == true) {
        echo json_encode("all day event recurring !");
    } else {
        $response = insertBookingData($connection, $title, $sport_id, $client_id, $current_date, $booking_date, $is_recurr_event, $is_all_day, $status, $colors, $time_slots, $is_booked, $booking_price);
    }
    if ($response == true) {
        jsonResponse(true, null, "Booking Successfull", "Booking Successfull", 200);
    } else {
        jsonResponse(false, null, "Booking creation Failed", "Booking Error", 200);
    }


}
function insertBookingData($connection, $title, $sport_id, $client_id, $current_date, $booking_date, $is_recurr_event, $is_all_day, $status, $colors, $time_slots, $is_booked, $booking_price)
{
    if (!($stmt = $connection->prepare("INSERT INTO tbl_booking_master (event_title, sport_id, client_id, date, booking_date, is_recurr_event, is_all_day,status,colors, booking_price) VALUES (?,?,?,?,?,?,?,?,?,?)"))) {
        $msg = "Prepare failed: (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("siissiiisi", $title, $sport_id, $client_id, $current_date, $booking_date, $is_recurr_event, $is_all_day, $status, $colors, $booking_price))) {
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
        if($lastId && boolval($is_all_day) == true) {
            return true;
        }
        else if(insertBookingDetails($connection, $lastId, $time_slots, $is_booked)) {
            return true;
        } else {
            return false;
        }

    }
}

function insertBookingDetails($connection, $lastId, $time_slots, $is_booked)
{
    $msg_arr = [];  // initialize
    for ($i = 0; $i < count($time_slots); $i++) {

        $stmt = $connection->prepare("INSERT INTO `tbl_booking_details`(`time_slot`,`booking_mast_id`,`is_booked`) VALUES (?,?,?)");
        if (!$stmt) {
            $msg_arr[0]['identify'] = 'error';
            $msg_arr[1]['msg'] = "Prepare failed: " . $connection->error;
            echo json_encode($msg_arr);
            return false;  // stop on error
        }
        if (!$stmt->bind_param("iii", $time_slots[$i], $lastId, $is_booked)) {
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


function recurringEvents($connection, $title, $sport_id, $client_id, $current_date, $is_recurr_event, $is_all_day, $status, $colors, $time_slots, $is_booked, $booking_price, $recurringtDates): bool
{
    $responseCount = 0;
    for ($i = 0; $i < count($recurringtDates); $i++) {
        $result = insertBookingData($connection, $title, $sport_id, $client_id, $current_date, $recurringtDates[$i], $is_recurr_event, $is_all_day, $status, $colors, $time_slots, $is_booked, $booking_price);
        if ($result == true) {
            $responseCount++;
        }
    }

    if (count($recurringtDates) === $responseCount) {
        return true;
    } else {
        return false;
    }
}
