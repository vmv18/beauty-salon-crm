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
        Schema::create('time_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable()->comment('Якщо null - весь день заблоковано');
            $table->time('end_time')->nullable()->comment('Якщо null - весь день заблоковано');
            $table->enum('type', ['vacation', 'sick_leave', 'other'])->default('other');
            $table->string('reason')->nullable()->comment('Причина блокування');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Індекси для швидкого пошуку
            $table->index('employee_id');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('type');
            // Композитний індекс для перевірки перекриття
            $table->index(['employee_id', 'start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_blocks');
    }
};
