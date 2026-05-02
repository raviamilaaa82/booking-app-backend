<?php
require_once "response.php";
require_once 'db_connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateEmail($connection, $new_id, $is_allDay, $is_recurring): bool
{
  $query = "
    SELECT 
    b.event_title,
    b.booking_date,
    b.booking_price,
    CONCAT(reg.first_name, ' ', reg.last_name) AS full_name,
    reg.email,
    sp.sport_name,
    GROUP_CONCAT(ts.time_slot_name) AS timeslots
FROM tbl_booking_master b
LEFT JOIN tbl_booking_details bt 
    ON bt.booking_mast_id = b.id AND bt.status = 1
INNER JOIN tbl_registration reg
    ON reg.id = b.client_id
INNER JOIN tbl_sports sp
    ON sp.id = b.sport_id
LEFT JOIN tbl_time_slots ts
   ON ts.id = bt.time_slot
WHERE b.id = ?
GROUP BY b.id
    ";

  $stmt = $connection->prepare($query);
  $stmt->bind_param("i", $new_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $data = $result->fetch_assoc();
  $stmt->close();

  $event_title = $data['event_title'];
  $booking_date = $data['booking_date'];
  $booking_price = $data['booking_price'];
  $full_name = $data['full_name'];
  $customer_email = $data['email'];
  $sport_name = $data['sport_name'];
  $time_slots = $data['timeslots'];


  if ($data) {
    if ($is_allDay) {
      return sendMail($customer_email, $full_name, $sport_name, $booking_date, $event_title, "All Day", $booking_price);
    } else if ($is_recurring) {
      return sendMail($customer_email, $full_name, $sport_name, $booking_date, $event_title, $time_slots, $booking_price);
    } else {
      return sendMail($customer_email, $full_name, $sport_name, $booking_date, $event_title, $time_slots, $booking_price);
    }
  } else {
    return false;
  }
}

function sendMail($to_mail, $full_name, $sport, $booking_date, $booking_title, $time_slot, $booking_price): bool
{

  $mail = new PHPMailer(true);

  try {
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'amilaebaypay@gmail.com';
    $mail->Password = 'rptg kdkc eydm tuju';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    //Recipients
    $mail->setFrom('amilaebaypay@gmail.com', 'Zone');
    $mail->addAddress($to_mail, '');     //Add a recipient
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Booking Request Made';
    $mail->Body = "
<html>

<head>
  <title></title>
</head>

<body style='margin:0;padding:0;background-color:#0f0f0f;font-family:'Outfit',Helvetica,Arial,sans-serif;'>
  <div style='display:none;max-height:0;overflow:hidden;font-size:1px;color:#0f0f0f;'>
    We’ve received your booking request.
  </div>

  <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color:#0f0f0f;'>
    <tr>
      <td align='center' style='padding:40px 16px;'>

        <table role='presentation' class='email-wrapper' border='0' cellpadding='0' cellspacing='0' width='600' style='max-width:600px;width:100%;background-color:#111111;border:1px solid rgba(46,204,113,0.2);'>


          <tr>
            <td style='background-color:#0a0a0a;padding:28px 40px;border-bottom:3px solid #2ECC71;'>
              <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                  <td>
                    <span style='font-size:26px;font-weight:700;
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
                       padding:20px 40px;text-align:center;border-bottom:1px solid rgba(46,204,113,0.2);'>
                    <div style='font-size:52px;margin-bottom:16px;'>🥎</div>

                    <h1 style='font-size:20px;font-weight:700;
                         letter-spacing:3px;color:#ffffff;margin:0 0 10px 0;line-height:1.1;
                         text-transform:uppercase;'>
                      Your booking is under review</h1>
                  </td>
                </tr>
              </table>
            </td>
          </tr>


          <tr>
            <td style='padding:40px 40px 32px;'>

              <p style='font-size:16px;color:#f5f5f5;line-height:1.7;margin:0 0 16px 0;'>
                Hey <strong style='color:#2ECC71;'>$full_name</strong>,
              </p>

              <p style='font-size:15px;color:#aaaaaa;line-height:1.8;margin:0 0 32px 0;'>
                Thank you for choosing <strong style='color:#f5f5f5;'>Sports Zone</strong>! We’ve successfully 
                received your booking request for $sport on $booking_date.
              </p>

               <p style='font-size:15px;color:#aaaaaa;line-height:1.8;margin:0 0 32px 0;'>
               <strong style='color:#f5f5f5;'>What happens next?</strong><br>
Our team is currently reviewing the details to ensure everything is perfect. 
While you wait for your formal confirmation email, there is one more step to complete:</p>
 <p style='font-size:15px;color:#aaaaaa;line-height:1.8;margin:0 0 32px 0;'>To secure your spot and prevent it from being released to other customers, please complete your payment.
              </p>


              <p style='font-size:12px;color:#f5f5f5;line-height:1.7;margin:0 0 16px 0;'>
                <strong style='color:#2ECC71;text-transform:uppercase;'>Booking Details</strong>
              </p>
              <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='background-color:#181818;border:1px solid rgba(46,204,113,0.2);
                      border-left:3px solid #2ECC71;margin-bottom:32px;'>
                <tr>
                  <td style='padding:24px 28px;'>

                    <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:10px;'>
                      <tr>
                        <td width='40%' style='font-size:11px;
                                         letter-spacing:1px;text-transform:uppercase;color:#2ECC71;'>
                          Booking Name
                        </td>
                        <td style='font-size:14px;color:#aaaaaa;'>
                          $booking_title
                        </td>
                      </tr>
                    </table>

                    <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:10px;'>
                      <tr>
                        <td width='40%' style='font-size:11px;
                                         letter-spacing:1px;text-transform:uppercase;color:#2ECC71;'>
                          Date
                        </td>
                        <td style='font-size:14px;color:#aaaaaa;'>$booking_date</td>
                      </tr>
                    </table>

                    <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:10px;'>
                      <tr>
                        <td width='40%' style='font-size:11px;
                                         letter-spacing:1px;text-transform:uppercase;color:#2ECC71;'>
                          Sport 
                        </td>
                        <td style='font-size:14px;color:#aaaaaa;'>$sport</td>
                      </tr>
                    </table>

                    <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:10px;'>
                      <tr>
                        <td width='40%' style='font-size:11px;
                                         letter-spacing:1px;text-transform:uppercase;color:#2ECC71;'>
                          Time Slot
                        </td>
                        <td style='font-size:14px;color:#aaaaaa;'>$time_slot</td>
                      </tr>
                    </table>

                    <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
                      <tr>
                        <td width='40%' style='font-size:11px;
                                         letter-spacing:1px;text-transform:uppercase;color:#2ECC71;'>
                          Total Payment
                        </td>
                        <td style='font-size:14px;color:#aaaaaa;'>$booking_price</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
              <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%' style='margin-bottom:32px;'>
                <tr>
                  <td style='border-top:1px solid rgba(46,204,113,0.15);font-size:0;line-height:0;'>&nbsp;</td>
                </tr>
                <tr>
                  <td>
                    <p style='font-size:15px;color:#aaaaaa;line-height:1.8;margin:0 0 32px 0;margin-top:10px'>
                      <strong style='color:#f5f5f5;'>Need help?</strong><br>
If you have any questions or need to make a change to your request, simply call us at  <strong style='color:#f5f5f5;'>077-777-7777</strong>. <br>

Once your payment is processed and our team gives the green light, we’ll send over your official booking confirmation.</p>
                  </td>
                  </tr>
              </table> 
            </td>
          </tr>
         
          <tr>
            <td style='background-color:#0a0a0a;padding:28px 40px;
                 border-top:1px solid rgba(46,204,113,0.15);
                 border-bottom:1px solid rgba(46,204,113,0.15);'>
              <table role='presentation' border='0' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                  <td align='center' width='33%' style='padding:0 8px;text-align:center;'>
                    <div style='font-size:22px;margin-bottom:6px;'>⚡</div>
                    <p style='font-size:10px;letter-spacing:1px;
                        text-transform:uppercase;color:#2ECC71;margin:0 0 4px 0;'>
                      Instant
                    </p>
                    <p style='font-size:12px;color:#ffffff;margin:0;'>Confirmed in seconds</p>
                  </td>
                  <td align='center' width='33%' style='padding:0 8px;text-align:center;
                       border-left:1px solid rgba(46,204,113,0.15);
                       border-right:1px solid rgba(46,204,113,0.15);'>
                    <div style='font-size:22px;margin-bottom:6px;'>📅</div>
                    <p style='font-size:10px;letter-spacing:1px;
                        text-transform:uppercase;color:#2ECC71;margin:0 0 4px 0;'>
                      Flexible
                    </p>
                    <p style='font-size:12px;color:#ffffff;margin:0;'>Book any time, any slot</p>
                  </td>
                  <td align='center' width='33%' style='padding:0 8px;text-align:center;'>
                    <div style='font-size:22px;margin-bottom:6px;'>💡</div>
                    <p style='font-size:10px;letter-spacing:1px;
                        text-transform:uppercase;color:#2ECC71;margin:0 0 4px 0;'>
                      Floodlit
                    </p>
                    <p style='font-size:12px;color:#ffffff;margin:0;'>Play day or night</p>
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

      </td>
    </tr>
  </table>

</body>

</html>

";

    $mail->send();
    return true;
  } catch (Exception $e) {
    return false;
  }


}

?>