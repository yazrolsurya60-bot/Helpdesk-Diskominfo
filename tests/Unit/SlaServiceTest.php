<?php

namespace Tests\Unit;

use App\Services\SlaService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SlaServiceTest extends TestCase
{
    public function test_same_day_working_hours(): void
    {
        // Senin 08:00 s.d. 10:30 (2 jam 30 menit = 150 menit)
        $start = Carbon::parse('2026-10-19 08:00:00'); // Senin
        $end = Carbon::parse('2026-10-19 10:30:00');

        $minutes = SlaService::getWorkingMinutesElapsed($start, $end);
        $this->assertSame(150, $minutes);
        $this->assertTrue(SlaService::isPastSla($start, 2, $end));
    }

    public function test_outside_office_hours_are_not_counted(): void
    {
        // Senin 18:00 s.d. 21:00 (di luar jam 07:30 - 15:45)
        $start = Carbon::parse('2026-10-19 18:00:00');
        $end = Carbon::parse('2026-10-19 21:00:00');

        $minutes = SlaService::getWorkingMinutesElapsed($start, $end);
        $this->assertSame(0, $minutes);
        $this->assertFalse(SlaService::isPastSla($start, 2, $end));
    }

    public function test_overnight_pause_and_resume(): void
    {
        // Diambil Senin 15:00 (kerja sampai 15:45 = 45 menit).
        // Dicek Selasa 08:00 (kantor buka 07:30, kerja 30 menit).
        // Total = 45 + 30 = 75 menit (< 120 menit).
        $start = Carbon::parse('2026-10-19 15:00:00'); // Senin
        $end = Carbon::parse('2026-10-20 08:00:00');   // Selasa

        $minutes = SlaService::getWorkingMinutesElapsed($start, $end);
        $this->assertSame(75, $minutes);
        $this->assertFalse(SlaService::isPastSla($start, 2, $end));

        // Dicek Selasa 08:45 (kantor buka 07:30, kerja 75 menit).
        // Total = 45 + 75 = 120 menit (tepat 2 jam kerja).
        $end2 = Carbon::parse('2026-10-20 08:45:00');
        $minutes2 = SlaService::getWorkingMinutesElapsed($start, $end2);
        $this->assertSame(120, $minutes2);
        $this->assertTrue(SlaService::isPastSla($start, 2, $end2));
    }

    public function test_weekend_pause_from_friday_to_monday(): void
    {
        // Diambil Jumat 15:15 (kerja 30 menit sampai 15:45).
        // Sabtu & Minggu dilewati.
        // Dicek Senin 08:30 (kerja 60 menit dari 07:30).
        // Total = 30 + 60 = 90 menit (< 120 menit).
        $start = Carbon::parse('2026-10-16 15:15:00'); // Jumat
        $end = Carbon::parse('2026-10-19 08:30:00');   // Senin

        $minutes = SlaService::getWorkingMinutesElapsed($start, $end);
        $this->assertSame(90, $minutes);
        $this->assertFalse(SlaService::isPastSla($start, 2, $end));

        // Dicek Senin 09:00 (kerja 90 menit dari 07:30).
        // Total = 30 + 90 = 120 menit.
        $end2 = Carbon::parse('2026-10-19 09:00:00');
        $this->assertTrue(SlaService::isPastSla($start, 2, $end2));
    }
}
