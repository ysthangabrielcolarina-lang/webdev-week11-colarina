<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    protected $fillable = ['venue_id', 'title', 'description', 'details', 'starts_at'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'details' => 'array',
        ];
    }

    // Scope with no parameter: earliest events come first.
    // Usage: Event::soonestFirst()
    public function scopeSoonestFirst(Builder $query): Builder
    {
        return $query->orderBy('starts_at');
    }

    // Scope with a parameter: only events on or after a given date.
    // Usage: Event::startingFrom('2027-01-01')
    public function scopeStartingFrom(Builder $query, string $date): Builder
    {
        return $query->where('starts_at', '>=', $date);
    }

    // JSONB filter, arrow form: read one key as text and compare it.
    public function scopeDressCodeArrow(Builder $query, string $dressCode): Builder
    {
        return $query->where('details->dress_code', $dressCode);
    }

    // JSONB filter, containment form: does the whole column contain this piece of JSON?
    public function scopeDressCodeContains(Builder $query, string $dressCode): Builder
    {
        return $query->whereJsonContains('details', ['dress_code' => $dressCode]);
    }

    // Inverse of one-to-many: an event happens at one venue.
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    // Many-to-many through the event_participant pivot table.
    // withPivot('status') lets us read each registration's status.
    // as('registration') renames ->pivot to ->registration for readability.
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'event_participant')
                    ->withPivot('status')
                    ->withTimestamps()
                    ->as('registration');
    }

    // Polymorphic one-to-many: an event can have many notes.
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }
}