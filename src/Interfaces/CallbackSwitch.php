<?php

namespace Rikta\CallbackSwitch\Interfaces;

use Rikta\CallbackSwitch\Exceptions\CaseNotFoundException;

interface CallbackSwitch
{
    /**
     * Key of the default case
     * @const string DEFAULT
     */
    public const DEFAULT = '___DEFAULT___'; // wrapped with 3 underscores = less chance to collide with "real" keys

    /**
     * @param array<string, callable> $cases $key => fn($value, ...$extra):$return
     * @param bool $optional if true, a missing case will throw no exception
     */
    public function __construct(array $cases = [], bool $optional = false);

    /**
     * Invokes the switch with the passed values.
     * Depending on the $optional-field this may throw an exception if the case is missing
     *
     * @param mixed $value value that is used to select the case, also passed as first param to the case
     * @param mixed ...$extra additional values to pass to the case
     * @return mixed the value returned by the case
     * @throws CaseNotFoundException
     */
    public function __invoke($value, ...$extra);

    /**
     * Creates a throwaway-instance of the implementing subclass and pass the $value and ...$extra to it
     * Throws an CaseNotFoundException if no case is found and no default provided
     *
     * @param mixed $value value that is used to select the case, also passed as first param to the case
     * @param array<string, callable> $cases AssocArray of callables, first passed param: $value, rest: ...$extra
     * @param mixed ...$extra additional values to pass to the case
     * @return mixed|null the value returned by the case
     * @throws CaseNotFoundException
     */
    public static function switch($value, array $cases, ...$extra);

    /**
     * Creates a throwaway-instance of the implementing subclass and pass the $value and ...$extra to it
     * Returns null if no case is found and no default provided
     *
     * @param mixed $value value that is used to select the case, also passed as first param to the case
     * @param array<string, callable> $cases AssocArray of callables
     * @param mixed ...$extra additional values to pass to the case
     * @return mixed|null the value returned by the case, or null if no case was found
     */
    public static function switchOptional($value, array $cases, ...$extra);

    /**
     * Adds a case on a given key to this instance
     * @param string $key key of the case to add (not the associated value!)
     * @param callable $case case to add fn($value, ...$extra):$return
     * @return $this
     */
    public function addCase(string $key, callable $case): self;

    /**
     * Checks the existence of a case with a specific key
     * @param string $key key of the case to check (not the associated value!)
     * @return bool
     */
    public function hasCase(string $key): bool;

    /**
     * Returns the case on a given key - or null
     * @param string $key key of the case to get (not the associated value!)
     * @return callable|null
     */
    public function getCase(string $key): ?callable;

    /**
     * Returns all currently registered cases
     * @return array<string, callable>
     */
    public function getCases(): array;

    /**
     * Adds a default-case to this instance
     * @param callable $case case to add fn($value, ...$extra):$return
     * @return $this
     */
    public function addDefault(callable $case): self;

    /**
     * Checks the existence of a default-case
     * @return bool
     */
    public function hasDefault(): bool;

    /**
     * Returns the default-case on a given key - or null
     * @return callable|null
     */
    public function getDefault(): ?callable;

    /**
     * Marks this instance as optional.
     * Instead of throwing an exception invoking the switch will return null in case of a missing case.
     * @param bool $optional shall null be returned instead of an exception? true by default
     * @return $this
     */
    public function setOptional($optional = true): self;

    /**
     * Check if this instance is marked as optional.
     * Instead of throwing an exception invoking the switch will return null in case of a missing case.
     * @return bool
     */
    public function isOptional(): bool;
}
