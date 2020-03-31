<?php

namespace ChrisHolland\Projector;

class FileHandler
{
    /**
     * @var string
     */
    private $fileName;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getArrayFromLines() : array
    {
        $array = [];
        $handle = fopen($this->fileName, 'rb');
        while (!feof($handle)) {
            $array []= trim(fgets($handle));
        }
        return $array;
    }
}
