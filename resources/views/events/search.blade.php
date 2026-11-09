<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Campus Events</title>
    <style>
        body { margin: 0; font-family: "Segoe UI", Arial, sans-serif; background: #f4f1ea; color: #222; }
        header { background: #7a1020; color: #fff; padding: 22px 32px; }
        header h1 { margin: 0; font-size: 26px; }
        main { max-width: 980px; margin: 0 auto; padding: 24px 16px 48px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; background: #fff; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #eee; }
        th { background: #faf7f0; }
        form { background: #fff; padding: 14px 18px; margin: 0 0 16px; display: flex; gap: 12px; }
        input { flex: 1; padding: 6px 10px; font-size: 14px; }
        button { padding: 6px 14px; font-size: 14px; }
        .sql { background: #2b2b2b; color: #f4f1ea; padding: 12px 16px; margin: 0 0 16px; font-size: 13px; overflow-wrap: anywhere; }
        .sql h2 { font-size: 13px; margin: 8px 0 4px; color: #d4a017; }
        .sql h2:first-child { margin-top: 0; }
        .sql code { display: block; }
        .result-count { margin: 0 0 12px; }
    </style>
</head>
<body>
<header><h1>Search Campus Events</h1></header>

<main>
    <form method="GET" action="{{ route('events.search') }}">
        <input type="text" name="q" value="{{ $q }}" placeholder="Search descriptions, for example hackathon">
        <button type="submit">Search</button>
    </form>

    @if ($sql)
        <div class="sql">
            <h2>toSql()</h2>
            <code>{{ $sql }}</code>
            <h2>With the value filled in</h2>
            <code>{{ $rawSql }}</code>
        </div>

        <p class="result-count">Showing {{ $events->count() }} result(s), up to 50.</p>

        <table>
            <tr><th>Title</th><th>Venue</th><th>Description</th></tr>
            @forelse ($events as $event)
                <tr>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->venue->name }}</td>
                    <td>{{ $event->description }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No events found.</td></tr>
            @endforelse
        </table>
    @else
        <p>Type a word above to search.</p>
    @endif
</main>
</body>
</html>