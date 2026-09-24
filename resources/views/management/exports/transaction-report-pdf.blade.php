<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>

<h3>Orders Transaction Report</h3>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Items</th>
            <th>Date</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Transaction</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>#{{ $order->order_id }}</td>
            <td>{{ $order->customer->cust_name ?? 'N/A' }}</td>
            <td>{{ $order->order_items_qty }}</td>
            <td>{{ date('d/m/Y h:i A', strtotime($order->order_date_time)) }}</td>
            <td>{{ number_format($order->order_total_amt, 2) }}</td>
            <td>{{ $order->order_payment_status == 1 ? 'Paid' : 'Pending' }}</td>
            <td>
                {{ match($order->order_status) {
                    0 => 'Cancelled',
                    1 => 'Pending',
                    2 => 'Confirmed',
                    4 => 'Shipped',
                    3 => 'Delivered',
                    default => 'Unknown'
                } }}
            </td>
            <td>{{ $order->order_payment_id }} ({{ $order->order_payment_mode == 1 ? 'COD' : 'Online' }})</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
