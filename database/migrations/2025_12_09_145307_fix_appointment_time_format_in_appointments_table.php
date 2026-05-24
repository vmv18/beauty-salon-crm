<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Виправити існуючі записи з неправильним форматом appointment_time
        $appointments = DB::table('appointments')->get();
        
        foreach ($appointments as $appointment) {
            $timeValue = $appointment->appointment_time;
            
            // Якщо це вже правильний формат H:i:s, пропустити
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $timeValue)) {
                continue;
            }
            
            // Якщо це формат H:i, додати секунди
            if (preg_match('/^\d{2}:\d{2}$/', $timeValue)) {
                DB::table('appointments')
                    ->where('id', $appointment->id)
                    ->update(['appointment_time' => $timeValue . ':00']);
                continue;
            }
            
            // Якщо містить подвійний час або datetime, витягнути останній час
            if (preg_match('/(\d{2}:\d{2}:\d{2})\s*$/', $timeValue, $matches)) {
                DB::table('appointments')
                    ->where('id', $appointment->id)
                    ->update(['appointment_time' => $matches[1]]);
                continue;
            }
            
            // Спробувати витягнути час з datetime формату
            if (preg_match('/(\d{2}:\d{2}:\d{2})/', $timeValue, $matches)) {
                DB::table('appointments')
                    ->where('id', $appointment->id)
                    ->update(['appointment_time' => $matches[1]]);
                continue;
            }
            
            // Якщо містить пробіли, спробувати витягнути останній час
            if (strpos($timeValue, ' ') !== false) {
                $parts = explode(' ', trim($timeValue));
                foreach (array_reverse($parts) as $part) {
                    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $part)) {
                        DB::table('appointments')
                            ->where('id', $appointment->id)
                            ->update(['appointment_time' => $part]);
                        break;
                    }
                    if (preg_match('/^\d{2}:\d{2}$/', $part)) {
                        DB::table('appointments')
                            ->where('id', $appointment->id)
                            ->update(['appointment_time' => $part . ':00']);
                        break;
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Неможливо відкотити виправлення даних
    }
};
