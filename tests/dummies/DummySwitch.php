<?php

namespace RiktaD\CallbackSwitch\Test\Dummies;

use RiktaD\CallbackSwitch\Abstractions\AbstractCallbackSwitch;

class DummySwitch extends AbstractCallbackSwitch
{
    /** @noinspection PhpUnused */
    protected function chooseCase($value): ?callable
    {
        return $this->getCase((string)$value);
    }
}
