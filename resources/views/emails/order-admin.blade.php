<h2>New Order Received</h2>

<p><b>Order ID:</b> {{ $order->order_id }}</p>
<p><b>Total Amount:</b> ₹{{ $order->order_total_amt }}</p>

@php
$delivery = $order->order_delivery_details;
@endphp

<p><b>Customer:</b> {{ $delivery['first_name'] ?? '' }} {{ $delivery['last_name'] ?? '' }}</p>
<p><b>Email:</b> {{ $delivery['email'] ?? '' }}</p>
<p><b>Phone:</b> {{ $delivery['phone'] ?? '' }}</p>
