<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Ready for Download</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .content h2 {
            color: #667eea;
            font-size: 20px;
            margin-top: 0;
        }
        .certificate-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .certificate-info p {
            margin: 8px 0;
        }
        .certificate-info strong {
            color: #495057;
        }
        .download-button {
            text-align: center;
            margin: 30px 0;
        }
        .download-button a {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
        }
        .download-button a:hover {
            opacity: 0.9;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        .footer p {
            margin: 5px 0;
        }
        .note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎓 Congratulations!</h1>
            <p>Your Certificate is Ready</p>
        </div>

        <div class="content">
            <h2>Dear {{ $certificate->student->full_name }},</h2>

            <p>We are pleased to inform you that your certificate has been successfully generated and is now ready for download.</p>

            <div class="certificate-info">
                <p><strong>Certificate ID:</strong> {{ $certificate->certificate_id }}</p>
                <p><strong>School:</strong> {{ $certificate->school->name }}</p>
                @if($certificate->event)
                <p><strong>Event:</strong> {{ $certificate->event->name }}</p>
                @endif
                @if($certificate->rank)
                <p><strong>Achievement:</strong> {{ $certificate->rank }}</p>
                @endif
                <p><strong>Issued Date:</strong> {{ $certificate->issued_at->format('F d, Y') }}</p>
            </div>

            <div class="download-button">
                <a href="{{ $certificate->download_url }}" target="_blank">
                    📥 Download Certificate
                </a>
            </div>

            <div class="note">
                <strong>Note:</strong> This download link is unique to you and will remain active. Please keep it safe for future reference. You can download your certificate as many times as you need.
            </div>

            <p>You can also verify your certificate authenticity at any time using this link:<br>
            <a href="{{ $certificate->verification_url }}" target="_blank">{{ $certificate->verification_url }}</a></p>

            <p>If you have any questions or concerns, please don't hesitate to contact us.</p>

            <p>Best regards,<br>
            <strong>{{ $certificate->school->name }}</strong></p>
        </div>

        <div class="footer">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} {{ $certificate->school->name }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
