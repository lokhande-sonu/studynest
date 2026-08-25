@php
$delivery = $order->order_delivery_details;
@endphp

<h2>Order Status Update</h2>

<p>Hello {{ $delivery['first_name'] ?? 'Customer' }},</p>

<p>Your order status has been updated.</p>

<p>
<b>Order ID:</b> {{ $order->order_id }} <br>
<b>Status:</b> {{ $status }} <br>
<b>Updated At:</b> {{ $updatedAt }} <br>
<b>Total Amount:</b> ₹{{ $order->order_total_amt }}
</p>

<p>You can login to your account to track your order.</p>

Thanks,<br>
Studynest Team
