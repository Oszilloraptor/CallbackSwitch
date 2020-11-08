<?php

namespace RiktaD\CallbackSwitch\Switches;

use RiktaD\CallbackSwitch\Abstractions\AbstractCallbackSwitch;

class SwitchByType extends AbstractCallbackSwitch
{
    public const BOOL = 'boolean';
    public const INT = 'integer';
    public const DOUBLE = 'double';
    public const FLOAT = self::DOUBLE; // for historical reasons "double" is returned by gettype in case of a float
    public const STRING = 'string';
    public const ARRAY = 'array';
    public const OBJECT = 'object';
    public const RESOURCE = 'resource';
    public const CLOSED_RESOURCE = 'resource (closed)';
    public const NULL = 'NULL';

    /**
     * Returns the case for a specific type (returned by gettype) or FQCN
     * Should $value be an object it will check first for the FQCN-case, then for the object-case
     *
     * @param mixed $value
     * @return callable|null
     */
    protected function chooseCase($value): ?callable
    {
        $gettype = gettype($value);
        if ($gettype === self::OBJECT && $this->hasCase($class = get_class($value))) {
            return $this->getCase($class);
        }

        if ($this->hasCase($gettype)) {
            return $this->getCase($gettype);
        }

        return null;
    }
}
