<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || str_starts_with((string) config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            \Illuminate\Support\Facades\DB::statement('PRAGMA foreign_keys = ON;');
            try {
                \Illuminate\Support\Facades\DB::statement('DROP TRIGGER IF EXISTS trg_pagos_audit_insert;');
                \Illuminate\Support\Facades\DB::statement('DROP TRIGGER IF EXISTS trg_pagos_audit_update;');
                \Illuminate\Support\Facades\DB::statement('DROP TRIGGER IF EXISTS trg_pagos_audit_delete;');
            } catch (\Throwable $e) {
            }
        }

        // Enforce foreign keys on every newly established SQLite connection or reconnection
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Database\Events\ConnectionEstablished::class,
            function (\Illuminate\Database\Events\ConnectionEstablished $event) {
                if ($event->connection->getDriverName() === 'sqlite') {
                    $event->connection->statement('PRAGMA foreign_keys = ON;');
                }
            }
        );

        // Auto-run pending migrations if any schema update or initial blank reset is missing in production
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('anticipos')) {
                $needsMigrate = !\Illuminate\Support\Facades\Schema::hasColumn('anticipos', 'dias_debe')
                    || !\Illuminate\Support\Facades\Schema::hasColumn('anticipos', 'observacion');

                if (!$needsMigrate && \Illuminate\Support\Facades\Schema::hasTable('migrations')) {
                    $hasBlankReset = \Illuminate\Support\Facades\DB::table('migrations')
                        ->where('migration', '2026_11_10_000001_reset_initial_blank_state')
                        ->exists();
                    if (!$hasBlankReset) {
                        $needsMigrate = true;
                    }
                }

                if ($needsMigrate) {
                    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

                    // Extra guarantee: if migration table has desync, apply columns directly
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('anticipos', 'observacion')) {
                        \Illuminate\Support\Facades\Schema::table('anticipos', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->string('observacion', 255)->nullable();
                        });
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('anticipos', 'dias_debe')) {
                        \Illuminate\Support\Facades\Schema::table('anticipos', function (\Illuminate\Database\Schema\Blueprint $table) {
                            $table->decimal('dias_debe', 8, 2)->default(0);
                        });
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Auto-migration / schema check notice: ' . $e->getMessage());
        }
    }
}
