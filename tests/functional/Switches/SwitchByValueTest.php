<?php

namespace Rikta\CallbackSwitch\Test\Functional\Switches;

use Generator;
use Rikta\CallbackSwitch\Switches\SwitchByValue;
use PHPUnit\Framework\TestCase;

class SwitchByValueTest extends TestCase
{

    /**
     * @param int|string $value value to pass
     * @param string $expected expected result
     * @dataProvider correctCaseDataProvider
     */
    public static function testCorrectCaseIsChosen($value, string $expected): void
    {
        $switch = new SwitchByValue([
            'a' => static fn () => 'A',
            'b' => static fn () => 'B',
            SwitchByValue::DEFAULT => fn () => 'default'
        ]);

        $result = $switch($value);

        self::assertEquals($expected, $result);
    }

    /**
     * @return Generator<mixed>
     */
    public function correctCaseDataProvider(): Generator
    {
        yield [
            'a',
            'A'
        ];

        yield [
            'b',
            'B'
        ];

        yield [
            'unknown',
            'default'
        ];
    }
}
