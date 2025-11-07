<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(to right, #3b82f6, #6366f1);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 16px;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }
        .info-box {
            background: #eff6ff;
            border: 1px solid #3b82f6;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Password Reset Request</h1>
    </div>

    <div class="content">
        <p>Dear {{ $student->full_name }},</p>

        <p>We received a request to reset your password for the Student Portal. Click the button below to create a new password:</p>

        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        </div>

        <div class="info-box">
            <strong>This link will expire in 60 minutes</strong> for security reasons. If the link expires, you'll need to request a new password reset.
        </div>

        <div class="warning">
            <strong>⚠️ Didn't request this?</strong><br>
            If you didn't request a password reset, please ignore this email. Your password will remain unchanged, and your account is secure.
        </div>

        <p style="font-size: 14px; color: #6b7280;">
            If the button doesn't work, copy and paste this link into your browser:<br>
            <a href="{{ $resetUrl }}" style="color: #3b82f6; word-break: break-all;">{{ $resetUrl }}</a>
        </p>

        <p>If you have any questions, please contact your school administrator.</p>

        <div class="footer">
            <p>
                This is an automated email. Please do not reply to this message.<br>
                &copy; {{ date('Y') }} Certificate Management System. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
