<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'tracking_code')) {
                $table->string('tracking_code')->nullable()->after('status');
            }

            if (!Schema::hasColumn('reports', 'company_id')) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->constrained('partner_companies')
                    ->nullOnDelete()
                    ->after('tracking_code');
            }

            if (!Schema::hasColumn('reports', 'lost_ownership_document')) {
                $table->boolean('lost_ownership_document')->default(false)->after('company_id');
            }

            if (!Schema::hasColumn('reports', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('lost_ownership_document');
            }

            if (!Schema::hasColumn('reports', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('reports', 'tracking_code')) {
                $dropColumns[] = 'tracking_code';
            }

            if (Schema::hasColumn('reports', 'company_id')) {
                $dropColumns[] = 'company_id';
            }

            if (Schema::hasColumn('reports', 'lost_ownership_document')) {
                $dropColumns[] = 'lost_ownership_document';
            }

            if (Schema::hasColumn('reports', 'latitude')) {
                $dropColumns[] = 'latitude';
            }

            if (Schema::hasColumn('reports', 'longitude')) {
                $dropColumns[] = 'longitude';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
