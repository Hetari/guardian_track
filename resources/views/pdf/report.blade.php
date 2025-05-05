<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Report PDF</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      line-height: 1.5;
      font-size: 14px;
    }
    h1 {
      color: #333;
      margin-bottom: 20px;
    }
    .section {
      margin-bottom: 10px;
    }
    strong {
      display: inline-block;
      width: 180px;
    }
  </style>
</head>
<body>
  <h1>Report #{{ $report->id }}</h1>

  <div class="section"><strong>User ID:</strong> {{ $report->user_id }}</div>
  <div class="section"><strong>Customer Name:</strong> {{ $report->customer_name }}</div>
  <div class="section"><strong>Type:</strong> {{ ucfirst($report->type) }}</div>
  <div class="section"><strong>Serial Code:</strong> {{ $report->serial_code }}</div>
  <div class="section"><strong>Date & Time:</strong> {{ $report->date_time }}</div>
  <div class="section"><strong>Country:</strong> {{ $report->country }}</div>
  <div class="section"><strong>City:</strong> {{ $report->city }}</div>
  <div class="section"><strong>Street Address:</strong> {{ $report->street_address }}</div>
  <div class="section"><strong>Item Type:</strong> {{ $report->item_type }}</div>
  <div class="section"><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $report->status)) }}</div>
  <div class="section"><strong>Tracking Code:</strong> {{ $report->tracking_code }}</div>
  <div class="section"><strong>Lost Ownership Document:</strong> {{ $report->lost_ownership_document ? 'Yes' : 'No' }}</div>
  <div class="section"><strong>Company ID:</strong> {{ $report->company_id }}</div>
  <div class="section"><strong>Created At:</strong> {{ $report->created_at ? $report->created_at->format('d M Y, H:i') : 'N/A' }}</div>
<div class="section"><strong>Updated At:</strong> {{ $report->updated_at ? $report->updated_at->format('d M Y, H:i') : 'N/A' }}</div>

</body>
</html>
