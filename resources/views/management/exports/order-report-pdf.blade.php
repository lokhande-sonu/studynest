<!DOCTYPE html>
<html>
<head>
    <style>
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:6px; font-size:12px; }
        th { background:#f2f2f2; }
    </style>
</head>
<body>

<h3>Order Report</h3>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Payment</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>#{{ $order->order_id }}</td>
            <td>{{ $order->customer->cust_name ?? 'N/A' }}</td>
            <td>{{ date('d/m/Y h:i A', strtotime($order->order_date_time)) }}</td>
            <td>{{ number_format($order->order_total_amt,2) }}</td>
            {{-- START: imohitmehto | 2026-08-25 | FIX: Use OrderStatus::label() for human-readable status --}}
            {{-- Previously showed raw numeric codes in PDF --}}
            <td>{{ $order->order_payment_status == 1 ? 'Paid' : 'Pending' }}</td>
            <td>{{ \App\Enums\OrderStatus::label($order->order_status) }}</td>
            {{-- END: imohitmehto | FIX: Human-readable status in PDF export --}}
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
