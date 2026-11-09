<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Events</title>
    <style>
        body { margin: 0; font-family: "Segoe UI", Arial, sans-serif; background: #f4f1ea; color: #222; }
        header { background: #7a1020; color: #fff; padding: 22px 32px; }
        header h1 { margin: 0; font-size: 26px; }
        main { max-width: 980px; margin: 0 auto; padding: 24px 16px 48px; display: flex; flex-direction: column; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; background: #fff; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #eee; }
        th { background: #faf7f0; }
        .query-count { order: -1; background: #fff; border-left: 6px solid #d4a017; padding: 12px 18px; margin: 0 0 16px; font-size: 18px; }
    </style>
</head>
<body>
<header><h1>Campus Events</h1></header>

<main>
    <table>
        <tr><th>Title</th><th>Venue</th><th>Participants</th></tr>
        @foreach ($events as $event)
            <tr>
                <td>{{ $event->title }}</td>
                <td>{{ $event->venue->name }}</td>
                <td>{{ $event->participants->count() }}</td>
            </tr>
        @endforeach
    </table>

    {{-- Written after the table so it counts the queries the table triggered. --}}
    <p class="query-count">
        Queries on this page: <strong>{{ count(\Illuminate\Support\Facades\DB::getQueryLog()) }}</strong>
    </p>
</main>
</body>
</html>