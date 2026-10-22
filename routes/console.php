<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Background Jobs (Otomatisasi Sistem)
// Berjalan setiap menit hanya di hari kerja (Senin - Jumat) pada jam operasional (07:30 - 15:45 WIB)
Schedule::command('tickets:auto-release')
    ->everyMinute()
    ->weekdays()
    ->between('07:30', '15:45');
