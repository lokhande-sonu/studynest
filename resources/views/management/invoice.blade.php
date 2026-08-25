<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: 300;
            text-transform: uppercase;
            color: #888;
            text-align: right;
            margin-bottom: 5px;
        }
        .invoice-meta {
            text-align: right;
            font-size: 13px;
            color: #555;
        }
        /* Using tables for layout to ensure compatibility with various PDF generators */
        table.layout-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.layout-table td {
            vertical-align: top;
        }
        .address-box {
            width: 50%;
        }
        .address-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #888;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        .address-content {
            font-size: 14px;
            line-height: 1.6;
        }
        
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.items-table th {
            text-align: left;
            padding: 12px 10px;
            border-bottom: 2px solid #eee;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            color: #555;
        }
        table.items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f5f5f5;
            font-size: 13px;
        }
        table.items-table tr:last-child td {
            border-bottom: none;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        table.totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.totals-table td {
            padding: 5px 10px;
            text-align: right;
        }
        table.totals-table td:first-child {
            color: #777;
            width: 60%;
        }
        table.totals-table tr.grand-total td {
            font-weight: bold;
            font-size: 16px;
            color: #000;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #888;
            text-align: center;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .invoice-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <table class="layout-table">
            <tr>
                <td>
                    <div class="company-name">StudyNest</div>
                    <div style="color: #777; font-size: 13px;">Your Learning Partner</div>
                </td>
                <td class="text-right">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta">
                        <strong>#{{ $order->order_id }}</strong><br>
                        {{ date('F d, Y', strtotime($order->order_date_time)) }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Billing Info -->
        <table class="layout-table">
            <tr>
                <td class="address-box">
                    <div class="address-title">Billed To</div>
                    <div class="address-content">
                        @php
                            $delivery = is_array($order->order_delivery_details) ? $order->order_delivery_details : json_decode($order->order_delivery_details, true);
                        @endphp

                        <strong>{{ $delivery['first_name'] ?? '' }} {{ $delivery['last_name'] ?? '' }}</strong><br>
                        {{ $delivery['address'] ?? '' }}
                        @if(!empty($delivery['apartment'])), {{ $delivery['apartment'] }}@endif<br>
                        {{ $delivery['city'] ?? '' }}, {{ $delivery['state'] ?? '' }} - {{ $delivery['pincode'] ?? ($delivery['zip'] ?? '') }}<br>
                        {{ $delivery['phone'] ?? '' }}<br>
                        {{ $delivery['email'] ?? '' }}
                        @if(!empty($order->order_gst_number))
                        <br><br><strong>GST Number:</strong> {{ $order->order_gst_number }}
                        @endif
                    </div>
                </td>
                <td class="address-box text-right">
                    <div class="address-title">Order Details</div>
                    <div class="address-content">
                        <strong>Payment Method:</strong> {{ \App\Enums\OrderPaymentMode::label($order->order_payment_mode) }}<br>
                        <strong>Status:</strong> 
                        @if($order->order_payment_status == 1)
                            <span style="color: green; font-weight: bold;">Paid</span>
                        @else
                            <span style="color: #e67e22; font-weight: bold;">Pending</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 50%;">Item Description</th>
                    <th class="text-center" style="width: 15%;">Qty</th>
                    <th class="text-right" style="width: 15%;">Rate</th>
                    <th class="text-right" style="width: 15%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $items = is_array($order->order_items) ? $order->order_items : json_decode($order->order_items, true);
                @endphp
                @if(is_array($items))
                    @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item['product_name'] ?? 'Product' }}</strong>
                            @if(!empty($item['variant_name']))
                                <br><span style="font-size: 11px; color: #777;">Variant: {{ $item['variant_name'] }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item['product_qty'] ?? 1 }}</td>
                        <td class="text-right">₹{{ number_format($item['product_rate'] ?? 0, 2) }}</td>
                        <td class="text-right">₹{{ number_format($item['price'] ?? ($item['product_total'] ?? 0), 2) }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <!-- Totals -->
        <table class="layout-table">
            <tr>
                <td style="width: 50%;">
                    @php
                        $hasGST = false;
                        $items = is_array($order->order_items) ? $order->order_items : json_decode($order->order_items, true);
                        if(is_array($items) && count($items) > 0) {
                            $hasGST = isset($items[0]['gst_rate']) && $items[0]['gst_rate'] > 0;
                            $gstRate = $items[0]['gst_rate'] ?? 0;
                            $gstType = $items[0]['gst_type'] ?? 'inclusive';
                        }
                    @endphp
                    @if($hasGST)
                    <div style="font-size: 11px; color: #666; padding-right: 20px;">
                        <strong>GST Details:</strong><br>
                        GST Rate: {{ $gstRate }}% ({{ ucfirst($gstType) }})<br>
                        CGST: {{ $gstRate/2 }}%<br>
                        SGST: {{ $gstRate/2 }}%<br>
                        @if(!empty($order->order_gst_number))
                        <br><strong>Customer GST:</strong> {{ $order->order_gst_number }}
                        @endif
                    </div>
                    @endif
                </td>
                <td style="width: 50%;">
                    <table class="totals-table">
                        @php
                            $charges = is_array($order->order_charges) ? $order->order_charges : json_decode($order->order_charges, true);
                            $totalCharges = 0;
                            $cgstAmount = 0;
                            $sgstAmount = 0;
                            $otherCharges = [];
                            if(is_array($charges)) {
                                foreach($charges as $charge) {
                                    $chargeName = strtolower($charge['charge_name'] ?? '');
                                    $amount = (float)($charge['calculated_amount'] ?? 0);
                                    if($chargeName === 'cgst') {
                                        $cgstAmount = $amount;
                                    } elseif($chargeName === 'sgst') {
                                        $sgstAmount = $amount;
                                    } else {
                                        $otherCharges[] = $charge;
                                        $totalCharges += $amount;
                                    }
                                }
                            }
                            // Calculate subtotal (price before GST)
                            $subTotal = $order->order_total_amt - $cgstAmount - $sgstAmount - $totalCharges;
                        @endphp
                        
                        <tr>
                            <td>Subtotal (before GST)</td>
                            <td>₹{{ number_format($subTotal, 2) }}</td>
                        </tr>
                        
                        @if($cgstAmount > 0)
                        <tr>
                            <td>CGST ({{ $gstRate/2 }}%)</td>
                            <td>₹{{ number_format($cgstAmount, 2) }}</td>
                        </tr>
                        @endif
                        
                        @if($sgstAmount > 0)
                        <tr>
                            <td>SGST ({{ $gstRate/2 }}%)</td>
                            <td>₹{{ number_format($sgstAmount, 2) }}</td>
                        </tr>
                        @endif
                        
                        @if(is_array($otherCharges))
                            @foreach($otherCharges as $charge)
                            <tr>
                                <td>{{ $charge['charge_name'] ?? 'Charge' }}</td>
                                <td>₹{{ number_format((float)($charge['calculated_amount'] ?? 0), 2) }}</td>
                            </tr>
                            @endforeach
                        @endif
                        
                        <tr class="grand-total">
                            <td>Total Amount</td>
                            <td>₹{{ number_format($order->order_total_amt, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for shopping with StudyNest!</p>
            <p>For any queries, contact us at support@studynest.com</p>
        </div>
    </div>
</body>
</html>