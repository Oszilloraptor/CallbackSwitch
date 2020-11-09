<?php

namespace Rikta\CallbackSwitch\Test\Functional\Switches;

use Exception;
use Generator;
use Rikta\CallbackSwitch\Switches\SwitchByType;
use PHPUnit\Framework\TestCase;

class SwitchByTypeTest extends TestCase
{

    /**
     * @param mixed $value value to pass
     * @param string $expected expected result
     * @dataProvider correctCaseDataProvider
     * @testdox SwitchByType choses correct type for $expected "$value"
     */
    public static function testCorrectCaseIsChosen($value, string $expected): void
    {
        $switch = new SwitchByType([
            SwitchByType::BOOL => fn () => 'bool',
            SwitchByType::INT => fn () => 'int',
            SwitchByType::DOUBLE => fn () => 'double', // same as ::FLOAT
            SwitchByType::STRING => fn () => 'string',
            SwitchByType::ARRAY => fn () => 'array',
            SwitchByType::OBJECT => fn () => 'object',
            SwitchByType::RESOURCE => fn () => 'resource',
            SwitchByType::CLOSED_RESOURCE => fn () => 'closed_resource',
            SwitchByType::NULL => fn () => 'null',
            self::class => fn () => 'self'
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
            true,
            'bool',
        ];

        yield [
            42,
            'int',
        ];

        yield [
            2.50,
            'double',
        ];

        yield [
            'lorem ipsum',
            'string',
        ];

        yield [
            [],
            'array',
        ];

        yield [
            new class {
            },
            'object'
        ];

        yield [
            null,
            'null',
        ];

        yield [
            $this,
            'self'
        ];

        if (!$resource = tmpfile()) {
            throw new Exception('Tempfile didnt work');
        }
        yield [
            $resource,
            'resource',
        ];
        fwrite($resource, random_bytes(1)); // or the garbage collector would close it early

        $resource = tmpfile();  // i don't know why, but without reassigning above will be closed to soon ...
        if (!$resource = tmpfile()) {
            throw new Exception('Tempfile didnt work');
        }
        fclose($resource);
        yield [
            $resource,
            'closed_resource',
        ];
    }
}
