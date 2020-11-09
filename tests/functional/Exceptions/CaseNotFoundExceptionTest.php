<?php

namespace Rikta\CallbackSwitch\Test\Functional\Exceptions;

use Exception;
use PHPUnit\Framework\TestCase;
use Rikta\CallbackSwitch\Exceptions\CaseNotFoundException;

class CaseNotFoundExceptionTest extends TestCase
{

    /**
     * @testdox returns message "No matching case found in {file}:{line}" by default
     */
    public function testDefaultMessage(): void
    {
        $exception = new CaseNotFoundException();
        $result = $exception->getMessage();
        $expected = sprintf(
            'No matching case found in %s:%d',
            __FILE__,
            __LINE__ - 5
        );

        self::assertEquals($expected, $result);
    }

    /**
     * @testdox returns code 0 by default
     */
    public function testDefaultCode(): void
    {
        $exception = new CaseNotFoundException();
        $result = $exception->getCode();
        $expected = 0;

        self::assertEquals($expected, $result);
    }

    /**
     * @testdox returns custom message if provided as 1nd parameter
     */
    public function testPassesMessage(): void
    {
        $message = 'dummy message';
        $exception = new CaseNotFoundException($message);
        $result = $exception->getMessage();

        self::assertEquals($message, $result);
    }

    /**
     * @testdox returns custom error code if provided as 2nd parameter
     */
    public function testPassesCode(): void
    {
        $exception = new CaseNotFoundException("", 1337);
        $result = $exception->getCode();
        $expected = 1337;

        self::assertEquals($expected, $result);
    }

    /**
     * @testdox returns previous exception if provided as 3rd parameter
     */
    public function testPassesPrevious(): void
    {
        $previous = new Exception();
        $exception = new CaseNotFoundException("", 0, $previous);
        $result = $exception->getPrevious();


        self::assertEquals($previous, $result);
    }
}
