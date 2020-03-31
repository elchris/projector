<?php

namespace ChrisHolland\Projector\Test;

use ChrisHolland\Projector\Trend;
use PHPUnit\Framework\TestCase;

class TrendTest extends TestCase
{
    public function testGetAverageIncreaseFromSeries(): void
    {
        $series = [
            120,
            150,
            195,
            262.5,
            363.75, //101.25 -> 50.625 -> 151.875
            515.625, //151.875 -> 75.9375 -> 227.8125
            743.4375
        ];

        $expectedAveragePercentageIncrease = 0.5;

        $trend = new Trend($series);

        self::assertEquals($expectedAveragePercentageIncrease, $trend->getAverageIncreasePercentage());
    }
}
