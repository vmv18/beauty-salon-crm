<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Отримуємо або створюємо користувачів для майстрів
        $masterUser = User::firstOrCreate(
            ['email' => 'master@beautysalon.com'],
            [
                'name' => 'Майстер',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Створюємо майстрів
        $employees = [
            [
                'user_id' => $masterUser->id,
                'specialization' => 'Стрижка',
                'bio' => 'Професійний стиліст з 10-річним досвідом. Спеціалізується на жіночих та чоловічих стрижках, укладках та стайлінгу.',
                'rating' => 4.8,
                'hire_date' => now()->subYears(5),
                'status' => 'active',
                'work_start_time' => '09:00',
                'work_end_time' => '18:00',
                'min_break_between_appointments' => 15,
            ],
            [
                'user_id' => User::firstOrCreate(
                    ['email' => 'colorist@beautysalon.com'],
                    [
                        'name' => 'Олена Колорист',
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ]
                )->id,
                'specialization' => 'Фарбування',
                'bio' => 'Досвідчений колорист, експерт з фарбування, мелірування та омбре. Працює з найсучаснішими технологіями фарбування.',
                'rating' => 4.9,
                'hire_date' => now()->subYears(7),
                'status' => 'active',
                'work_start_time' => '10:00',
                'work_end_time' => '19:00',
                'min_break_between_appointments' => 20,
            ],
            [
                'user_id' => User::firstOrCreate(
                    ['email' => 'manicurist@beautysalon.com'],
                    [
                        'name' => 'Марія Манікюрист',
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ]
                )->id,
                'specialization' => 'Манікюр',
                'bio' => 'Майстер манікюру та педикюру. Виконує класичний та апаратний манікюр, створює унікальні дизайни нігтів.',
                'rating' => 4.7,
                'hire_date' => now()->subYears(3),
                'status' => 'active',
                'work_start_time' => '09:00',
                'work_end_time' => '18:00',
                'min_break_between_appointments' => 10,
            ],
            [
                'user_id' => User::firstOrCreate(
                    ['email' => 'vizazhist@beautysalon.com'],
                    [
                        'name' => 'Анна Візажист',
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                    ]
                )->id,
                'specialization' => 'Макіяж',
                'bio' => 'Професійний візажист. Створює денний, вечірній та весільний макіяж. Має сертифікати від провідних косметичних брендів.',
                'rating' => 5.0,
                'hire_date' => now()->subYears(4),
                'status' => 'active',
                'work_start_time' => '10:00',
                'work_end_time' => '19:00',
                'min_break_between_appointments' => 15,
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = Employee::firstOrCreate(
                ['user_id' => $employeeData['user_id']],
                $employeeData
            );

            // Призначаємо послуги майстру на основі спеціалізації
            if ($employee->wasRecentlyCreated) {
                $services = Service::where('is_active', true)->get();
                
                // Призначаємо послуги за спеціалізацією
                if ($employee->specialization === 'Стрижка') {
                    $employee->services()->attach(
                        $services->whereIn('name', ['Жіноча стрижка', 'Чоловіча стрижка', 'Укладка волосся', 'Вечірня укладка'])->pluck('id')
                    );
                } elseif ($employee->specialization === 'Фарбування') {
                    $employee->services()->attach(
                        $services->whereIn('name', ['Повне фарбування', 'Мелірування', 'Омбре/Балаяж', 'Тонування', 'Корекція коренів'])->pluck('id')
                    );
                } elseif ($employee->specialization === 'Манікюр') {
                    $employee->services()->attach(
                        $services->whereIn('name', ['Класичний манікюр', 'Апаратний манікюр', 'Покриття гель-лаком', 'Класичний педікюр', 'Апаратний педікюр'])->pluck('id')
                    );
                } elseif ($employee->specialization === 'Макіяж') {
                    $employee->services()->attach(
                        $services->whereIn('name', ['Денний макіяж', 'Вечірній макіяж', 'Весільний макіяж'])->pluck('id')
                    );
                }
            }
        }

        $this->command->info('Майстрів успішно створено!');
    }
}
