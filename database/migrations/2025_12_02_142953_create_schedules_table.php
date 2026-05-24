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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('day_of_week')->comment('1 = Понеділок, 2 = Вівторок, ..., 7 = Неділя');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->time('break_start')->nullable()->comment('Початок обідньої перерви');
            $table->time('break_end')->nullable()->comment('Кінець обідньої перерви');
            $table->boolean('is_working')->default(true)->comment('Чи працює майстер в цей день');
            $table->timestamps();
            
            // Унікальний індекс: один майстер не може мати два розклади на один день
            $table->unique(['employee_id', 'day_of_week']);
            
            // Індекси для швидкого пошуку
            $table->index('employee_id');
            $table->index('day_of_week');
            $table->index('is_working');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
