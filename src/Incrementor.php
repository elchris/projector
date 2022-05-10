<?php

namespace ChrisHolland\Projector;

class Incrementor
{
    public const CHARACTERS =
        'abcdefghijklmnopqrstuvwxyz'
    .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
    .'0123456789';

    private array $sequence;
    private array $map;
    private string $lastCharacter;

    public function __construct()
    {
        $this->sequence = str_split(self::CHARACTERS);
        $this->map = [];
        foreach ($this->sequence as $index => $value) {
            $this->map[$value] = $index;
        }
        $this->firstCharacter = $this->sequence[0];
        $this->lastCharacter = $this->sequence[sizeof($this->sequence) - 1];
    }

    public function getNextIdentifier(string $input): string
    {
        $inputArray = str_split($input);
        $inputSize = sizeof($inputArray);
        $lastCharacter = end($inputArray);
        $secondToLastCharacter = null;
        if ($inputSize > 2) {
            $secondToLastCharacter = $inputArray[$inputSize - 2];
        }
        $nextLastCharacter = $this->getNextPosition($lastCharacter);
        $hasCycledLast = false;
        if ($nextLastCharacter === $this->firstCharacter) {
            $hasCycledLast = true;
        }
        $outputString = '';
        foreach ($inputArray as $index => $value) {
            if ($hasCycledLast && ($inputSize >= 3) && ($index === ($inputSize - 2))) {
                $outputString .= $this->getNextPosition($value);
            } elseif ($index < ($inputSize - 1)) {
                $outputString .= $value;
            } else {
                $outputString .= $nextLastCharacter;
            }
        }
        if ($hasCycledLast
            &&
            (
                ($secondToLastCharacter === null)
                ||
                (
                $secondToLastCharacter === $this->lastCharacter
                )
            )
        ) {
            return $outputString . $nextLastCharacter;
        }
        return $outputString;
    }

    public function getNextPosition(string $character): string
    {
        $characterPosition = $this->map[$character];
        $nextCharacterPosition = $characterPosition + 1;
        if ($nextCharacterPosition > (sizeof($this->sequence) - 1)) {
            $nextCharacterPosition = 0;
        }
        return $this->sequence[$nextCharacterPosition];
    }
}
