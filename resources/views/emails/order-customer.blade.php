<h2>Thank you for your order!</h2>

@php
$delivery = $order->order_delivery_details;
$items = is_array($order->order_items) ? $order->order_items : json_decode($order->order_items, true);
$charges = is_array($order->order_charges) ? $order->order_charges : json_decode($order->order_charges, true);
$subTotal = collect($items)->sum(function ($item) {
    return ($item['product_rate'] ?? 0) * ($item['product_qty'] ?? 1);
});
@endphp

<p>Hello {{ $delivery['first_name'] ?? 'Customer' }},</p>

<p>Thank you for shopping with StudyNest. Your order has been placed successfully and the invoice is attached to this email.</p>

@if(!empty($order->order_id))
    <p style="margin: 16px 0;">
        <a href="{{ route('website.orders.invoice', $order->order_id) }}"
           style="display: inline-block; padding: 12px 24px; background-color: #008000; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold;">
            Download Invoice
        </a>
    </p>
@endif

<p><b>Order ID:</b> #{{ $order->order_id }}</p>
<p><b>Order Date:</b> {{ date('d M Y h:i A', strtotime($order->order_date_time)) }}</p>
<p><b>Payment Method:</b> {{ \App\Enums\OrderPaymentMode::label($order->order_payment_mode) }}</p>

<h3>Items Ordered</h3>
<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th>Product</th>
            <th>Variant</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item['product_name'] ?? 'Product' }}</td>
            <td>{{ $item['variant_name'] ?? '-' }}</td>
            <td>{{ $item['product_qty'] ?? 1 }}</td>
            <td>₹{{ number_format($item['product_rate'] ?? 0, 2) }}</td>
            <td>₹{{ number_format($item['price'] ?? ($item['product_total'] ?? 0), 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h3>Amount Summary</h3>
<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse;">
    <tr>
        <td>Subtotal (after GST)</td>
        <td>₹{{ number_format(round($subTotal, 2), 2) }}</td>
    </tr>
    @foreach($charges as $charge)
    <tr>
        <td>{{ $charge['charge_name'] ?? 'Charge' }}</td>
        <td>₹{{ number_format((float)($charge['calculated_amount'] ?? 0), 2) }}</td>
    </tr>
    @endforeach
    <tr>
        <td><b>Total Amount</b></td>
        <td><b>₹{{ number_format(round($order->order_total_amt), 0) }}</b></td>
    </tr>
</table>

@if(!empty($delivery['address']))
<h3>Delivery Address</h3>
<p>
    {{ $delivery['first_name'] ?? '' }} {{ $delivery['last_name'] ?? '' }}<br>
    {{ $delivery['address'] ?? '' }}@if(!empty($delivery['apartment'])), {{ $delivery['apartment'] }}@endif, {{ $delivery['city'] ?? '' }}, {{ $delivery['state'] ?? '' }} - {{ $delivery['pincode'] ?? ($delivery['zip'] ?? '') }}<br>
    Phone: {{ $delivery['phone'] ?? '' }}
</p>
@endif

<p>We will notify you when your order is shipped.</p>

<br>
Thanks<br>
StudyNest Team