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

        // Auto-run pending migrations if any schema update is missing in production
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('anticipos')) {
                if (!\Illuminate\Support\Facades\Schema::hasColumn('anticipos', 'dias_debe') || !\Illuminate\Support\Facades\Schema::hasColumn('anticipos', 'observacion')) {
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
