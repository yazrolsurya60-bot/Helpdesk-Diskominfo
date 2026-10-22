<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class SlaService
{
    /**
     * Jam operasional buka (07:30 WIB)
     */
    public const WORK_START_HOUR = 7;
    public const WORK_START_MINUTE = 30;

    /**
     * Jam operasional tutup (15:45 WIB)
     */
    public const WORK_END_HOUR = 15;
    public const WORK_END_MINUTE = 45;

    /**
     * Batas default durasi SLA (2 Jam = 120 Menit Kerja)
     */
    public const DEFAULT_SLA_HOURS = 2;
    public const DEFAULT_SLA_MINUTES = 120;

    /**
     * Menghitung total menit kerja aktif yang telah berjalan antara dua waktu.
     * Hanya menghitung hari kerja (Senin - Jumat) dan jam kerja (07:30 - 15:45).
     *
     * @param CarbonInterface|string|null $start Waktu awal tiket diambil (assigned_at)
     * @param CarbonInterface|string|null $end Waktu pembanding (default: now())
     * @return int Jumlah menit kerja aktif
     */
    public static function getWorkingMinutesElapsed(CarbonInterface|string|null $start, CarbonInterface|string|null $end = null): int
    {
        if (!$start) {
            return 0;
        }

        $startDate = $start instanceof CarbonInterface ? $start->copy() : Carbon::parse($start);
        $endDate = $end ? ($end instanceof CarbonInterface ? $end->copy() : Carbon::parse($end)) : Carbon::now();

        if ($startDate->gte($endDate)) {
            return 0;
        }

        $totalMinutes = 0;
        $current = $startDate->copy()->startOfDay();
        $endDay = $endDate->copy()->startOfDay();

        while ($current->lte($endDay)) {
            // Hari kerja: Senin (1) s.d. Jumat (5). Weekend dilewati.
            if (!$current->isWeekend()) {
                $workStart = $current->copy()->setTime(self::WORK_START_HOUR, self::WORK_START_MINUTE, 0);
                $workEnd = $current->copy()->setTime(self::WORK_END_HOUR, self::WORK_END_MINUTE, 0);

                // Tentukan batas awal efektif pada hari ini
                $effectiveStart = $current->isSameDay($startDate)
                    ? ($startDate->gt($workStart) ? $startDate->copy() : $workStart)
                    : $workStart;

                // Tentukan batas akhir efektif pada hari ini
                $effectiveEnd = $current->isSameDay($endDate)
                    ? ($endDate->lt($workEnd) ? $endDate->copy() : $workEnd)
                    : $workEnd;

                // Akumulasikan hanya jika rentang waktu valid
                if ($effectiveEnd->gt($effectiveStart)) {
                    $totalMinutes += $effectiveStart->diffInMinutes($effectiveEnd);
                }
            }

            $current->addDay();
        }

        return $totalMinutes;
    }

    /**
     * Memeriksa apakah tiket telah melewati batas SLA jam kerja aktif.
     *
     * @param CarbonInterface|string|null $assignedAt Waktu penugasan tiket
     * @param int $maxHours Batas SLA dalam jam (default: 2 jam)
     * @param CarbonInterface|string|null $now Waktu pembanding
     * @return bool
     */
    public static function isPastSla(CarbonInterface|string|null $assignedAt, int $maxHours = self::DEFAULT_SLA_HOURS, CarbonInterface|string|null $now = null): bool
    {
        if (!$assignedAt) {
            return false;
        }

        $minutesElapsed = self::getWorkingMinutesElapsed($assignedAt, $now);

        return $minutesElapsed >= ($maxHours * 60);
    }

    /**
     * Mengambil durasi jam kerja yang sudah berjalan dalam bentuk angka desimal.
     *
     * @param CarbonInterface|string|null $assignedAt
     * @param CarbonInterface|string|null $now
     * @return float
     */
    public static function getWorkingHoursHeld(CarbonInterface|string|null $assignedAt, CarbonInterface|string|null $now = null): float
    {
        $minutes = self::getWorkingMinutesElapsed($assignedAt, $now);

        return round($minutes / 60, 1);
    }
}
