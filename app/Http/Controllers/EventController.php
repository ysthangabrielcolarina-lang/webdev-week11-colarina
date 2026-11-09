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

        // NAIVE VERSION: venue and participants are not loaded here,
        // so the view triggers extra queries for every event.
        $events = Event::startingFrom('2026-01-01')
            ->soonestFirst()
            ->limit(50)
            ->get();

        return view('events.index', compact('events'));
    }
}