<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table to modify the enum
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("DROP TABLE IF EXISTS games_new");
            
            DB::statement("
                CREATE TABLE games_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255) NOT NULL,
                    code VARCHAR(6) NOT NULL UNIQUE,
                    status VARCHAR(255) NOT NULL DEFAULT 'waiting' CHECK(status IN ('waiting', 'playing', 'voting', 'finished')),
                    current_player_id INTEGER,
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ");
            
            DB::statement("INSERT INTO games_new (id, name, code, status, current_player_id, created_at, updated_at) 
                          SELECT id, name, code, COALESCE(status, 'waiting'), current_player_id, created_at, updated_at 
                          FROM games");
            DB::statement("DROP TABLE games");
            DB::statement("ALTER TABLE games_new RENAME TO games");
        } else {
            Schema::table('games', function (Blueprint $table) {
                // For non-SQLite, try to change the enum (may not work for all DBs)
                // $table->enum('status', ['waiting', 'playing', 'voting', 'finished'])->default('waiting')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("
                CREATE TABLE games_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255) NOT NULL,
                    code VARCHAR(6) NOT NULL UNIQUE,
                    status VARCHAR(255) NOT NULL DEFAULT 'waiting' CHECK(status IN ('waiting', 'playing', 'finished')),
                    current_player_id INTEGER,
                    created_at DATETIME,
                    updated_at DATETIME
                )
            ");
            
            DB::statement("INSERT INTO games_new SELECT * FROM games");
            DB::statement("DROP TABLE games");
            DB::statement("ALTER TABLE games_new RENAME TO games");
        } else {
            Schema::table('games', function (Blueprint $table) {
                $table->enum('status', ['waiting', 'playing', 'finished'])->default('waiting')->change();
            });
        }
    }
};
