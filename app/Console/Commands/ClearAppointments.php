<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class ClearAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:clear {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Видалити всі бронювання з бази даних';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Appointment::count();
        
        if ($count === 0) {
            $this->info('Таблиця appointments вже порожня.');
            return 0;
        }
        
        if (!$this->option('force')) {
            if (!$this->confirm("Ви впевнені, що хочете видалити всі {$count} бронювань? Цю дію неможливо скасувати!")) {
                $this->info('Операцію скасовано.');
                return 0;
            }
        }
        
        try {
            DB::beginTransaction();
            
            // Видалити всі записи
            Appointment::query()->delete();
            
            DB::commit();
            
            $this->info("Успішно видалено {$count} бронювань.");
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Помилка при видаленні: ' . $e->getMessage());
            return 1;
        }
    }
}
