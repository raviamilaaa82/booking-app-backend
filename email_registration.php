<?php

function renderRegistrationEmail(array $data): string
{
    // Merge defaults so every variable is always defined
    $d = array_merge([
        'first_name'    => 'Player',
        'last_name'     => '',
        'email'         => 'chamila.ranansinghe@gmail.com',
        'registered_at' => date('j F Y \a\t g:i A'),
        'confirm_url'   => 'https://pitchdrop.com/verify',
        'login_url'     => 'https://pitchdrop.com/login',
        'support_email' => 'support@pitchdrop.com',
        'site_url'      => 'https://pitchdrop.com',
    ], $data);

    // Sanitise for HTML output
    foreach ($d as $k => $v) {
        $d[$k] = htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }

    ob_start(); ?>
< html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <title>Welcome to Sports Zone</title>

  <style>
    /* Reset */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
    body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #0f0f0f; }
    /* Mobile */
    @media screen and (max-width: 600px) {
      .email-wrapper { width: 100% !important; }
      .email-body { padding: 24px 20px !important; }
      .hero-title { font-size: 36px !important; }
      .step-td { display: block !important; width: 100% !important; padding: 10px 0 !important; }
      .btn-cta { display: block !important; width: 100% !important; text-align: center !important; }
    }
  </style>
</head>

<body style="margin:0;padding:0;background-color:#0f0f0f;font-family:'Outfit',Helvetica,Arial,sans-serif;">


<div style="display:none;max-height:0;overflow:hidden;font-size:1px;color:#0f0f0f;">
  Welcome to Sports Zone! Your account is ready — let's play ball.
</div>


<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#0f0f0f;">
<tr><td align="center" style="padding:40px 16px;">

  
  <table role="presentation" class="email-wrapper" border="0" cellpadding="0" cellspacing="0" width="600"
         style="max-width:600px;width:100%;background-color:#111111;border:1px solid rgba(46,204,113,0.2);">

    
    <tr>
      <td style="background-color:#0a0a0a;padding:28px 40px;border-bottom:3px solid #2ECC71;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td>
             
              <span style="font-family:'Outfit';font-size:26px;font-weight:700;
                           letter-spacing:4px;color:#f5f5f5;text-decoration:none;">
                Sports<span style="color:#2ECC71;">Zone</span>
              </span>
            </td>
            <td align="right">
              <span style="font-family:'Outfit';font-size:10px;letter-spacing:2px;
                           text-transform:uppercase;color:#2ECC71;border:1px solid #2ECC71;padding:4px 10px;">
                ● LIVE
              </span>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    
    <tr>
      <td style="background-color:#0d2b1a;padding:0;overflow:hidden;">
        
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td style="background:linear-gradient(135deg,#0d2b1a 0%,#1a3d2b 50%,#0d2b1a 100%);
                       padding:50px 40px;text-align:center;border-bottom:1px solid rgba(46,204,113,0.2);">
              
              <div style="font-size:52px;margin-bottom:16px;">🥎</div>
              <h1 class="hero-title"
                  style="font-family:'Outfit';font-size:44px;font-weight:700;
                         letter-spacing:3px;color:#f5f5f5;margin:0 0 10px 0;line-height:1.1;
                         text-transform:uppercase;">
                YOU'RE IN,<br><span style="color:#2ECC71;"><?= $d['first_name'] ?>!</span>
              </h1>
              <p style="font-family:'Outfit';font-size:12px;letter-spacing:2px;
                        text-transform:uppercase;color:rgba(245,245,245,0.6);margin:0;">
                Account successfully created
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

   
    <tr>
      <td class="email-body" style="padding:40px 40px 32px;">

        
        <p style="font-size:16px;color:#f5f5f5;line-height:1.7;margin:0 0 16px 0;">
          Hey <strong style="color:#2ECC71;"><?= $d['first_name'] ?> <?= $d['last_name'] ?></strong>,
        </p>
        <p style="font-size:15px;color:#aaaaaa;line-height:1.8;margin:0 0 32px 0;">
          Welcome to <strong style="color:#f5f5f5;">PitchDrop</strong> — the fastest way to book premium
          softball pitches. Your account is live and ready to go. One tap to find your next game.
        </p>

        
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
               style="background-color:#181818;border:1px solid rgba(46,204,113,0.2);
                      border-left:3px solid #2ECC71;margin-bottom:32px;">
          <tr>
            <td style="padding:24px 28px;">
              <p style="font-family:'Outfit';font-size:10px;letter-spacing:2px;
                        text-transform:uppercase;color:#2ECC71;margin:0 0 16px 0;">
                
              </p>
             
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                     style="margin-bottom:10px;">
                <tr>
                  <td width="40%" style="font-family:'Outfit';font-size:11px;
                                         letter-spacing:1px;text-transform:uppercase;color:#666666;">
                    Full Name
                  </td>
                  <td style="font-size:14px;color:#f5f5f5;font-weight:600;">
                    <?= $d['first_name'] ?> <?= $d['last_name'] ?>
                  </td>
                </tr>
              </table>
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
                     style="margin-bottom:10px;">
                <tr>
                  <td width="40%" style="font-family:'Outfit';font-size:11px;
                                         letter-spacing:1px;text-transform:uppercase;color:#666666;">
                    Email
                  </td>
                  <td style="font-size:14px;color:#2ECC71;">
                    <?= $d['email'] ?>
                  </td>
                </tr>
              </table>
              <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td width="40%" style="font-family:'Outfit';font-size:11px;
                                         letter-spacing:1px;text-transform:uppercase;color:#666666;">
                    Registered
                  </td>
                  <td style="font-size:14px;color:#aaaaaa;">
                    <?= $d['registered_at'] ?>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>

        
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%"
               style="margin-bottom:32px;">
          <tr>
            <td style="border-top:1px solid rgba(46,204,113,0.15);font-size:0;line-height:0;">&nbsp;</td>
          </tr>
        </table>

       

      </td>
    </tr>

   
    <tr>
      <td style="background-color:#0a0a0a;padding:28px 40px;
                 border-top:1px solid rgba(46,204,113,0.15);
                 border-bottom:1px solid rgba(46,204,113,0.15);">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td align="center" width="33%" style="padding:0 8px;text-align:center;">
              <div style="font-size:22px;margin-bottom:6px;">⚡</div>
              <p style="font-family:Courier New,monospace;font-size:10px;letter-spacing:1px;
                        text-transform:uppercase;color:#2ECC71;margin:0 0 4px 0;">
                Instant
              </p>
              <p style="font-size:12px;color:#666666;margin:0;">Confirmed in seconds</p>
            </td>
            <td align="center" width="33%"
                style="padding:0 8px;text-align:center;
                       border-left:1px solid rgba(46,204,113,0.15);
                       border-right:1px solid rgba(46,204,113,0.15);">
              <div style="font-size:22px;margin-bottom:6px;">📅</div>
              <p style="font-family:Courier New,monospace;font-size:10px;letter-spacing:1px;
                        text-transform:uppercase;color:#2ECC71;margin:0 0 4px 0;">
                Flexible
              </p>
              <p style="font-size:12px;color:#666666;margin:0;">Book any time, any slot</p>
            </td>
            <td align="center" width="33%" style="padding:0 8px;text-align:center;">
              <div style="font-size:22px;margin-bottom:6px;">💡</div>
              <p style="font-family:Courier New,monospace;font-size:10px;letter-spacing:1px;
                        text-transform:uppercase;color:#2ECC71;margin:0 0 4px 0;">
                Floodlit
              </p>
              <p style="font-size:12px;color:#666666;margin:0;">Play day or night</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

   
    <tr>
      <td style="background-color:#0a0a0a;padding:28px 40px;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
          <!-- Legal -->
          <tr>
            <td align="center">
              <p style="font-size:11px;color:#555555;line-height:1.7;margin:0 0 8px 0;">
                © <?= date('Y') ?> Sports Zone. All rights reserved.<br>
                123 Sports Complex Road, Your City, Country
              </p>
              <p style="font-size:11px;color:#444444;margin:0;">
                Questions? Email us at
                <a href="mailto:<?= $d['support_email'] ?>"
                   style="color:#2ECC71;text-decoration:none;">
                  <?= $d['support_email'] ?>
                </a>
              </p>
              <p style="font-size:10px;color:#333333;margin:12px 0 0 0;font-family:Courier New,monospace;
                        letter-spacing:1px;">
                You received this email because you registered at
                <a href="<?= $d['site_url'] ?>" style="color:#444444;text-decoration:none;">
                  Sportszone.com
                </a>
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
    <?php
    return ob_get_clean();
}
