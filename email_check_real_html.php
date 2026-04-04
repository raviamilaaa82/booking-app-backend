<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST,GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials:true");
//https://www.millioncliq.com
//http://localhost:3000

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';




require_once 'db_connection.php';

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);

    $email = $request->email;
    // $userpassword = $request->userpassword;
    $msg_arr = [];
    $user_data = [];
    if (checkingEmail($connection, $email) > 0) {
        $msg = "Email is already used";
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else {
        createRandomNumAndSaveDb($connection, $email);
    }
}

function checkingEmail($connection, $email)
{

    if (!($stmt = $connection->prepare("SELECT COUNT(*) as check_user_count FROM tbl_registration WHERE email=? "))) {
        $msg = "Prepare failed for checking user : (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("s", $email))) {
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
        $email_result = $stmt->get_result();
        $email_count = $email_result->fetch_assoc();

        $user_email_count = $email_count['check_user_count'];
    }
    if ($stmt !== null) {
        $stmt->close();
    }


    return $user_email_count;
}


function createRandomNumAndSaveDb($connection, $email)
{
    $rnum = rand(100000, 999999);
    if (!($stmt1 = $connection->prepare("INSERT INTO tbl_temp_email (email,random_num) VALUES (?,?)"))) {
        $msg = "Prepare failed: (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt1->bind_param("si", $email, $rnum))) {
        $msg = "Binding parameters failed: (" . $stmt1->errno . ") " . $stmt1->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt1->execute())) {
        $msg = "Execute failed: (" . $stmt1->errno . ") " . $stmt1->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else {
        sendMail($rnum, $email);
       
    }
    if ($stmt1 !== null) {
        $stmt1->close();
    }

    $connection->close();
}



function sendMail($code, $to_mail)
{

$mail = new PHPMailer(true);

try {
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            
    $mail->Host       = 'smtp.gmail.com';                     
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'amilaebaypay@gmail.com';                     
    $mail->Password   = 'rptg kdkc eydm tuju';                               
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           
    $mail->Port       = 587;                                 

    //Recipients
    $mail->setFrom('amilaebaypay@gmail.com', 'Zone');
    $mail->addAddress($to_mail, '');     //Add a recipient
    // $mail->addAddress('ellen@example.com');               //Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    // $imageUrl = "http://localhost:3000/images/mqq.png";
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Email Verification';
    $mail->Body    = "<html>
<head>
    <title></title>
</head>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600&display=swap');
</style>

<body style='margin:0;padding:0;background-color:#0f0f0f;font-family:'Outfit',Helvetica,Arial,sans-serif;'>
<div style='display:none;max-height:0;overflow:hidden;font-size:1px;color:#0f0f0f;'>
  Sports Zone - Email Verification Code.
</div>

<table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color:#0f0f0f;'>
<tr><td align='center' style='padding:40px 16px;'>

  <table role='presentation' class='email-wrapper' border='0' cellpadding='0' cellspacing='0' width='600'
         style='max-width:600px;width:100%;background-color:#111111;border:1px solid rgba(46,204,113,0.2);'>

    
    <tr>
      <td style='background-color:#0a0a0a;padding:28px 40px;border-bottom:3px solid #2ECC71;'>
        <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
          <tr>
            <td>
              <span style='font-family:'Outfit';font-size:26px;font-weight:700;
                           letter-spacing:4px;color:#ffffff;text-decoration:none;'>
                Sports<span style='color:#2ECC71;'>Zone</span>
              </span>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    
    <tr>
      <td style='background-color:#0d2b1a;padding:0;overflow:hidden;'>
        <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
          <tr>
            <td style='background:linear-gradient(135deg,#0d2b1a 0%,#1a3d2b 50%,#0d2b1a 100%);
                       padding:50px 40px;text-align:center;border-bottom:1px solid rgba(46,204,113,0.2);'>
              <div style='font-size:52px;margin-bottom:16px;'>🥎</div>

              <h1 style='font-family:'Outfit';font-size:34px;font-weight:700;
                         letter-spacing:3px;color:#ffffff;margin:0 0 10px 0;line-height:1.1;'>
                Your Verification Code: <br><span style='color:#2ECC71;font-size:18px;'>$code</span>
              </h1>

              <p style='font-family:'Outfit';font-size:12px;letter-spacing:2px;
                        text-transform:uppercase;color:#ffffff;margin:0;'>
                This code can only be used once. It expires in 15 minutes.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td style='background-color:#0a0a0a;padding:28px 40px;'>
        <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
          <tr>
            <td align='center'>
              <p style='font-size:11px;color:#ffffff;line-height:1.7;margin:0 0 8px 0;'>
                ©2026 Sports Zone. All rights reserved.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

  </table>

</td></tr>
</table>

</body>
</html>

";
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
   
     $msg = "The email has been sent. Please check your inbox.";
    $msg_arr[0]['identify'] = 'success';
    $msg_arr[1]['msg'] = $msg;
    echo json_encode($msg_arr);
} catch (Exception $e) {
    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    $msg = "Something wrong with email address try again";
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;

        echo json_encode($msg_arr);
}


}
