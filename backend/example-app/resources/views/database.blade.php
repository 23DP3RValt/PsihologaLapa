<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database - Events</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Quicksand', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        
        .table-wrapper {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background-color: #667eea;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background-color: #f7f7f7;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        .empty {
            text-align: center;
            color: #999;
            padding: 40px;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s;
        }
        
        .back-link:hover {
            background-color: #764ba2;
        }
        
        .stats {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f0f4ff;
            border-left: 4px solid #667eea;
            border-radius: 6px;
        }
        
        .stats strong {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Database Events</h1>
        
        <div class="stats">
            <strong>Total Events:</strong> {{ count($events) }}
        </div>
        
        @if($events->count() > 0)
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Description</th>
                            <th>Color</th>
                            <th>Created</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td>{{ $event->id }}</td>
                                <td><strong>{{ $event->title }}</strong></td>
                                <td>{{ $event->start }}</td>
                                <td>{{ $event->end }}</td>
                                <td>{{ $event->description ?? 'N/A' }}</td>
                                <td>
                                    @if($event->color)
                                        <span style="display: inline-block; width: 20px; height: 20px; background-color: {{ $event->color }}; border-radius: 4px; border: 1px solid #ddd;"></span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $event->created_at }}</td>
                                <td>{{ $event->updated_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty">
                <p>No events found in the database.</p>
            </div>
        @endif
        
        <a href="/" class="back-link">← Back to Home</a>
    </div>
</body>
</html>
