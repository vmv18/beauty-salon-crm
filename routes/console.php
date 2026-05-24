<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('appointments:send-reminders --hours=24')
    ->dailyAt('09:00')
    ->timezone('Europe/Kyiv')
    ->description('Send 24-hour appointment reminders');

Schedule::command('appointments:send-reminders --hours=2')
    ->hourly()
    ->timezone('Europe/Kyiv')
    ->description('Send 2-hour appointment reminders');
