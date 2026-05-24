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
        Schema::table('employees', function (Blueprint $table) {
            $table->time('work_start_time')->default('09:00:00')->after('status')->comment('Час початку робочого дня');
            $table->time('work_end_time')->default('18:00:00')->after('work_start_time')->comment('Час закінчення робочого дня');
            $table->integer('min_break_between_appointments')->default(15)->after('work_end_time')->comment('Мінімальний проміжок між записами в хвилинах');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['work_start_time', 'work_end_time', 'min_break_between_appointments']);
        });
    }
};
