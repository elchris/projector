<?php

namespace ChrisHolland\Projector\Test;

use ChrisHolland\Projector\OutlierDetector;
use PHPUnit\Framework\TestCase;

class OutlierFilteringTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function testOutlierDetection(): void
    {
        $data = [
            [1, 2], [2, 3.9], [3, 6.1], [4, 8], [5, 9.8],
            [6, 12.1], [7, 14], [8, 16.2], [9, 17.9], [10, 20],
            [5, 25]  // This is an outlier affecting R-squared
        ];

        $detector = OutlierDetector::fromData($data);
        $result = $detector->getOutliers(0.8);
        $directory = __DIR__ . '/Files';
        self::assertDirectoryExists($directory);
        $expectedOutliers = json_decode(
            file_get_contents($directory . '/outliers.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertSame(
            $expectedOutliers,
            $result
        );
    }
}
