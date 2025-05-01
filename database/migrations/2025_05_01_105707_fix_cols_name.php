<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('product_name', 'customer_name');
            $table->dropColumn('purchase_location');
            $table->foreignId('company_id')
                ->nullable()
                ->after('status')
                ->constrained('partner_companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->boolean('lost_ownership_document')
                ->after('status')
                ->default(false);
            $table->string('tracking_code')
                ->after('status')
                ->unique();
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('customer_name', 'product_name');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            $table->dropColumn('lost_ownership_document');
            $table->dropColumn('tracking_code');
        });
    }
};
