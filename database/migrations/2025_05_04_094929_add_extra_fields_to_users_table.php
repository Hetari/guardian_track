<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ownership_number')->nullable()->after('email');
            $table->string('company_name')->nullable()->after('ownership_number');
            $table->string('national_id_number')->nullable()->after('company_name');
            $table->string('product_type')->nullable()->after('national_id_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ownership_number',
                'company_name',
                'national_id_number',
                'product_type',
            ]);
        });
    }
};
