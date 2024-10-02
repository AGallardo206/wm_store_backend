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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('sales_user_id')->constrained('sales_users')->onDelete('restrict');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('restrict');
            $table->foreignId('typification_id')->constrained('typifications')->onDelete('restrict');
            $table->foreignId('operator_id')->constrained('operators')->onDelete('restrict');
            $table->foreignId('sales_type_id')->constrained('sales_types')->onDelete('restrict');
            $table->string('origin');
            $table->string('phone',9);
            $table->string('equip')->nullable();
            $table->string('imei',15)->unique()->nullable();
            $table->string('sales_order',9)->unique();
            $table->longText('notes')->comment('Observations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
