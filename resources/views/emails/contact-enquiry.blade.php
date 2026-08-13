<!DOCTYPE html>
<html>
<head>
    <title>New Contact Enquiry</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
        .header { background: #f4f4f4; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .field { margin-bottom: 10px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Enquiry Received</h2>
        </div>
        <div class="content">
            <div class="field">
                <span class="label">Name:</span> {{ $enquiry['name'] }}
            </div>
            <div class="field">
                <span class="label">Email:</span> {{ $enquiry['email'] }}
            </div>
            <div class="field">
                <span class="label">Mobile:</span> {{ $enquiry['mobile'] }}
            </div>
            <div class="field">
                <span class="label">Subject:</span> {{ $enquiry['subject'] }}
            </div>
            <div class="field">
                <span class="label">Message:</span>
                <p>{{ $enquiry['message'] }}</p>
            </div>
        </div>
    </div>
</body>
</html>
