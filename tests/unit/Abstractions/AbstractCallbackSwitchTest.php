<?php

/** @noinspection PhpUnhandledExceptionInspection */

namespace RiktaD\CallbackSwitch\Test\Unit\Abstractions;

use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RiktaD\CallbackSwitch\Abstractions\AbstractCallbackSwitch;
use RiktaD\CallbackSwitch\Exceptions\CaseNotFoundException;
use RiktaD\CallbackSwitch\Test\Dummies\DummySwitch;

/**
 * @coversDefaultClass \RiktaD\CallbackSwitch\Abstractions\AbstractCallbackSwitch
 */
class AbstractCallbackSwitchTest extends TestCase
{
    /**
     * @var array<string, callable>
     */
    private array $dummyCases;

    private ReflectionProperty $optional;

    private ReflectionProperty $cases;

    public function setUp(): void
    {
        $this->dummyCases = [
            'int_sum' => static fn(int ...$ints) => array_sum($ints),
            'planet' => static fn($planet) => $planet,
            'moon' => static fn() => 'Moon!',
            'echo' => static fn($a) => $a,
        ];

        $this->optional = new ReflectionProperty(AbstractCallbackSwitch::class, 'optional');
        $this->optional->setAccessible(true);

        $this->cases = new ReflectionProperty(AbstractCallbackSwitch::class, 'cases');
        $this->cases->setAccessible(true);
    }

    /**
     * @testdox ::__constructor() is callable without parameters
     */
    public function testConstructorIsCallableWithoutParameters(): void
    {
        new DummySwitch(); // would throw exception otherwise
        self::assertTrue(true);
    }

    /**
     * @testdox ::__constructor($cases) sets the cases
     */
    public function test1stConstructorParamSetsCallables(): void
    {
        $switch = new DummySwitch($this->dummyCases);
        $cases = $this->cases->getValue($switch);

        self::assertEquals($this->dummyCases, $cases);
    }

    /**
     * @testdox ::constructor($cases, $optional=true) sets $this->optional to true if $optional=true
     */
    public function test2ndConstructorParamSetsOptionalToFalseIfFalse(): void
    {
        $switch = new DummySwitch($this->dummyCases, false);
        $optional = $this->optional->getValue($switch);

        self::assertFalse($optional);
    }

    /**
     * @testdox ::constructor($cases, $optional=false) sets $this->optional to false if $optional=false
     */
    public function test2ndConstructorParamSetsOptionalToTrueIfTrue(): void
    {
        $switch = new DummySwitch($this->dummyCases, true);
        $optional = $this->optional->getValue($switch);

        self::assertTrue($optional);
    }

    /**
     * @testdox ::__constructor() without parameters initializes with empty cases-array
     */
    public function testCasesAreEmptyByDefault(): void
    {
        $switch = new DummySwitch();

        self::assertCount(0, $this->cases->getValue($switch));
    }

    /**
     * @testdox ::__constructor() without parameters initializes with optional=false
     */
    public function testOptionalIsFalseByDefault(): void
    {
        $switch = new DummySwitch();

        self::assertFalse($this->optional->getValue($switch));
    }

    /**
     * @testdox ->__invoke($value) invokes the case chosen by `$this->chooseCase`
     */
    public function testInvokesInvokesChosenCaseIfPresent(): void
    {
        $switch = $this->createSwitchMock();
        $value = 'dummy';
        $called = false;

        $switch->expects('chooseCase')
            ->with($value)
            ->andReturns(static function () use (&$called) {
                $called = true;
            });

        $switch($value);

        self::assertTrue($called);
    }

    /**
     * @testdox ->__invoke($value) invokes the case chosen by `$this->chooseCase` with $value as first param
     */
    public function testInvokesInvokesChosenCaseWithValueAs1stParam(): void
    {
        $switch = $this->createSwitchMock();
        $value = 'dummy';

        $switch->expects('chooseCase')
            ->with($value)
            ->andReturns(static function ($a) use ($value) {
                self::assertEquals($value, $a);
            });

        $switch($value);
    }

    /**
     * @testdox ->__invoke($value) invokes the case chosen by `$this->chooseCase` with ...$extra as 2nd+ params
     */
    public function testInvokesInvokesChosenCaseWithExtraParams(): void
    {
        $switch = $this->createSwitchMock();
        $valueA = 'dummy';
        $valueB = 'yummy';
        $valueC = 'tummy';

        $switch->expects('chooseCase')
            ->with($valueA)
            ->andReturns(static function ($a, $b, $c) use ($valueB, $valueC) {
                self::assertEquals($valueB, $b);
                self::assertEquals($valueC, $c);
            });

        $switch($valueA, $valueB, $valueC);
    }

    /**
     * @testdox ->__invoke($value) invokes the default if `$this->chooseCase` returns null
     */
    public function testInvokesInvokesDefaultIfCallbackNotPresent(): void
    {
        $switch = $this->createSwitchMock();
        $value = 'dummy';
        $called = false;

        $switch->expects('chooseCase')
            ->with($value)
            ->andReturns(null);

        $switch->expects('getDefault')
            ->andReturns(static function () use (&$called) {
                $called = true;
            });

        $switch($value);

        self::assertTrue($called);
    }

    /**
     * @testdox ->__invoke($value) throws exception if neither callback nor default are provided and optional=false
     * @throws CaseNotFoundException
     */
    public function testInvokesThrowsExceptionIfNotOptional(): void
    {
        $this->expectException(CaseNotFoundException::class);

        $switch = $this->createSwitchMock();
        $value = 'dummy';

        $switch->expects('chooseCase')
            ->with($value)
            ->andReturns(null);

        $switch->expects('getDefault')
            ->andReturns(null);

        $switch->expects('isOptional')
            ->andReturns(false);

        $switch->expects('getCases')
            ->andReturns([]);

        $switch($value);
    }

    /**
     * @testdox ->__invoke($value) returns null if neither callback nor default are provided and optional=true
     * @throws CaseNotFoundException
     */
    public function testInvokesReturnsNullIfOptional(): void
    {
        $switch = $this->createSwitchMock();
        $value = 'dummy';

        $switch->expects('chooseCase')
            ->with($value)
            ->andReturns(null);

        $switch->expects('getDefault')
            ->andReturns(null);

        $switch->expects('isOptional')
            ->andReturns(true);

        $result = $switch($value);

        self::assertNull($result);
    }

    /**
     * @testdox ->addCase($key, $callback) adds the case
     */
    public function testAddCase(): void
    {
        $switch = new DummySwitch();

        foreach ($this->dummyCases as $case => $callable) {
            $switch->addCase($case, $callable);
        }

        $cases = $this->cases->getValue($switch);

        self::assertEquals($this->dummyCases, $cases);
    }

    /**
     * @testdox ->hasCase($key) returns true if the case is present
     */
    public function testHasCaseReturnsTrue(): void
    {
        $switch = new DummySwitch($this->dummyCases);

        $result = $switch->hasCase('moon');

        self::assertTrue($result);
    }

    /**
     * @testdox ->hasCase($key) returns false if the case is not present
     */
    public function testHasCaseReturnsFalse(): void
    {
        $switch = new DummySwitch($this->dummyCases);

        $result = $switch->hasCase('saturn');

        self::assertFalse($result);
    }

    /**
     * @testdox ->getCase($key) returns the case, if it is present
     */
    public function testGetCaseReturnsCase(): void
    {
        $switch = $this->createSwitchMock();
        $value = 'moon';

        $switch->expects('getCases')
            ->andReturns($this->dummyCases);

        $result = $switch->getCase($value);

        self::assertEquals($this->dummyCases[$value], $result);
    }

    /**
     * @testdox ->getCase($key) returns null, if the case is not present
     */
    public function testGetCaseReturnsNull(): void
    {
        $switch = $this->createSwitchMock();
        $value = 'dummy';

        $switch->expects('getCases')
            ->andReturns([]);

        $result = $switch->getCase($value);

        self::assertNull($result);
    }

    /**
     * @testdox ->getCases() returns all currently stored cases
     */
    public function testGetCases(): void
    {
        $switch = new DummySwitch($this->dummyCases);

        $this->cases->setValue($switch, $this->dummyCases);

        $result = $switch->getCases();

        self::assertEquals($this->dummyCases, $result);
    }

    /**
     * @testdox ->addDefault($callable) adds $callable as default case
     */
    public function testAddDefault(): void
    {
        $switch = new DummySwitch();
        $default = $this->dummyCases['moon'];

        $switch->addDefault($default);

        $result = $this->cases->getValue($switch)[DummySwitch::DEFAULT];

        self::assertEquals($default, $result);
    }

    /**
     * @testdox ->hasDefault() returns true if a default case exists
     */
    public function testHasDefaultReturnsTrue(): void
    {
        $switch = new DummySwitch();

        $this->cases->setValue($switch, [
            DummySwitch::DEFAULT => $this->dummyCases['moon']
        ]);

        $result = $switch->hasDefault();

        self::assertTrue($result);
    }

    /**
     * @testdox ->hasDefault() returns true if no default case exists
     */
    public function testHasDefaultReturnsFalse(): void
    {
        $switch = new DummySwitch();

        $result = $switch->hasDefault();

        self::assertFalse($result);
    }

    /**
     * @testdox ->getDefault() returns the default case, if one is present
     */
    public function testGetDefaultReturnsDefault(): void
    {
        $switch = new DummySwitch();

        $default = $this->dummyCases['moon'];
        $this->cases->setValue($switch, [
            DummySwitch::DEFAULT => $default
        ]);

        $result = $switch->getDefault();

        self::assertEquals($result, $default);
    }

    /**
     * @testdox ->getDefault() returns null, if no default case is present
     */
    public function testGetDefaultReturnsNull(): void
    {
        $switch = new DummySwitch();

        $default = $this->dummyCases['moon'];

        $result = $switch->getDefault();

        self::assertNull($result);
    }

    /**
     * @testdox ->setOptional() without parameters sets optional to true
     */
    public function testSetOptionalWithoutParameter(): void
    {
        $switch = new DummySwitch();

        $switch->setOptional();

        $result = $this->optional->getValue($switch);

        self::assertTrue($result);
    }

    /**
     * @testdox ->setOptional(true) sets optional to true
     */
    public function testSetOptionalWithTrue(): void
    {
        $switch = new DummySwitch();

        $switch->setOptional(true);

        $result = $this->optional->getValue($switch);

        self::assertTrue($result);
    }

    /**
     * @testdox ->setOptional(false) sets optional to false
     */
    public function testSetOptionalWithFalse(): void
    {
        $switch = new DummySwitch();

        $switch->setOptional(false);

        $result = $this->optional->getValue($switch);

        self::assertFalse($result);
    }

    /**
     * @testdox ->isOptional() returns true if optional is true
     */
    public function testIsOptionalReturnsTrue(): void
    {
        $switch = new DummySwitch();

        $this->optional->setValue($switch, true);

        $result = $switch->isOptional();

        self::assertTrue($result);
    }

    /**
     * @testdox ->isOptional() returns false if optional is false
     */
    public function testIsOptionalReturnsFalse(): void
    {
        $switch = new DummySwitch();

        $this->optional->setValue($switch, false);

        $result = $switch->isOptional();

        self::assertFalse($result);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @return Mockery\MockInterface&AbstractCallbackSwitch
     */
    private function createSwitchMock()
    {
        $switch = Mockery::mock(AbstractCallbackSwitch::class);
        $switch->shouldAllowMockingProtectedMethods();
        $switch->makePartial();
        return $switch;
    }
}
