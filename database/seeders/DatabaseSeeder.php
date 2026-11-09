<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- 3 venues ----------
        $gym = Venue::create(['name' => 'PUPC Gymnasium', 'location' => 'Main Campus, Calauan', 'capacity' => 800]);
        $avr = Venue::create(['name' => 'Audio-Visual Room', 'location' => 'Academic Building, 2nd Floor', 'capacity' => 120]);
        $court = Venue::create(['name' => 'Covered Court', 'location' => 'Beside the Admin Building', 'capacity' => 500]);

        // ---------- 6 events, created THROUGH the venue relationship ----------
        // $gym->events()->create([...]) fills in venue_id automatically.
        $e1 = $gym->events()->create(['title' => 'Intramurals Opening', 'description' => 'Parade and opening program.', 'starts_at' => '2026-11-16 08:00']);
        $e2 = $gym->events()->create(['title' => 'Basketball Finals', 'description' => 'Championship game.', 'starts_at' => '2026-11-20 13:00']);
        $e3 = $gym->events()->create(['title' => 'Cheerdance Competition', 'description' => 'Per-department cheerdance.', 'starts_at' => '2026-11-21 15:00']);
        $e4 = $avr->events()->create(['title' => 'Web Development Seminar', 'description' => 'Intro to Laravel.', 'starts_at' => '2026-11-24 09:00']);
        $e5 = $avr->events()->create(['title' => 'Database Workshop', 'description' => 'Hands-on PostgreSQL.', 'starts_at' => '2026-11-25 09:00']);
        $e6 = $court->events()->create(['title' => 'Foundation Day Concert', 'description' => 'Evening concert.', 'starts_at' => '2026-12-01 18:00']);

        // Give the 6 original events a details value too.
        DB::table('events')->whereNull('details')->update([
            'details' => json_encode(['dress_code' => 'casual', 'capacity' => 100, 'tags' => ['campus']]),
        ]);

        // ---------- 10 participants ----------
        $names = [
            'Ysthan Colarina', 'LeBron James', 'Ihsan Belches', 'Ethan Borlagdan', 'Dave Villanueva',
            'Jim Delantar', 'Marchel Mercado', 'Jasmine Garcia', 'Kevin Durant', 'Nicole Batista',
        ];
        $p = [];
        foreach ($names as $i => $name) {
            $p[$i + 1] = Participant::create([
                'name'  => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            ]);
        }

        // ---------- 17 registrations, created THROUGH the many-to-many relationship ----------
        // attach() inserts rows into the event_participant pivot table.
        $e1->participants()->attach([
            $p[1]->id => ['status' => 'registered'],
            $p[2]->id => ['status' => 'registered'],
            $p[3]->id => ['status' => 'registered'],
            $p[4]->id => ['status' => 'waitlisted'],
        ]);
        $e2->participants()->attach([
            $p[1]->id => ['status' => 'registered'],
            $p[5]->id => ['status' => 'registered'],
        ]);
        $e3->participants()->attach([
            $p[2]->id => ['status' => 'registered'],
            $p[6]->id => ['status' => 'waitlisted'],
        ]);
        $e4->participants()->attach([
            $p[7]->id => ['status' => 'registered'],
            $p[9]->id => ['status' => 'registered'],
        ]);
        $e5->participants()->attach([
            $p[1]->id  => ['status' => 'registered'],
            $p[8]->id  => ['status' => 'registered'],
            $p[10]->id => ['status' => 'registered'],
        ]);
        $e6->participants()->attach([
            $p[3]->id => ['status' => 'registered'],
            $p[4]->id => ['status' => 'registered'],
            $p[5]->id => ['status' => 'waitlisted'],
        ]);

        // ---------- 6 notes, created THROUGH the polymorphic relationship ----------
        // 3 on events
        $e1->notes()->create(['body' => 'Assembly at 7:30 AM in PE uniform.']);
        $e1->notes()->create(['body' => 'Each department brings its own banner.']);
        $e2->notes()->create(['body' => 'Free entrance for students with ID.']);
        // 3 on venues
        $gym->notes()->create(['body' => 'Aircon units are under maintenance until November 10.']);
        $gym->notes()->create(['body' => 'Reserve through the Student Affairs Office.']);
        $avr->notes()->create(['body' => 'Projector remote is kept at the faculty room.']);

        // ---------- Week 11: large data ----------
        $this->seedManyEvents([$gym->id, $avr->id, $court->id]);
        $this->seedManyParticipants();
        $this->seedManyRegistrations($e6->id);
    }

    // Creates 20,000 events, sent to the database 1,000 at a time.
    private function seedManyEvents(array $venueIds): void
    {
        $eventTypes = ['Seminar', 'Workshop', 'Assembly', 'Tournament', 'Orientation', 'Forum'];
        $openers = ['Join us for', 'Students are invited to', 'Do not miss', 'Come and experience', 'The university presents'];
        $topics = ['a lively campus fair', 'an outdoor sports clinic', 'a leadership workshop', 'a coding seminar', 'a cultural showcase', 'a career talk'];
        $closers = [
            'with free snacks for attendees.',
            'open to all year levels.',
            'hosted by the student council.',
            'followed by a short open forum.',
            'with certificates for participants.',
            'held rain or shine.',
            'with limited seats available.',
        ];
        $dressCodes = ['casual', 'smart casual', 'school uniform', 'pe uniform'];
        $tags = ['sports', 'academic', 'culture', 'seminar', 'free', 'outdoor', 'indoor'];

        $start = Carbon::parse('2027-01-01 08:00:00');
        $now = now();
        $rows = [];

        for ($i = 1; $i <= 20000; $i++) {
            $description = $openers[$i % count($openers)] . ' '
                . $topics[$i % count($topics)] . ' '
                . $closers[$i % count($closers)];

            // RARE WORD: "hackathon" appears in 133 events (under 1%).
            if ($i % 150 === 0) {
                $description .= ' Includes a hackathon challenge.';
            }

            // RARE VALUE: dress_code "formal" appears in 118 events (under 1%).
            $dressCode = $i % 170 === 3 ? 'formal' : $dressCodes[$i % count($dressCodes)];

            $rows[] = [
                'venue_id'    => $venueIds[$i % count($venueIds)],
                'title'       => $eventTypes[$i % count($eventTypes)] . ' #' . $i,
                'description' => $description,
                'details'     => json_encode([
                    'dress_code' => $dressCode,
                    'capacity'   => 30 + ($i % 20) * 25,
                    'tags'       => [$tags[$i % count($tags)], $tags[($i + 3) % count($tags)]],
                ]),
                'starts_at'   => $start->copy()->addHours($i)->toDateTimeString(),
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            // When 1,000 rows are ready, insert them together and start over.
            if (count($rows) === 1000) {
                DB::table('events')->insert($rows);
                $rows = [];
            }
        }

        // Insert any rows left over.
        if ($rows !== []) {
            DB::table('events')->insert($rows);
        }
    }

    // Creates 200 more participants, for 210 in total.
    private function seedManyParticipants(): void
    {
        $now = now();
        $rows = [];

        for ($i = 1; $i <= 200; $i++) {
            $rows[] = [
                'name'       => 'Participant ' . $i,
                'email'      => 'participant' . $i . '@example.com',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('participants')->insert($chunk);
        }
    }

    // Registers people for events 7 to 100. Events 1 to 6 already have registrations.
    private function seedManyRegistrations(int $lastOriginalEventId): void
    {
        $eventIds = Event::where('id', '>', $lastOriginalEventId)
            ->orderBy('id')
            ->limit(94)
            ->pluck('id');

        $participantIds = Participant::orderBy('id')->pluck('id')->all();
        $total = count($participantIds);
        $now = now();
        $rows = [];

        foreach ($eventIds as $eventId) {
            // Each event gets between 3 and 12 participants.
            $howMany = 3 + ($eventId % 10);

            for ($j = 0; $j < $howMany; $j++) {
                $rows[] = [
                    'event_id'       => $eventId,
                    'participant_id' => $participantIds[($eventId * 7 + $j) % $total],
                    'status'         => $j % 5 === 4 ? 'waitlisted' : 'registered',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('event_participant')->insert($chunk);
        }
    }
}