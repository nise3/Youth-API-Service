<?php

namespace App\Services;

use App\Models\Youth;

class StatisticsService
{
    public function getNiseStatistics(): int
    {
        return $this->getTotalYouth();
    }

    private function getTotalYouth(): int
    {
        return Youth::count('id');
    }
}
