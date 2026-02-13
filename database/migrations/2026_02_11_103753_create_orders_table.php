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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_number')->primary();
            $table->date('order_date');
            $table->string('e_commerce');
            $table->string('customer_name');
            $table->enum('status', ['pending', 'processing', 'shipped', 'completed'])
                  ->default('pending');
            $table->decimal('gross_total',15,2)->default(0);
            $table->decimal('net_payout',15,2)->default(0);
            $table->decimal('net_total',15,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
