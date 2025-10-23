<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Print Certificates</title>
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
            background: white;
        }
        .certificate-page {
            width: 297mm;
            height: 210mm;
            page-break-after: always;
            page-break-inside: avoid;
            overflow: hidden;
            position: relative;
        }
        .certificate-page:last-child {
            page-break-after: auto;
        }
        .print-instructions {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1e40af;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 9999;
            font-family: Arial, sans-serif;
            font-size: 14px;
            max-width: 300px;
        }
        .print-instructions h3 {
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        .print-instructions ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        .print-instructions li {
            margin-bottom: 5px;
        }
        .print-btn {
            display: block;
            width: 100%;
            background: white;
            color: #1e40af;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #f3f4f6;
        }
        @media print {
            .print-instructions {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .certificate-page {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Print Instructions (hidden when printing) -->
    <div class="print-instructions">
        <h3>Ready to Print!</h3>
        <ul>
            <li>Total: {{ count($certificatesHtml) }} certificate(s)</li>
            <li>Press <strong>Ctrl+P</strong> (or Cmd+P on Mac)</li>
            <li>Select "Landscape" orientation</li>
            <li>Set margins to "None"</li>
        </ul>
        <button class="print-btn" onclick="window.print()">Print Now</button>
    </div>

    <!-- Certificates -->
    @foreach($certificatesHtml as $index => $html)
        <div class="certificate-page">
            {!! $html !!}
        </div>
    @endforeach
</body>
</html>
