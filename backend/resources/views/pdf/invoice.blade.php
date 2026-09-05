<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header-table {
            width: 100%;
        }
        .header-left {
            width: 50%;
        }
        .header-right {
            width: 50%;
            text-align: right;
        }
        .brand {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
            color: #111;
        }
        .tagline {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            color: #000;
        }
        .meta-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            line-height: 1.4;
        }
        .info-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table {
            width: 100%;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
        }
        .info-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #777;
            margin-bottom: 8px;
        }
        .info-content {
            font-size: 14px;
            line-height: 1.5;
        }
        .info-content strong {
            color: #000;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8f9fa;
            color: #555;
            text-transform: uppercase;
            font-size: 11px;
            font-weight: bold;
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .items-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .item-name {
            font-weight: bold;
            color: #222;
        }
        .item-meta {
            font-size: 11px;
            color: #777;
            margin-top: 4px;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .totals-section {
            width: 100%;
        }
        .totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .totals-label {
            color: #666;
            font-size: 13px;
        }
        .totals-value {
            text-align: right;
            font-weight: bold;
        }
        .grand-total td {
            border-bottom: 2px solid #000;
            padding-top: 15px;
            font-size: 18px;
        }
        .footer {
            margin-top: 80px;
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
            clear: both;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        <h1 class="brand">Tory Crown</h1>
                        <div class="tagline">Luxury Jewelry Collection</div>
                    </td>
                    <td class="header-right">
                        <h2 class="invoice-title">INVOICE</h2>
                        <div class="meta-text">
                            <strong>Date:</strong> {{ \Carbon\Carbon::parse($order->created_at)->format('F d, Y') }}<br>
                            <strong>Order No:</strong> {{ $order->order_number }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Billing & Shipping Info -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="info-col">
                        <div class="info-title">Billed & Shipped To</div>
                        <div class="info-content">
                            <strong>{{ $order->recipient_name }}</strong><br>
                            {{ $order->shipping_address ?? 'Address not provided' }}<br>
                            {{ $order->shipping_city ?? '' }}{{ $order->shipping_district ? ', ' . $order->shipping_district : '' }}<br>
                            {{ $order->recipient_phone }}
                        </div>
                    </td>
                    <td class="info-col" style="text-align: right;">
                        <div class="info-title">Order Details</div>
                        <div class="info-content">
                            Payment Method: <strong style="text-transform: uppercase">{{ $order->payment_method }}</strong><br>
                            Payment Status: <strong style="text-transform: uppercase">{{ $order->payment_status ?? 'Pending' }}</strong><br>
                            Order Status: <strong style="text-transform: uppercase">{{ $order->status }}</strong>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items ?? [] as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product_name ?? 'Jewelry Piece' }}</div>
                        @if($item->variant_name)
                        <div class="item-meta">{{ $item->variant_name }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity ?? 1 }}</td>
                    <td class="text-right">BDT {{ number_format($item->unit_price ?? 0, 2) }}</td>
                    <td class="text-right"><strong>BDT {{ number_format(($item->unit_price ?? 0) * ($item->quantity ?? 1), 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section clearfix">
            <table class="totals-table">
                <tr>
                    <td class="totals-label">Subtotal</td>
                    <td class="totals-value">BDT {{ number_format($order->subtotal ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="totals-label">Shipping Fee</td>
                    <td class="totals-value">BDT {{ number_format($order->shipping_fee ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="totals-label">VAT</td>
                    <td class="totals-value">BDT {{ number_format($order->vat ?? 0, 2) }}</td>
                </tr>
                @if(($order->coupon_discount ?? 0) > 0)
                <tr>
                    <td class="totals-label" style="color: #e53e3e;">Discount</td>
                    <td class="totals-value" style="color: #e53e3e;">-BDT {{ number_format($order->coupon_discount, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td class="totals-label" style="color: #000; font-weight: bold;">Grand Total</td>
                    <td class="totals-value">BDT {{ number_format($order->total ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            Thank you for shopping with Tory Crown. If you have any questions concerning this invoice, please contact support@torycrown.com.
        </div>
    </div>
</body>
</html>
