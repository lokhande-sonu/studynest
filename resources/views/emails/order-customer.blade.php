<h2>Thank you for your order</h2>

<p>Hello {{ $order->order_delivery_details['first_name'] ?? 'Customer' }}</p>

<p>Your order has been placed successfully.</p>

<p><b>Order ID:</b> {{ $order->order_id }}</p>
<p><b>Total:</b> ₹{{ $order->order_total_amt }}</p>

<p>We will notify you when your order is shipped.</p>

<br>
Thanks  
Studynest Team
