<?php

namespace ChrisHolland\Projector;

class ExponentialProjector
{
    /**
     * @var int
     */
    private $numberOfDays;
    /**
     * @var int
     */
    private $initialCount;
    /**
     * @var int
     */
    private $initialIncrement;
    /**
     * @var float
     */
    private $incrementIncreasePercentage;

    public function __construct(
        int $numberOfDays,
        int $initialCount,
        int $initialIncrement,
        float $incrementIncreasePercentage
    ) {
        $this->numberOfDays = $numberOfDays;
        $this->initialCount = $initialCount;
        $this->initialIncrement = $initialIncrement;
        $this->incrementIncreasePercentage = $incrementIncreasePercentage;
    }

    public static function fromSeries(
        array $series,
        int $lastNUnits,
        int $unitsToProject
    ): ExponentialProjector {
        return self::getInstanceFromSeriesAndSpecs(
            $series,
            $lastNUnits,
            $unitsToProject
        );
    }

    public static function fromFile(
        string $dataFile,
        int $lastNUnits,
        int $unitsToProject
    ): ExponentialProjector {
        return self::getInstanceFromSeriesAndSpecs(
            (new FileHandler($dataFile))->getArrayFromLines(),
            $lastNUnits,
            $unitsToProject
        );
    }

    private static function getInstanceFromSeriesAndSpecs(
        array $series,
        int $lastNUnits,
        int $unitsToProject
    ): ExponentialProjector {
        $seriesFragment = array_slice(
            $series,
            -$lastNUnits
        );
        $incrementIncreasePercentage = (
        new Trend(
            $seriesFragment
        )
        )->getAverageIncreasePercentage();
        $secondToLastDayCount = array_slice(
            $seriesFragment,
            -2
        )[0];
        $lastDayCount = array_slice(
            $seriesFragment,
            -1
        )[0];
        $initialIncrement = $lastDayCount - $secondToLastDayCount;

        return new self(
            $unitsToProject,
            $secondToLastDayCount,
            $initialIncrement,
            $incrementIncreasePercentage
        );
    }

    /**
     * @return array<Projection>
     */
    public function getProjections(): array
    {
        $count = $this->initialCount;
        $increment = $this->initialIncrement;
        $deltaIncrementPercentage = $this->incrementIncreasePercentage;

        $projections = [];
        $projections[0] = $this->getProjection($count, 0, 0);

        for ($i = 1; $i < $this->numberOfDays; $i++) {
            $count += $increment;
            $projections[$i] = $this->getProjection($count, $increment, $deltaIncrementPercentage);
            $increment += ($deltaIncrementPercentage * $increment);
        }
        return $projections;
    }

    private function getProjection(float $count, float $increment, float $deltaIncrementPercentage): Projection
    {
        return new Projection(
            $count,
            $increment,
            $deltaIncrementPercentage
        );
    }
}
