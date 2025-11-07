<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Student Portal Login Credentials</title>
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
        .credentials-box {
            background: white;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
        }
        .credential-label {
            font-weight: bold;
            color: #1f2937;
        }
        .credential-value {
            color: #3b82f6;
            font-size: 18px;
            font-family: monospace;
            background: #eff6ff;
            padding: 8px 12px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 5px;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
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
        <h1>Welcome to Student Portal!</h1>
    </div>

    <div class="content">
        <p>Dear {{ $student->full_name }},</p>

        <p>Your student portal account has been created. You can now access your certificates, manage your profile, and more.</p>

        <div class="credentials-box">
            <h3 style="margin-top: 0; color: #1f2937;">Your Login Credentials</h3>

            <div class="credential-item">
                <div class="credential-label">Username:</div>
                <div class="credential-value">{{ $student->username }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Temporary Password:</div>
                <div class="credential-value">{{ $password }}</div>
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    (A random generated password using your DOB)
                </p>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Login to Student Portal</a>
        </div>

        <div class="warning">
            <strong>⚠️ Important:</strong> For security reasons, please change your password immediately after your first login.
        </div>

        <h3>What you can do in the Student Portal:</h3>
        <ul>
            <li>View and download all your certificates</li>
            <li>Create a public profile (like LinkedIn)</li>
            <li>Control which certificates are visible on your profile</li>
            <li>Share your profile with a unique URL</li>
            <li>Update your bio, headline, and social links</li>
        </ul>

        <p>If you have any questions or need assistance, please contact your school administrator.</p>

        <div class="footer">
            <p>
                This is an automated email. Please do not reply to this message.<br>
                &copy; {{ date('Y') }} Certificate Management System. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
