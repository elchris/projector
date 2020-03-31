<?php

namespace ChrisHolland\Projector\Test;

use ChrisHolland\Projector\ExponentialProjector;
use ChrisHolland\Projector\Projection;
use ChrisHolland\Projector\Trend;
use PHPUnit\Framework\TestCase;

class ProjectionTest extends TestCase
{
    private const SERIES_ONGOING = [
        4727,
        6507,
        9421,
        14332,
        19762,
        26881,
        35226,
        46455,
        55225,
        69222,
        86043,
        104845,
        124676,
        143101,
        164670
    ];

    public const SERIES_STATIC = [
        4727,
        6507,
        9421,
        14332,
        19762,
        26881,
        35226,
        46455,
        55225,
        69222,
        86043,
        104845,
        124676,
        143101,
        164669
    ];

    /**
     * @param array $projections
     */
    public static function assertDataFromStaticSeries(array $projections): void
    {
        self::assertEquals(
            self::getProjection(143101, 0, 0.0),
            $projections[0]
        );

        $projectedGrowthRate = 0.23906886632162305;

        self::assertEquals(
            self::getProjection(164669, 21568, $projectedGrowthRate),
            $projections[1]
        );

        self::assertEquals(
            self::getProjection(56048006.2831671, 10803830.756885238, $projectedGrowthRate),
            $projections[30]
        );

        self::assertEquals(
            self::getProjection(385539405.8258587, 74376677.6683254, $projectedGrowthRate),
            $projections[39]
        );
    }

    /**
     * day 1 -- 100
     * day 2 -- + 20  -- 120
     * day 3 -- + 30  -- 150
     * day 4 -- + 45  -- 195 -- 15
     * day 5 -- + 67.5  -- 262.5
     * day 6 -- + 101.25  -- 363.75
     */

    public function testProjection() : void
    {
        $numberOfDays = 6;
        $initialCount = 100;
        $initialIncrement = 20;
        $incrementIncreasePercentage = 0.5;

        $projector = new ExponentialProjector(
            $numberOfDays,
            $initialCount,
            $initialIncrement,
            $incrementIncreasePercentage
        );

        $projections = $projector->getProjections();

        self::assertEquals(
            self::getProjection(
                100,
                0,
                0
            ),
            $projections[0]
        );
        self::assertEquals(self::getProjection(
            120,
            20,
            0.5
        ), $projections[1]);

        self::assertEquals(self::getProjection(
            150,
            30,
            0.5
        ), $projections[2]);

        self::assertEquals(self::getProjection(
            195,
            45,
            0.5
        ), $projections[3]);

        self::assertEquals(self::getProjection(
            262.5,
            67.5,
            0.5
        ), $projections[4]);

        self::assertEquals(self::getProjection(
            363.75,
            101.25,
            0.5
        ), $projections[5]);
    }

    public function testProjectionFromSeries() : void
    {
        $seriesDays = [15, 6, 4, 3];
        foreach ($seriesDays as $lastNDays) {
            $this->showProjectionsFromSeries(
                array_slice(
                    self::SERIES_ONGOING,
                    -$lastNDays
                ),
                $lastNDays
            );
        }
    }

    public function testProjectorFromSeries() : void
    {
        /** @var ExponentialProjector $projector */
        $projector = ExponentialProjector::fromSeries(
            self::SERIES_STATIC,
            15,
            200
        );

        $projections = $projector->getProjections();

        self::assertDataFromStaticSeries($projections);
    }

    private function show($day, $projections): void
    {
        print 'day: '.$day.': '.$projections[$day];
    }

    private static function getProjection(float $total, float $delta, float $deltaIncrementPercentage): Projection
    {
        return new Projection(
            $total,
            $delta,
            $deltaIncrementPercentage
        );
    }

    private function getAverageIncreasePercentage(array $series): float
    {
        return (new Trend(
            $series
        ))->getAverageIncreasePercentage();
    }

    private function showProjectionsFromSeries(array $series, int $days): void
    {
        $secondToLastDayCount = array_slice($series, -2)[0];
        $lastDayCount = array_slice($series, -1)[0];
        $initialIncrement = $lastDayCount - $secondToLastDayCount;
        $showLastDayCount = number_format($secondToLastDayCount);

        $incrementIncreasePercentage = $this->getAverageIncreasePercentage(
            $series
        );

        print "\n*********************************************************\n";
        print   "* Projecting from $showLastDayCount for $days-day delta rate: "
                . number_format($incrementIncreasePercentage, 4);
        print "\n*********************************************************\n";

        $numberOfDaysToProject = 400;
        $projections = (new ExponentialProjector(
            $numberOfDaysToProject,
            $secondToLastDayCount,
            $initialIncrement,
            $incrementIncreasePercentage
        ))->getProjections();

        self::assertCount($numberOfDaysToProject, $projections);
        $this->show(0, $projections);
        $this->show(1, $projections);
        $this->show(10, $projections);
        $this->show(14, $projections);
        $this->show(29, $projections);
        $this->show(30, $projections);
        $this->show(35, $projections);
        $this->show(39, $projections);
        $this->show(45, $projections);
        $this->show(48, $projections);
        $this->show(50, $projections);
        $this->show(60, $projections);
        $this->show(120, $projections);
        $this->show(199, $projections);
        $this->show(($numberOfDaysToProject - 1), $projections);
    }
}
