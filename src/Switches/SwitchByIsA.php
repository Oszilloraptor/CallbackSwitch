<?php

namespace RiktaD\CallbackSwitch\Switches;

use RiktaD\CallbackSwitch\Abstractions\AbstractCallbackSwitch;

class SwitchByIsA extends AbstractCallbackSwitch
{
    /**
     * Returns the first case that has the FQCN of $value or one of its parents
     *
     * @param mixed $value
     * @return callable|null
     */
    protected function chooseCase($value): ?callable
    {
        $cases = $this->getCases();

        foreach ($cases as $interface => $case) {
            if (is_a($value, $interface, true)) {
                return $case;
            }
        }

        return null;
    }
}
