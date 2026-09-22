<?php
// Email template for login link
$emailTemplate = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ACSES Election Portal - Login Link</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background: linear-gradient(135deg, #124824 0%, #124824 100%);
            color: #FFFFFF;
            border-radius: 10px 10px 0 0;
        }
        .content {
            padding: 20px;
            color: #333;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #124824;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .button:hover {
            background-color: #f9a915;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            margin-top: 20px;
        }
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
        }
        .message {
            margin: 20px 0;
            line-height: 1.8;
        }
        .warning {
            font-size: 12px;
            color: #666;
            font-style: italic;
            margin-top: 20px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                padding: 15px;
            }
            .button {
                display: block;
                text-align: center;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://election.acses-umat.com/logo.png" alt="ACSES Logo" class="logo">
        </div>
        <div class="content">
            <div class="message">
                <p>Hello,</p>
                <p>You have requested a login link for the ACSES Election Portal. Click the button below, then click <strong>Continue to Election Portal</strong> on the next page to sign in:</p>
                <div style="text-align: center;">
                    <a href="' . $accessLink['url'] . '" class="button">Open Sign-In Link</a>
                </div>
                <p>This link expires in 1 hour and can only be used once.</p>
            </div>
            <p class="warning">If you did not request this link, please ignore this email and ensure your account is secure.</p>
        </div>
        <div class="footer">
            <p>&copy; ' . date('Y') . ' ACSES Election Portal. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
';
?> 