<?php

namespace RiktaD\CallbackSwitch\Exceptions;

use Exception;
use Throwable;

class CaseNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        if ($message === "") {
            $message = sprintf('No matching case found in %s:%d', $this->getFile(), $this->getLine());
        }
        parent::__construct($message, $code, $previous);
    }
}
