<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('type', ['stolen', 'lost']);
            $table->string('product_name');
            $table->string('serial_code');
            $table->dateTime('date_time');
            $table->string('country');
            $table->string('city');
            $table->string('street_address');
            $table->string('purchase_location');

            // TODO: another table for this and fk
            $table->string('item_type');
            $table->enum('status', ['received', 'checking_brand', 'checking_company', 'transferred_to_police', 'done'])->default('received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
