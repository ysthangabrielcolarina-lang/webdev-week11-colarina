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
        form { background: #fff; padding: 14px 18px; margin: 0 0 16px; display: flex; gap: 12px; align-items: end; }
        label { font-size: 13px; display: block; }
        select, button { padding: 6px 10px; font-size: 14px; }
        .sql { background: #2b2b2b; color: #f4f1ea; padding: 12px 16px; margin: 0 0 16px; font-size: 13px; overflow-wrap: anywhere; }
        .sql h2 { font-size: 13px; margin: 8px 0 4px; color: #d4a017; }
        .sql h2:first-child { margin-top: 0; }
        .sql code { display: block; }
    </style>
</head>
<body>
<header><h1>Campus Events</h1></header>

<main>
    <form method="GET" action="{{ route('events.index') }}">
        <div>
            <label for="dress_code">Dress code</label>
            <select name="dress_code" id="dress_code">
                <option value="">All</option>
                @foreach (['casual', 'smart casual', 'school uniform', 'pe uniform', 'formal'] as $option)
                    <option value="{{ $option }}" @selected($dressCode === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="form">Filter form</label>
            <select name="form" id="form">
                <option value="arrow" @selected($form === 'arrow')>Arrow (details->dress_code)</option>
                <option value="contains" @selected($form === 'contains')>Containment (whereJsonContains)</option>
            </select>
        </div>
        <button type="submit">Filter</button>
    </form>

    @if ($filterSql)
        <div class="sql">
            <h2>toSql()</h2>
            <code>{{ $filterSql }}</code>
            <h2>With the value filled in</h2>
            <code>{{ $filterRawSql }}</code>
        </div>
    @endif

    <table>
        <tr><th>Title</th><th>Venue</th><th>Dress code</th><th>Participants</th></tr>
        @forelse ($events as $event)
            <tr>
                <td>{{ $event->title }}</td>
                <td>{{ $event->venue->name }}</td>
                <td>{{ $event->details['dress_code'] ?? '-' }}</td>
                <td>{{ $event->participants_count }}</td>
            </tr>
        @empty
            <tr><td colspan="4">No events found.</td></tr>
        @endforelse
    </table>

    {{-- Written after the table so it counts the queries the table triggered. --}}
    <p class="query-count">
        Queries on this page: <strong>{{ count(\Illuminate\Support\Facades\DB::getQueryLog()) }}</strong>
    </p>
</main>
</body>
</html>