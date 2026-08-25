<h2>New Pre-Booking Request</h2>

<p>A new pre-booking has been submitted.</p>

<p>
<b>Name:</b> {{ $booking->customer_name }} <br>
<b>Email:</b> {{ $booking->customer_email }} <br>
<b>Mobile:</b> {{ $booking->customer_mobile }} <br>
<b>School ID:</b> {{ $booking->school_id }} <br>
<b>Class ID:</b> {{ $booking->class_id }} <br>
<b>Token:</b> ₹{{ $booking->token_amount }}
</p>