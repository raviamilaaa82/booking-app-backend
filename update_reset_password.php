<?php
require_once "header.php";
require_once 'db_connection.php';

$postdata = file_get_contents("php://input");

if (isset($postdata) && !empty($postdata)) {
    $request = json_decode($postdata);

    $userid = $request->userid;

    $temppassword = $request->password;
    $useremail = $request->email;

    $userpassword = password_hash($temppassword, PASSWORD_DEFAULT);
    global $msg;
    $user_email_count = 0;
    $user_pw_count = 0;
    $stmt = null;
    $statement = null;
    $msg_arr = [];



    updatePassword($connection, $userid, $userpassword, $useremail, $temppassword);
}








function updatePassword($connection, $userid, $userpassword, $useremail, $temppassword)
{

    if (!($stmt = $connection->prepare("UPDATE tbl_registration SET password=? WHERE id=?"))) {
        $msg = "Prepare failed: (" . $connection->errno . ") " . $connection->error;
        $msg_arr[0]['identify'] = 'error';
        $msg_arr[1]['msg'] = $msg;
        echo json_encode($msg_arr);
    } else if (!($stmt->bind_param("si", $userpassword, $userid))) {
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

        $msg = "Your password has been updated.";


        $msg_arr[0]['identify'] = 'success';
        $msg_arr[1]['msg'] = $msg;

        echo json_encode($msg_arr);
    }
    $stmt->close();
}


// function sendMail($first_name,$last_name,$email,$reg_date,$connection)
// {
      


//     $mail = new PHPMailer(true);


//       $confirm_url   = 'https://pitchdrop.com/verify';
//         $login_url     ='https://pitchdrop.com/login';
//         $support_email = 'support@pitchdrop.com';
//         $site_url      = 'https://pitchdrop.com';

// try {
//     //Server settings
//     // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
//     $mail->isSMTP();                                            //Send using SMTP
//     $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
//     $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
//     $mail->Username   = 'amilaebaypay@gmail.com';                     //SMTP username
//     $mail->Password   = 'rptg kdkc eydm tuju';                               //SMTP password
//     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
//     $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

//     //Recipients
//     $mail->setFrom('amilaebaypay@gmail.com', 'SPORTS ZONE');
//     $mail->addAddress($email, '');     //Add a recipient
//     // $mail->addAddress('ellen@example.com');               //Name is optional
//     // $mail->addReplyTo('info@example.com', 'Information');
//     // $mail->addCC('cc@example.com');
//     // $mail->addBCC('bcc@example.com');

//     //Attachments
//     // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
//     // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

//     //Content
//     // $imageUrl = "http://localhost:3000/images/mqq.png";
//     $mail->isHTML(true);                                  //Set email format to HTML
//     $mail->Subject = 'Registration Verification';
//     // $mail->isHTML(true);
//     $mail->Body    = " 
// <html>

// <head>
//   <title></title>
// </head>

// <body style='margin:0;padding:0;background-color:#0f0f0f;font-family:'Outfit',Helvetica,Arial,sans-serif;'>
//   <div style='display:none;max-height:0;overflow:hidden;font-size:1px;color:#0f0f0f;'>
//     Welcome to Sports Zone! Your account is ready — let's play ball.
//   </div>

//   <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color:#0f0f0f;'>
//     <tr>
//       <td align='center' style='padding:40px 16px;'>

//         <table role='presentation' class='email-wrapper' border='0' cellpadding='0' cellspacing='0' width='600' style='max-width:600px;width:100%;background-color:#111111;border:1px solid rgba(46,204,113,0.2);'>


//           <tr>
//             <td style='background-color:#0a0a0a;padding:28px 40px;border-bottom:3px solid #2ECC71;'>
//               <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
//                 <tr>
//                   <td>
//                     <span style='font-size:26px;font-weight:700;
//                            letter-spacing:4px;color:#ffffff;text-decoration:none;'>
//                       Sports<span style='color:#2ECC71;'>Zone</span>
//                     </span>
//                   </td>
//                 </tr>
//               </table>
//             </td>
//           </tr>

//           <tr>
//             <td style='background-color:#0d2b1a;padding:0;overflow:hidden;'>
//               <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
//                 <tr>
//                   <td style='background:linear-gradient(135deg,#0d2b1a 0%,#1a3d2b 50%,#0d2b1a 100%);
//                        padding:50px 40px;text-align:center;border-bottom:1px solid rgba(46,204,113,0.2);'>
//                     <div style='font-size:52px;margin-bottom:16px;'>🥎</div>

//                     <h1 style='font-size:44px;font-weight:700;
//                          letter-spacing:3px;color:#ffffff;margin:0 0 10px 0;line-height:1.1;
//                          text-transform:uppercase;'>
//                       YOU'RE IN,<br><span style='color:#2ECC71;'>$first_name</span>
//                     </h1>

//                     <p style='font-size:12px;letter-spacing:2px;
//                         text-transform:uppercase;color:#ffffff;margin:0;'>
//                       Account successfully created !
//                     </p>
//                   </td>
//                 </tr>
//               </table>
//             </td>
//           </tr>


//           <tr>
//             <td style='padding:40px 40px 32px;'>

//               <p style='font-size:16px;color:#f5f5f5;line-height:1.7;margin:0 0 16px 0;'>
//                 Hey <strong style='color:#2ECC71;'>$first_name $last_name</strong>,
//               </p>

//               <p style='font-size:15px;color:#aaaaaa;line-height:1.8;margin:0 0 32px 0;'>
//                 Welcome to <strong style='color:#f5f5f5;'>Sports Zone</strong> — the fastest way to book premium
//                 softball pitches. Your account is live and ready to go.
//               </p>

//               <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color:#181818;border:1px solid rgba(46,204,113,0.2);
//                       border-left:3px solid #2ECC71;margin-bottom:32px;'>
//                 <tr>
//                   <td style='padding:24px 28px;'>

//                     <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:10px;'>
//                       <tr>
//                         <td width='40%' style='font-size:11px;
//                                          letter-spacing:1px;text-transform:uppercase;color:#2ECC71;'>
//                           Full Name
//                         </td>
//                         <td style='font-size:14px;color:#f5f5f5;font-weight:600;'>
//                           $first_name $last_name
//                         </td>
//                       </tr>
//                     </table>

//                     <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:10px;'>
//                       <tr>
//                         <td width='40%' style='font-size:11px;
//                                          letter-spacing:1px;text-transform:uppercase;color:#2ECC71;'>
//                           Email
//                         </td>
//                         <td style='font-size:14px;color:#2ECC71;'>$email</td>
//                       </tr>
//                     </table>

//                     <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
//                       <tr>
//                         <td width='40%' style='font-size:11px;
//                                          letter-spacing:1px;text-transform:uppercase;color:#2ECC71;'>
//                           Registered
//                         </td>
//                         <td style='font-size:14px;color:#aaaaaa;'>$reg_date</td>
//                       </tr>
//                     </table>

//                   </td>
//                 </tr>
//               </table>
//               <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:32px;'>
//                 <tr>
//                   <td style='border-top:1px solid rgba(46,204,113,0.15);font-size:0;line-height:0;'>&nbsp;</td>
//                 </tr>
//               </table>
//             </td>
//           </tr>
//           <tr>
//             <td style='background-color:#0a0a0a;padding:28px 40px;
//                  border-top:1px solid rgba(46,204,113,0.15);
//                  border-bottom:1px solid rgba(46,204,113,0.15);'>
//               <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
//                 <tr>
//                   <td align='center' width='33%' style='padding:0 8px;text-align:center;'>
//                     <div style='font-size:22px;margin-bottom:6px;'>⚡</div>
//                     <p style='font-size:10px;letter-spacing:1px;
//                         text-transform:uppercase;color:#2ECC71;margin:0 0 4px 0;'>
//                       Instant
//                     </p>
//                     <p style='font-size:12px;color:#ffffff;margin:0;'>Confirmed in seconds</p>
//                   </td>
//                   <td align='center' width='33%' style='padding:0 8px;text-align:center;
//                        border-left:1px solid rgba(46,204,113,0.15);
//                        border-right:1px solid rgba(46,204,113,0.15);'>
//                     <div style='font-size:22px;margin-bottom:6px;'>📅</div>
//                     <p style='font-size:10px;letter-spacing:1px;
//                         text-transform:uppercase;color:#2ECC71;margin:0 0 4px 0;'>
//                       Flexible
//                     </p>
//                     <p style='font-size:12px;color:#ffffff;margin:0;'>Book any time, any slot</p>
//                   </td>
//                   <td align='center' width='33%' style='padding:0 8px;text-align:center;'>
//                     <div style='font-size:22px;margin-bottom:6px;'>💡</div>
//                     <p style='font-size:10px;letter-spacing:1px;
//                         text-transform:uppercase;color:#2ECC71;margin:0 0 4px 0;'>
//                       Floodlit
//                     </p>
//                     <p style='font-size:12px;color:#ffffff;margin:0;'>Play day or night</p>
//                   </td>
//                 </tr>
//               </table>
//             </td>
//           </tr>

//           <tr>
//             <td style='background-color:#0a0a0a;padding:28px 40px;'>
//               <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
//                 <tr>
//                   <td align='center'>
//                     <p style='font-size:11px;color:#ffffff;line-height:1.7;margin:0 0 8px 0;'>
//                       ©2026 Sports Zone. All rights reserved.
//                     </p>
//                   </td>
//                 </tr>
//               </table>
//             </td>
//           </tr>

//         </table>

//       </td>
//     </tr>
//   </table>

// </body>

// </html>
//     ";




//     // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

//     $mail->send();
   
//     //  $msg = "The email has been sent. Please check your inbox.";
//     // $msg_arr[0]['identify'] = 'success';
//     // $msg_arr[1]['msg'] = $msg;
//     // echo json_encode($msg_arr);
//     jsonResponse(true, null, "The email has been sent. Please check your inbox.", "", 200);
// } catch (Exception $e) {
//     // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
//         //  $msg = $e;
//         // $msg_arr[0]['identify'] = 'error';
//         // $msg_arr[1]['msg'] = $msg;

//         // echo json_encode($msg_arr);
//         jsonResponse(false, null, $e, "Error", 200);
// }

    
// }

if ($stmt !== null) {
    $stmt->close();
}


$connection->close();
