<?php

namespace ChrisHolland\Projector;

use InvalidArgumentException;

class OutlierDetector
{
    private array $data;
    public function __construct(
        array $data
    ) {
        // Validate data format [x, y]
        foreach ($data as $point) {
            if (!is_array($point) || count($point) !== 2) {
                throw new InvalidArgumentException("Data points must be [x, y] pairs");
            }
        }
        $this->data = $data;
    }

    public static function fromData(array $data): self
    {
        return new self($data);
    }

    /**
     * Find outliers that when removed, improve R-squared above threshold
     */
    public function getOutliers(float $threshold = 0.8): array
    {
        $baselineStats = $this->calculateRegression($this->data);
        $baselineRSquared = $baselineStats['r_squared'];

        if ($baselineRSquared >= $threshold) {
            return [
                'message' => 'R-squared already meets threshold',
                'baseline_r_squared' => $baselineRSquared,
                'outliers' => []
            ];
        }

        // Calculate impact of each point
        $impacts = [];
        for ($i = 0, $iMax = count($this->data); $i < $iMax; $i++) {
            $testData = array_values(array_filter($this->data, static function ($idx) use ($i) {
                return $idx !== $i;
            }, ARRAY_FILTER_USE_KEY));

            $stats = $this->calculateRegression($testData);
            $impacts[$i] = [
                'index' => $i,
                'point' => $this->data[$i],
                'r_squared' => $stats['r_squared'],
                'improvement' => $stats['r_squared'] - $baselineRSquared
            ];
        }

        // Sort by improvement (highest first)
        usort($impacts, static function ($a, $b) {
            return $b['improvement'] <=> $a['improvement'];
        });

        // Find minimum set of outliers needed
        $removedIndices = [];
        $currentRSquared = $baselineRSquared;
        $outliers = [];

        foreach ($impacts as $impact) {
            if ($currentRSquared >= $threshold) {
                break;
            }

            $idx = $impact['index'];
            $removedIndices[] = $idx;
            $outliers[] = $impact;

            // Recalculate with all selected outliers removed
            $testData = array_values(array_filter($this->data, static function ($idx) use ($removedIndices) {
                return !in_array($idx, $removedIndices, true);
            }, ARRAY_FILTER_USE_KEY));

            $stats = $this->calculateRegression($testData);
            $currentRSquared = $stats['r_squared'];
        }

        return [
            'baseline_r_squared' => $baselineRSquared,
            'achieved_r_squared' => $currentRSquared,
            'meets_threshold' => $currentRSquared >= $threshold,
            'outliers_removed' => count($outliers),
            'outliers' => $outliers
        ];
    }

    /**
     * Calculate linear regression parameters and R-squared
     */
    private function calculateRegression(array $data): array
    {
        $n = count($data);
        if ($n < 2) {
            throw new InvalidArgumentException("Need at least 2 points for regression");
        }

        $sumX = $sumY = $sumXY = $sumX2 = 0;

        foreach ($data as $point) {
            $x = $point[0];
            $y = $point[1];

            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        // Calculate slope (m)
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);

        // Calculate intercept (b)
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Calculate R-squared
        $yMean = $sumY / $n;
        $ssTot = 0;  // Total sum of squares
        $ssRes = 0;  // Residual sum of squares

        foreach ($data as $point) {
            $x = $point[0];
            $y = $point[1];
            $predicted = $slope * $x + $intercept;

            $ssTot += ($y - $yMean) ** 2;
            $ssRes += ($y - $predicted) ** 2;
        }

        $rSquared = 1 - ($ssRes / $ssTot);

        return [
            'slope' => $slope,
            'intercept' => $intercept,
            'r_squared' => $rSquared
        ];
    }
}
