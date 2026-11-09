<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // B-tree expression index for the arrow query: details->>'dress_code' = ?
        DB::statement("CREATE INDEX events_details_dress_code_index ON events ((details->>'dress_code'))");

        // GIN index for the containment query: details @> ?
        DB::statement('CREATE INDEX events_details_gin_index ON events USING GIN (details jsonb_path_ops)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS events_details_gin_index');
        DB::statement('DROP INDEX IF EXISTS events_details_dress_code_index');
    }
};