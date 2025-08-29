<?php 

namespace App\Service;

class DateValidatorService
{
    public function isStartInPast(string $date, string $hour = '00:00'): bool
    {
        try {
            $start = new \DateTimeImmutable("$date $hour");
        } catch (\Exception $e) {
            return true;
        }
        $now = new \DateTimeImmutable();
        return $start < $now;
    }

    public function isEndAfterStart(string $startDate, string $startHour, string $endDate, string $endHour): bool
    {
        try {
            $start = new \DateTimeImmutable("$startDate $startHour");
            $end = new \DateTimeImmutable("$endDate $endHour");
        } catch (\Exception $e) {
            return false; 
        }
        return $end > $start;
    }
}
