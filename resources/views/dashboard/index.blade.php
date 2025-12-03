<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart Support Classifier</title>
</head>
<body>
    <h1>Dashboard</h1>

    <div>
        <h2>Total Tickets: {{ $totalTickets }}</h2>

        <h3>Tickets by Category:</h3>
        <ul>
            @foreach($ticketsByCategory as $item)
                <li>{{ $item->category }}: {{ $item->count }}</li>
            @endforeach
        </ul>

        <h3>Tickets by Sentiment:</h3>
        <ul>
            @foreach($ticketsBySentiment as $item)
                <li>{{ $item->sentiment }}: {{ $item->count }}</li>
            @endforeach
        </ul>

        <h3>Tickets by Status:</h3>
        <ul>
            @foreach($ticketsByStatus as $item)
                <li>{{ $item->status }}: {{ $item->count }}</li>
            @endforeach
        </ul>
    </div>
</body>
</html>
