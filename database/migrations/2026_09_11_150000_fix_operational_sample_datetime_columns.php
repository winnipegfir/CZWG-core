<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['network_operational_airport_samples', 'network_operational_position_samples'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'sampled_at')) {
                continue;
            }

            // Every valid operational sample is aligned to a five-minute UTC
            // boundary. Remove rows whose timestamp was changed by legacy
            // MySQL TIMESTAMP auto-update behaviour before correcting the type.
            DB::table($table)
                ->whereRaw(
                    'SECOND(`sampled_at`) <> 0 '
                    .'OR MOD(MINUTE(`sampled_at`), 5) <> 0 '
                    .'OR (`created_at` IS NOT NULL AND `sampled_at` > `created_at`)'
                )
                ->delete();

            // Production uses legacy TIMESTAMP defaults. DATETIME prevents an
            // update to counts/updated_at from silently replacing sampled_at.
            DB::statement("ALTER TABLE `{$table}` MODIFY `sampled_at` DATETIME NOT NULL");
        }
    }

    public function down(): void
    {
        // Intentionally retain DATETIME. Reintroducing legacy TIMESTAMP
        // auto-update behaviour could corrupt historical sample boundaries.
    }
};
