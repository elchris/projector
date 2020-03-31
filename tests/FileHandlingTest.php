<?php

namespace ChrisHolland\Projector\Test;

use ChrisHolland\Projector\ExponentialProjector;
use ChrisHolland\Projector\FileHandler;
use PHPUnit\Framework\TestCase;

class FileHandlingTest extends TestCase
{
    public function testReadFileIntoArray() : void
    {
        $stringDataForFile = "one\ntwo\nthree\nfour";
        $fileName = $this->getFileWithStringData($stringDataForFile);
        $arrayToGetFromFile = ['one', 'two', 'three', 'four'];

        $fileHandler = new FileHandler($fileName);
        $arrayFromFile = $fileHandler->getArrayFromLines();

        self::assertEquals($arrayToGetFromFile, $arrayFromFile);
    }

    public function testProjectionFromFile() : void
    {
        $stringDataForFile = implode("\n", ProjectionTest::SERIES_STATIC);
        $fileName = $this->getFileWithStringData($stringDataForFile);

        /** @var ExponentialProjector $projector */
        $projector = ExponentialProjector::fromFile(
            $fileName,
            15,
            200
        );
        $projections = $projector->getProjections();

        ProjectionTest::assertDataFromStaticSeries($projections);
    }

    /**
     * @param string $stringDataForFile
     * @return string
     */
    private function getFileWithStringData(string $stringDataForFile): string
    {
        $fileName = 'series.txt';
        file_put_contents($fileName, (string)($stringDataForFile));
        self::assertFileExists($fileName);

        return $fileName;
    }
}
