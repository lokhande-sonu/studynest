<h2>Pre-Booking Received</h2>

<p>Hello {{ $booking->customer_name }},</p>

<p>Your pre-booking request has been received successfully.</p>

<p>
<b>School ID:</b> {{ $booking->school_id }} <br>
<b>Class:</b> {{ $booking->class_id }} <br>
<b>Token Amount:</b> ₹{{ $booking->token_amount }} <br>
<b>Status:</b> Pending Payment
</p>

<p>Our team will contact you shortly.</p>

Thanks,<br>
Studynest Team