<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| VSP TIMES E-Paper Publication Scheduler
|--------------------------------------------------------------------------
|
| - Standard daily publication runs at 05:00 AM IST (Asia/Kolkata).
| - Every-minute check runs with withoutOverlapping to guarantee that any
|   scheduled edition releases precisely on time with duplicate prevention.
|
*/

Schedule::command('epaper:publish-scheduled')
    ->timezone('Asia/Kolkata')
    ->dailyAt('05:00')
    ->name('epaper-daily-morning-release')
    ->withoutOverlapping();

Schedule::command('epaper:publish-scheduled')
    ->everyMinute()
    ->name('epaper-publish-catchup')
    ->withoutOverlapping();
