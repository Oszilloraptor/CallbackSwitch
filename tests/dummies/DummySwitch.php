<?php

namespace Rikta\CallbackSwitch\Test\Dummies;

use Rikta\CallbackSwitch\Abstractions\AbstractCallbackSwitch;

class DummySwitch extends AbstractCallbackSwitch
{
    /** @noinspection PhpUnused */
    protected function chooseCase($value): ?callable
    {
        return $this->getCase((string)$value);
    }
}
