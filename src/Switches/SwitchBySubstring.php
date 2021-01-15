<?php

namespace Rikta\CallbackSwitch\Switches;

use Rikta\CallbackSwitch\Abstractions\AbstractCallbackSwitch;

class SwitchBySubstring extends AbstractCallbackSwitch
{
    /**
     * Returns the first case that contains the key as substring
     *
     * @param mixed $value
     * @return callable|null
     */
    protected function chooseCase($value): ?callable
    {
        $cases = $this->getCases();

        foreach ($cases as $substring => $case) {
            if (strpos($value, $substring) !== false) {
                return $case;
            }
        }

        return null;
    }
}
