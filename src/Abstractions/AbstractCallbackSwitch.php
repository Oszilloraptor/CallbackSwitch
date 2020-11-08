<?php

namespace RiktaD\CallbackSwitch\Abstractions;

use RiktaD\CallbackSwitch\Exceptions\CaseNotFoundException;
use RiktaD\CallbackSwitch\Interfaces\CallbackSwitch;

abstract class AbstractCallbackSwitch implements CallbackSwitch
{
    /**
     * AssocArray of callables; the content of the keys are depending on the implementing class
     * @var array<string, callable>
     */
    private array $cases;

    /**
     * Normally the AbstractCaseSwitch will throw an exception if no case is found
     * if $optional is true, null will be returned instead
     * @var bool
     */
    private bool $optional;

    /**
     * @inheritDoc
     */
    final public function __construct(array $cases = [], bool $optional = false)
    {
        $this->cases = $cases;
        $this->optional = $optional;
    }

    /**
     * @inheritDoc
     */
    final public function __invoke($value, ...$extra)
    {
        $case = $this->chooseCase($value) ?? $this->getDefault();

        if ($case === null) {
            if ($this->isOptional()) {
                return null;
            }

            $gettype = gettype($value);
            throw new CaseNotFoundException(sprintf(
                'No case found in %s for (%s)%s and no default provided. Valid values are "%s"',
                static::class,
                $gettype,
                $gettype === 'object' ? get_class($value) : $value,
                implode('","', array_keys($this->getCases()))
            ));
        }

        return $case($value, ...$extra);
    }

    /**
     * @inheritDoc
     */
    public static function switch($value, array $cases, ...$extra)
    {
        return (new static($cases, false))($value, ...$extra);
    }

    /**
     * @inheritDoc
     */
    public static function switchOptional($value, array $cases, ...$extra)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new static($cases, true))($value, ...$extra);
    }

    /**
     * @inheritDoc
     */
    public function addCase(string $key, callable $case): self
    {
        $this->cases[$key] = $case;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasCase(string $key): bool
    {
        return array_key_exists($key, $this->cases);
    }

    /**
     * @inheritDoc
     */
    public function getCase(string $key): ?callable
    {
        return $this->getCases()[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getCases(): array
    {
        return $this->cases;
    }

    /**
     * @inheritDoc
     */
    public function addDefault(callable $case): self
    {
        $this->addCase(self::DEFAULT, $case);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasDefault(): bool
    {
        return $this->hasCase(self::DEFAULT);
    }

    /**
     * @inheritDoc
     */
    public function getDefault(): ?callable
    {
        return $this->getCase(self::DEFAULT);
    }

    /**
     * @inheritDoc
     */
    public function setOptional($optional = true): self
    {
        $this->optional = $optional;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isOptional(): bool
    {
        return $this->optional;
    }

    /**
     * Each implementation of this abstractions must provide
     * a method to choose the correct case
     *
     * This method must not handle the default case
     * but return null if no suitable case was found
     *
     * @param mixed $value value that is used to select the case
     * @return callable|null
     */
    abstract protected function chooseCase($value): ?callable;
}
