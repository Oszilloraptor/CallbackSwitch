<?php

namespace RiktaD\CallbackSwitch\Switches;

use RiktaD\CallbackSwitch\Abstractions\AbstractCallbackSwitch;

class SwitchByValue extends AbstractCallbackSwitch
{
    /**
     * Returns the case stored on the specific key - or null
     *
     * @param mixed $value
     * @return callable|null
     */
    protected function chooseCase($value): ?callable
    {
        if ($this->hasCase((string)$value)) {
            return $this->getCase((string)$value);
        }

        return null;
    }
}
