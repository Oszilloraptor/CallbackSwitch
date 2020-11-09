<?php

/**
 * @noinspection PhpUnused
 * @noinspection PhpUnhandledExceptionInspection
 */

namespace Rikta\CallbackSwitch\Test\Functional\Abstractions;

use Generator;
use PHPUnit\Framework\TestCase;
use Rikta\CallbackSwitch\Exceptions\CaseNotFoundException;
use Rikta\CallbackSwitch\Test\Dummies\DummySwitch;

class AbstractCallbackSwitchTest extends TestCase
{
    /**
     * @dataProvider regularDataProvider
     * @param int|string|null $value
     * @param array<string, callable> $callables
     * @param mixed $extra
     * @param int|string $expected
     * @throws CaseNotFoundException
     */
    public function testSwitch($value, array $callables, $extra, $expected): void
    {
        $result = DummySwitch::switch($value, $callables, ...$extra);

        self::assertEquals($expected, $result);
    }

    /**
     * @dataProvider regularDataProvider
     * @param int|string|null $value
     * @param array<string, callable> $callables
     * @param mixed $extra
     * @param int|string $expected
     */
    public function testSwitchOptional($value, array $callables, $extra, $expected): void
    {
        $result = DummySwitch::switchOptional($value, $callables, ...$extra);

        self::assertEquals($expected, $result);
    }

    /**
     * @dataProvider regularDataProvider
     * @param int|string|null $value
     * @param array<string, callable> $callables
     * @param mixed $extra
     * @param int|string $expected
     * @throws CaseNotFoundException
     */
    public function testInvoke($value, array $callables, $extra, $expected): void
    {
        $switch = new DummySwitch($callables);
        $result = $switch($value, ...$extra);

        self::assertEquals($expected, $result);
    }

    /**
     * @testdox ::switch(...) throws CaseNotFoundException if no callback found and optional=false
     * @throws CaseNotFoundException
     */
    public function testSwitchThrowsException(): void
    {
        $this->expectException(CaseNotFoundException::class);

        DummySwitch::switch('dummy', []);
    }

    /**
     * @testdox ->_invoke(...) throws CaseNotFoundException if no callback found and optional=false
     * @throws CaseNotFoundException
     */
    public function testInvokeThrowsException(): void
    {
        $this->expectException(CaseNotFoundException::class);

        $switch = new DummySwitch();
        $switch('dummy');
    }

    /**
     * @testdox ::switchOptional(...) returns null if no callback found and optional=true
     */
    public function testSwitchOptionalReturnsNull(): void
    {
        $result = DummySwitch::switchOptional('dummy', []);

        self::assertNull($result);
    }

    /**
     * @testdox ->__invoke(...) returns null if no callback found and optional=true
     */
    public function testInvokeReturnsNull(): void
    {
        $switch = new DummySwitch([], true);
        $result = $switch('dummy');

        self::assertNull($result);
    }

    /**
     * @return Generator<mixed>
     */
    public function regularDataProvider(): Generator
    {
        yield 'correct callback is chosen' => [
            'dummy',
            [
                'dummy' => fn () => 'dummy called'
            ],
            [],
            'dummy called'
        ];

        yield 'default is chosen if nothing matches' => [
            'dummy',
            [
                DummySwitch::DEFAULT => fn () => 'default called'
            ],
            [],
            'default called'
        ];

        yield 'null as value is not throwing any errors' => [
            null,
            [
                DummySwitch::DEFAULT => fn () => 'default called'
            ],
            [],
            'default called'
        ];

        yield 'param1 is passed to callback' => [
            2,
            [
                DummySwitch::DEFAULT => fn ($a) => $a * 2
            ],
            [],
            4
        ];

        yield '...extra is passed to callback' => [
            2,
            [
                DummySwitch::DEFAULT => fn ($a, $b, $c) => $a * $b * $c
            ],
            [3, 4],
            24
        ];
    }
}
