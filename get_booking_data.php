<?php
require_once "header.php";
require_once 'db_connection.php';
require_once "response.php";


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


// require_once "SessionCheck.php";


$msg_arr = [];

$stmt = $connection->prepare("
    SELECT 
        b.id AS booking_id,
        b.event_title,
        b.sport_id,
        b.client_id,
        b.booking_date,
        b.is_recurr_event,
        b.is_all_day,
        b.status,
        b.colors,
        CONCAT(reg.first_name, ' ', reg.last_name) AS full_name,
        reg.phone,
        GROUP_CONCAT(bt.time_slot) AS timeslots,
        sp.sport_name,
        b.booking_price
    FROM tbl_booking_master b
    LEFT JOIN tbl_booking_details bt 
        ON bt.booking_mast_id = b.id
    INNER JOIN tbl_registration reg
        ON reg.id= b.client_id
    INNER JOIN tbl_sports sp
        ON sp.id = b.sport_id
    WHERE bt.status = 1 AND b.status IN (1,2)          
    GROUP BY b.id, b.client_id
");

if (!($stmt)) {
    $msg = "Prepare failed for getting announcemnt user : (" . $connection->errno . ") " . $connection->error;
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
    // echo $result;
    $cr = 0;
    if ($result !== null && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $msg_arr[$cr]['BookingId'] = $row['booking_id'];
            $msg_arr[$cr]['eventTitle'] = $row['event_title'];
            $msg_arr[$cr]['sport_id'] = $row['sport_id'];
            $msg_arr[$cr]['client_id'] = $row['client_id'];
            $msg_arr[$cr]['booking_date'] = $row['booking_date'];
            $msg_arr[$cr]['is_recurr_event'] = boolval($row['is_recurr_event']);
            $msg_arr[$cr]['is_all_day'] = boolval($row['is_all_day']);
            $msg_arr[$cr]['status'] = $row['status'];
            $msg_arr[$cr]['colors'] = $row['colors'];
            $msg_arr[$cr]['full_name'] = $row['full_name'];
            $msg_arr[$cr]['phone'] = $row['phone'];
            $msg_arr[$cr]['timeslots'] = explode(',', $row['timeslots']);
            $msg_arr[$cr]['sport_name'] = $row['sport_name'];
            $msg_arr[$cr]['booking_price'] = $row['booking_price'];
            $cr++;
        }
    } else {
        $msg_arr[0]['identify'] = 'error';
        jsonResponse(false, null, null,"fetch error", 200);
    }
    

    jsonResponse(true, $msg_arr, null,"Booking Data", 200);
    $stmt->close();
    $connection->close();
}
?>