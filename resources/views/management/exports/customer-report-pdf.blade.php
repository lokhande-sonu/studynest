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

<h3>Customers Report</h3>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Registered</th>
            <th>Gender</th>
            <th>City</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
        <tr>
            <td>{{ $customer->cust_id }}</td>
            <td>{{ $customer->cust_name }}</td>
            <td>{{ $customer->cust_mobile }}</td>
            <td>{{ $customer->cust_email }}</td>
            <td>{{ optional($customer->cust_created_at)->format('d/m/Y h:i A') }}</td>
            <td>{{ ucfirst($customer->cust_gender ?? 'N/A') }}</td>
            <td>{{ $customer->cust_city ?? 'N/A' }}</td>
            <td>{{ $customer->cust_status == 1 ? 'Active' : 'Blocked' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
