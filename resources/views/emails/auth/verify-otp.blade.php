<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333333;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background-color: #4F46E5;
            /* Indigo 600 */
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .body-content {
            padding: 40px 30px;
            text-align: center;
        }

        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: left;
        }

        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
            text-align: left;
            margin-bottom: 30px;
        }

        .otp-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #1e293b;
            letter-spacing: 4px;
            margin: 0;
        }

        .expiration {
            font-size: 13px;
            color: #ef4444;
            /* Red for emphasis */
            margin-top: 10px;
        }

        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Account Verification</h1>
        </div>

        <!-- Body -->
        <div class="body-content">
            <div class="greeting">
                Hello {{ $user->name }},
            </div>

            <div class="message">
                Thank you for registering with us. To complete your registration and secure your account, please use the
                following One-Time Password (OTP) to verify your email address.
            </div>

            <div class="otp-box">
                <p class="otp-code">{{ $otp }}</p>
                <p class="expiration">This code will expire in 10 minutes.</p>
            </div>

            <div class="message">
                If you did not create an account using this email address, please ignore this email or contact our
                support team.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} Your Company Name. All rights reserved.<br>
            Please do not reply to this automated message.
        </div>
    </div>
</body>

</html>