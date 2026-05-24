<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class EmployeePhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Мапінг спеціалізацій до фото
        $specializationPhotoMap = [
            'Стрижка' => 'employees/master-stylist.png',
            'Фарбування' => 'employees/master-colorist.png',
            'Манікюр' => 'employees/master-manikyurist.png',
            'Педикюр' => 'employees/master-manikyurist.png',
            'Макіяж' => 'employees/master-vizazhist.png',
            'Візажист' => 'employees/master-vizazhist.png',
            'Стиліст' => 'employees/master-stylist.png',
            'Колорист' => 'employees/master-colorist.png',
            'Манікюрист' => 'employees/master-manikyurist.png',
        ];

        $employees = Employee::with('user')->get();

        foreach ($employees as $employee) {
            $photoPath = null;

            // Визначаємо фото на основі спеціалізації
            if ($employee->specialization) {
                // Перевіряємо точну відповідність
                if (isset($specializationPhotoMap[$employee->specialization])) {
                    $photoPath = $specializationPhotoMap[$employee->specialization];
                } else {
                    // Перевіряємо часткову відповідність
                    foreach ($specializationPhotoMap as $spec => $path) {
                        if (stripos($employee->specialization, $spec) !== false) {
                            $photoPath = $path;
                            break;
                        }
                    }
                }
            }

            // Якщо не знайдено за спеціалізацією, використовуємо дефолтне
            if (!$photoPath) {
                $photoPath = 'employees/master-stylist.png';
            }

            // Перевіряємо, чи файл існує
            if (Storage::disk('public')->exists($photoPath)) {
                // Оновлюємо фото, якщо воно не встановлено або відрізняється
                if (!$employee->photo || $employee->photo !== $photoPath) {
                    $employee->update(['photo' => $photoPath]);
                    $specialization = $employee->specialization ?? 'без спеціалізації';
                    $this->command->info("Оновлено фото для: {$employee->user->name} ({$specialization})");
                } else {
                    $this->command->warn("Фото вже встановлено для: {$employee->user->name}");
                }
            } else {
                $this->command->error("Файл не знайдено: {$photoPath}");
            }
        }

        $this->command->info('Фото співробітників успішно оновлено!');
    }
}
