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
        if (!Schema::hasTable('customer_records')) {
            Schema::create('customer_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
                $table->foreignId('operator_id')->constrained('operators')->onDelete('restrict');
                $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
                $table->foreignId('sales_id')->nullable()->constrained('sales')->onDelete('restrict');
                $table->string('phone', 9);
                $table->longText('schedule_1')->nullable();
                $table->longText('schedule_2')->nullable();
                $table->longText('schedule_3')->nullable();
                $table->boolean('status')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_records');
    }
};
