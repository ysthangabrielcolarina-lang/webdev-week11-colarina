<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // Start recording every SQL query from this point on.
        DB::enableQueryLog();

        $dressCode = $request->query('dress_code');
        $form = $request->query('form', 'arrow');

        $query = Event::query();
        $filterSql = null;
        $filterRawSql = null;

        if ($dressCode) {
            if ($form === 'contains') {
                $query->dressCodeContains($dressCode);
            } else {
                $query->dressCodeArrow($dressCode);
            }

            // Capture the filter's SQL before the list parts below are added.
            $filterSql = $query->toSql();
            $filterRawSql = $query->toRawSql();
        }

        // Two chained scopes, plus eager loading and a count.
        $events = $query->startingFrom('2026-01-01')
            ->soonestFirst()
            ->with('venue')
            ->withCount('participants')
            ->limit(50)
            ->get();

        return view('events.index', compact('events', 'dressCode', 'form', 'filterSql', 'filterRawSql'));
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $events = collect();
        $sql = null;
        $rawSql = null;

        if ($q !== '') {
            $query = Event::whereFullText('description', $q);

            // Capture the SQL before the eager loading and limit are added.
            $sql = $query->toSql();
            $rawSql = $query->toRawSql();

            $events = $query->with('venue')->limit(50)->get();
        }

        return view('events.search', compact('q', 'events', 'sql', 'rawSql'));
    }
}