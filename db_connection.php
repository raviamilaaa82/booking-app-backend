<?php
// $servername = "localhost";
// $database="webiocew_business_consulting_db";
// $username="webiocew_webizcom";
// $password="y5X2*~;(h34]";

// $connection= mysqli_connect($servername,$username,$password,$database); 
// if($connection->connect_error){
//     die("Connection failed;".$connection->connect_error);
// }else{
     
// }
$server="localhost";
$database="booking_app_db";
$user_name="root";
$password="";

$connection= mysqli_connect($server,$user_name,$password,$database); 
if($connection->connect_error){
    die("Connection failed;".$connection->connect_error);
}else{
    // echo"success";
}
?>