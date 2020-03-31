<?php

namespace ChrisHolland\Projector;

class Trend
{
    /**
     * @var array
     */
    private $series;

    /**
     * Trend constructor.
     * @param array<float> $series
     */
    public function __construct(array $series)
    {
        $this->series = $series;
    }

    public function getAverageIncreasePercentage() : float
    {
        $count = 0;
        $previousEntry = 0;
        $previousDelta = 0;
        $cumulativeDeltaIncrementPercentage = 0;
        foreach ($this->series as $entry) {
            if ($previousEntry !== 0) {
                $delta = $entry - $previousEntry;
                if ($previousDelta !== 0) {
                    $deltaIncrement = ($delta - $previousDelta);
                    $deltaIncrementPercentage = $deltaIncrement / $previousDelta;
                    $cumulativeDeltaIncrementPercentage += $deltaIncrementPercentage;
                    $count++;
                }
                $previousDelta = $delta;
            }
            $previousEntry = $entry;
        }

        return $cumulativeDeltaIncrementPercentage / $count;
    }
}
