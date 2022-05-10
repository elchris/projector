<?php

namespace ChrisHolland\Projector\Test;

use ChrisHolland\Projector\Incrementor;
use PHPUnit\Framework\TestCase;

class TinyStringIncrementorTest extends TestCase
{
    public function testIncrementIdentifier(): void
    {
        $characters = Incrementor::CHARACTERS;

        $array = str_split($characters);

        self::assertCount(62, $array);

        $expected = [
            'a' => 'b',
            'b' => 'c',
            'c' => 'd',
            'z' => 'A',
            'Z' => '0',
            '9' => 'aa',
            'aa' => 'ab',
            'ab' => 'ac',
            'ac' => 'ad',
            'az' => 'aA',
            'aZ' => 'a0',
            'a9' => 'aaa',
            'aaa' => 'aab',
            'aab' => 'aac',
            'aac' => 'aad',
            'aaz' => 'aaA',
            'aaZ' => 'aa0',
            'aa9' => 'aba',
            'aba' => 'abb',
            'abb' => 'abc',
            'abc' => 'abd',
            'abz' => 'abA',
            'abZ' => 'ab0',
            'ab9' => 'aca',
            'aca' => 'acb',
            'acb' => 'acc',
            'acc' => 'acd',
            'acz' => 'acA',
            'acZ' => 'ac0',
            'ac9' => 'ada',
            'a9a' => 'a9b',
            'a99' => 'aaaa'
        ];

        $incrementor = new \ChrisHolland\Projector\Incrementor();

        self::assertSame(
            'b',
            $incrementor->getNextPosition('a')
        );

        self::assertSame(
            'd',
            $incrementor->getNextPosition('c')
        );

        self::assertSame(
            'A',
            $incrementor->getNextPosition('z')
        );

        self::assertSame(
            '0',
            $incrementor->getNextPosition('Z')
        );

        self::assertSame(
            'a',
            $incrementor->getNextPosition('9')
        );


        foreach ($expected as $input => $output) {
            self::assertSame(
                $output,
                $incrementor->getNextIdentifier($input),
                'from: ' . $input
            );
        }

        $current = 'a';
        $counter = 0;
        while ((strlen($current) < 4) && ($counter < 10000)) {
            $counter++;
            $current = $incrementor->getNextIdentifier($current);
            print " - ".$current;
        }
        print("\n\n".$counter);
    }
}
