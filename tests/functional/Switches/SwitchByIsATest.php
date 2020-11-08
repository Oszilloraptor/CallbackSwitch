<?php

namespace RiktaD\CallbackSwitch\Test\Functional\Switches;

use DomainException;
use Generator;
use LogicException;
use RiktaD\CallbackSwitch\Exceptions\CaseNotFoundException;
use RiktaD\CallbackSwitch\Switches\SwitchByIsA;
use PHPUnit\Framework\TestCase;
use RiktaD\CallbackSwitch\Switches\SwitchByValue;
use RuntimeException;

class SwitchByIsATest extends TestCase
{

    /**
     * @param array<string, callable> $cases
     * @param string $expected expected result
     * @param string $value value to pass
     * @dataProvider correctCaseDataProvider
     */
    public static function testCorrectCaseIsChosen(array $cases, string $expected, string $value): void
    {
        $switch = new SwitchByIsA($cases);

        $result = $switch(new $value());

        self::assertEquals($expected, $result);
    }

    /**
     * @param array<string, callable> $cases
     * @param string $expected expected result
     * @param string $value value to pass
     * @dataProvider correctCaseDataProvider
     */
    public static function testCorrectCaseIsChosenWithClassname(array $cases, string $expected, string $value): void
    {
        $switch = new SwitchByIsA($cases);

        $result = $switch($value);

        self::assertEquals($expected, $result);
    }

    /**
     * @return Generator<mixed>
     */
    public function correctCaseDataProvider(): Generator
    {
        yield 'search unambiguous' => [
            $this->createCases('default', 'logic', 'domain', 'runtime'),
            'result of runtime',
            RuntimeException::class
        ];

        yield 'default first' => [
            $this->createCases('default', 'logic', 'domain', 'runtime'),
            'result of default',
            self::class
        ];

        yield 'default last' => [
            $this->createCases('runtime', 'logic', 'domain', 'default'),
            'result of default',
            self::class
        ];

        yield 'returns parent if parent comes first' => [
            $this->createCases('default', 'logic', 'domain', 'runtime'),
            'result of logic',
            LogicException::class
        ];

        yield 'doesnt return parent if parent comes later' => [
            $this->createCases('default', 'domain', 'logic', 'runtime'),
            'result of domain',
            DomainException::class
        ];
    }

    /**
     * @param string ...$cases
     * @return array<string, callable>
     * @throws CaseNotFoundException
     */
    private function createCases(string ...$cases): array
    {
        $return = [];

        foreach ($cases as $case) {
            $fqcn = SwitchByValue::switch($case, [
                'logic' => static fn () => LogicException::class,
                'domain' => static fn () => DomainException::class, // extends logic
                'runtime' => static fn () => RuntimeException::class, // nothing to do with logic
                'default' => static fn () => SwitchByValue::DEFAULT
            ]);
            $return[(string)$fqcn] = static fn () => 'result of ' . $case;
        }

        return $return;
    }
}
