<?php
require_once "header.php";
require_once 'db_connection.php';
require_once "response.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$date = $_GET['date'] ?? null;

if (!$date) {
    echo $date;
    echo json_encode(["error" => "Date is required"]);
    exit;
}

$stmt = $connection->prepare("
    SELECT 
    sm.id AS slot_id,
    sm.time_slot_name,
    sm.time_period,
    CASE 
        WHEN COUNT(b.id) > 0 THEN 1 
        ELSE 0 
    END AS isbooked
FROM tbl_time_slots sm
LEFT JOIN tbl_booking_details bs 
    ON sm.id = bs.time_slot
    AND bs.status = 1
LEFT JOIN tbl_booking_master b 
    ON b.id = bs.booking_mast_id 
    AND b.booking_date = ?
GROUP BY sm.id, sm.time_slot_name, sm.time_period
ORDER BY sm.id;
");



$msg_arr = [];
if (!($stmt)) {
    $msg = "Prepare failed for getting announcemnt user : (" . $connection->errno . ") " . $connection->error;
    $msg_arr[0]['identify'] = 'error';
    $msg_arr[1]['msg'] = $msg;
    echo json_encode($msg_arr);
} else if (!($stmt->bind_param("s",$date))) {
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
    // $data = $result->fetch_assoc();
    $cr = 0;
    if ($result !== null && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $msg_arr[$cr]['id'] = $row['slot_id'];
            $msg_arr[$cr]['timeSlotName'] = $row['time_slot_name'];
            $msg_arr[$cr]['timePeriod'] = $row['time_period'];
            $msg_arr[$cr]['isbooked'] = boolval($row['isbooked']);
            $cr++;
        }
    } else {
        $msg_arr[0]['identify'] = 'error';
        jsonResponse(false, null, null,"fetch error", 200);
    }
    // echo json_encode($msg_arr);
    jsonResponse(true, $msg_arr, null,"Timeslot data", 200);
    $stmt->close();
    $connection->close();
}
?>