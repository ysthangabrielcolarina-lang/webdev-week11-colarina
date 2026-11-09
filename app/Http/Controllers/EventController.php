<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index()
    {
        // Start recording every SQL query from this point on.
        DB::enableQueryLog();

        // FIXED VERSION: two chained scopes, plus eager loading and a count.
        $events = Event::startingFrom('2026-01-01')
            ->soonestFirst()
            ->with('venue')
            ->withCount('participants')
            ->limit(50)
            ->get();

        return view('events.index', compact('events'));
    }
}