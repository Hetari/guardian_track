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
        Schema::create('lost_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('serial_code');
            $table->dateTime('lost_date_time');
            $table->string('phone');
            $table->string('country');
            $table->string('city');
            $table->string('street_address');
            $table->string('id_card_image')->nullable();
            $table->string('purchase_location');
            $table->string('files')->nullable();
            $table->enum('lost_item_type', ['Bag', 'Shoe', 'Watch', 'Other']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_items');
    }
};
