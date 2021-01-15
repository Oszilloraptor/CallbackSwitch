<?php

namespace Rikta\CallbackSwitch\Test\Functional\Switches;

use DomainException;
use Generator;
use LogicException;
use Rikta\CallbackSwitch\Exceptions\CaseNotFoundException;
use Rikta\CallbackSwitch\Switches\SwitchByIsA;
use PHPUnit\Framework\TestCase;
use Rikta\CallbackSwitch\Switches\SwitchBySubstring;
use Rikta\CallbackSwitch\Switches\SwitchByValue;
use RuntimeException;

class SwitchBySubstringTest extends TestCase
{

    /**
     * @param array<string, callable> $cases
     * @param string $expected expected result
     * @param string $value value to pass
     * @dataProvider correctCaseDataProvider
     */
    public static function testCorrectCaseIsChosen(array $cases, string $expected, string $value): void
    {
        $switch = new SwitchBySubstring($cases);

        $result = $switch($value);

        self::assertEquals($expected, $result);
    }

    /**
     * @return Generator<mixed>
     */
    public function correctCaseDataProvider(): Generator
    {
        yield 'search unambiguous' => [
            $this->createCases(SwitchBySubstring::DEFAULT, 'hello', 'bye'),
            'result of hello',
            'hello world!'
        ];

        yield 'default first' => [
            $this->createCases('default', 'hello', 'bye', SwitchBySubstring::DEFAULT),
            'result of ___DEFAULT___',
            'something'
        ];

        yield 'default last' => [
            $this->createCases('hello', 'bye', 'world', SwitchBySubstring::DEFAULT),
            'result of ___DEFAULT___',
            'something'
        ];

        yield 'returns first match' => [
            $this->createCases(SwitchBySubstring::DEFAULT, 'hello', 'world'),
            'result of hello',
            'hello world!'
        ];


        yield 'matches literal string default without using the default' => [
            $this->createCases(SwitchBySubstring::DEFAULT, 'default'),
            'result of default',
            'some default message'
        ];
    }

    /**
     * @param string ...$cases
     * @return array<string, callable>
     */
    private function createCases(string ...$cases): array
    {
        $return = [];

        foreach ($cases as $case) {
            $return[$case] = static fn () => 'result of ' . $case;
        }

        return $return;
    }
}
