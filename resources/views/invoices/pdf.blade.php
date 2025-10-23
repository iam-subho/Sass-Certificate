<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #3B82F6;
            padding: 30px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #3B82F6;
            padding-bottom: 20px;
        }
        .invoice-header h1 {
            color: #3B82F6;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .invoice-header p {
            font-size: 14px;
            color: #666;
        }
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .invoice-details .left,
        .invoice-details .right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .invoice-details .right {
            text-align: right;
        }
        .invoice-details h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #3B82F6;
        }
        .invoice-details p {
            margin: 5px 0;
            font-size: 14px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .invoice-table th {
            background-color: #3B82F6;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        .invoice-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        .invoice-table tr:last-child td {
            border-bottom: 2px solid #3B82F6;
        }
        .invoice-total {
            text-align: right;
            margin-top: 20px;
        }
        .invoice-total table {
            margin-left: auto;
            border-collapse: collapse;
        }
        .invoice-total td {
            padding: 8px 15px;
            font-size: 16px;
        }
        .invoice-total .total-row {
            background-color: #3B82F6;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #10B981;
            color: white;
        }
        .status-pending {
            background-color: #F59E0B;
            color: white;
        }
        .status-cancelled {
            background-color: #6B7280;
            color: white;
        }
        .status-overdue {
            background-color: #EF4444;
            color: white;
        }
        .invoice-notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #F3F4F6;
            border-left: 4px solid #3B82F6;
        }
        .invoice-notes h4 {
            color: #3B82F6;
            margin-bottom: 10px;
        }
        .invoice-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1>INVOICE</h1>
            <p>Invoice Number: <strong>{{ $invoice->invoice_number }}</strong></p>
            <p>Date: {{ $invoice->created_at->format('d F Y') }}</p>
        </div>

        <div class="invoice-details">
            <div class="left">
                <h3>Bill To:</h3>
                <p><strong>{{ $invoice->school->name }}</strong></p>
                <p>{{ $invoice->school->email }}</p>
                <p>{{ $invoice->school->phone }}</p>
            </div>
            <div class="right">
                <h3>Invoice Details:</h3>
                <p><strong>Invoice Month:</strong> {{ \Carbon\Carbon::parse($invoice->month)->format('F Y') }}</p>
                <p><strong>Due Date:</strong> {{ $invoice->due_date->format('d F Y') }}</p>
                @if($invoice->paid_date)
                <p><strong>Paid Date:</strong> {{ $invoice->paid_date->format('d F Y') }}</p>
                @endif
                <p><strong>Status:</strong>
                    @if($invoice->status === 'paid')
                    <span class="status-badge status-paid">Paid</span>
                    @elseif($invoice->status === 'pending')
                        @if($invoice->due_date < now())
                        <span class="status-badge status-overdue">Overdue</span>
                        @else
                        <span class="status-badge status-pending">Pending</span>
                        @endif
                    @elseif($invoice->status === 'cancelled')
                    <span class="status-badge status-cancelled">Cancelled</span>
                    @endif
                </p>
            </div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $invoice->plan_type }} Plan</strong><br>
                        <small>Monthly certificate generation subscription for {{ \Carbon\Carbon::parse($invoice->month)->format('F Y') }}</small>
                    </td>
                    <td style="text-align: center;">{{ $invoice->certificates_count }} certificates</td>
                    <td style="text-align: right;">₹{{ number_format($invoice->amount / max($invoice->certificates_count, 1), 2) }}</td>
                    <td style="text-align: right;">₹{{ number_format($invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="invoice-total">
            <table>
                <tr>
                    <td>Subtotal:</td>
                    <td style="text-align: right;"><strong>₹{{ number_format($invoice->amount, 2) }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL DUE:</td>
                    <td style="text-align: right;"><strong>₹{{ number_format($invoice->amount, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        @if($invoice->payment_method)
        <div class="invoice-notes">
            <h4>Payment Information</h4>
            <p><strong>Payment Method:</strong> {{ $invoice->payment_method }}</p>
        </div>
        @endif

        @if($invoice->notes)
        <div class="invoice-notes">
            <h4>Notes</h4>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="invoice-footer">
            <p>Thank you for your business!</p>
            <p>This is a computer-generated invoice. No signature required.</p>
            <p>Generated on {{ now()->format('d F Y, h:i A') }}</p>
        </div>
    </div>

    <script>
        // Auto-print when the page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
