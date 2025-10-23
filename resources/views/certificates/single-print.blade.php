<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_id }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
            background: #f3f4f6;
        }
        .certificate-page {
            width: 297mm;
            height: 210mm;
            margin: 20px auto;
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .print-instructions {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            font-family: Arial, sans-serif;
            font-size: 14px;
            max-width: 320px;
        }
        .print-instructions h3 {
            margin-bottom: 12px;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        .print-instructions h3::before {
            content: "🎓";
            margin-right: 8px;
            font-size: 24px;
        }
        .certificate-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .certificate-info p {
            margin: 5px 0;
            font-size: 13px;
        }
        .certificate-info strong {
            color: #ffd700;
        }
        .print-instructions ul {
            margin-left: 20px;
            margin-bottom: 15px;
            list-style: none;
        }
        .print-instructions li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }
        .print-instructions li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4ade80;
            font-weight: bold;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        .print-btn {
            flex: 1;
            background: white;
            color: #667eea;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .print-btn:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .print-btn svg {
            margin-right: 6px;
        }
        .download-pdf-btn {
            background: #10b981;
            color: white;
        }
        .download-pdf-btn:hover {
            background: #059669;
        }
        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .print-instructions {
                display: none !important;
            }
            .certificate-page {
                margin: 0;
                padding: 0;
                box-shadow: none;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
        }
        @media screen and (max-width: 1200px) {
            .certificate-page {
                width: 100%;
                height: auto;
                margin: 10px;
            }
            .print-instructions {
                position: relative;
                margin: 20px;
                right: auto;
                top: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Print Instructions (hidden when printing) -->
    <div class="print-instructions">
        <h3>Certificate Ready!</h3>

        <div class="certificate-info">
            <p><strong>Student:</strong> {{ $certificate->student->full_name }}</p>
            <p><strong>Certificate ID:</strong> {{ $certificate->certificate_id }}</p>
            @if($certificate->event)
            <p><strong>Event:</strong> {{ $certificate->event->name }}</p>
            @endif
        </div>

        <ul>
            <li>Press <strong>Ctrl+P</strong> or click Print button</li>
            <li>Select <strong>Landscape</strong> orientation</li>
            <li>Set margins to <strong>None</strong></li>
            <li>Choose your printer or "Save as PDF"</li>
        </ul>

        <div class="button-group">
            <button class="print-btn" onclick="window.print()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Print
            </button>
        </div>

        <p style="margin-top: 15px; font-size: 12px; opacity: 0.9;">
            💡 Tip: Use "Save as PDF" to create a digital copy
        </p>
    </div>

    <!-- Certificate -->
    <div class="certificate-page">
        {!! $html !!}
    </div>
</body>
</html>
