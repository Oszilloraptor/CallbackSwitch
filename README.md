# CallbackSwitch

![b-packagist-version] ![b-php-version] ![b-license]

![b-last-release-date] ![b-last-commit-date] ![b-commits-since-last-release]

Advanced switch statements as classes

## Description

This package provides classes that wrap similar logic to a switch statement
into object-instances.

You provide several callbacks together with their key and subsequently
call the instance like a method. (there is also a static one-method-call)

This enables you to abstract away commonly used switching-logic,
dynamically construct the cases or maybe even inject some conditional as dependency 
e.g.  in DI or Strategy-Patterns

Along the Abstraction this package ships:

- `SwitchByIsA` -> Keys are Interfaces (FQCN), prioritized by order of added callbacks (first wins)
- `SwitchByType` -> Keys are inbuilt types or FQCNs, FQCNs have higher priority
- `SwitchByValue` -> Keys are the values

You can also easily implementing your own CallbackSwitches by extending the `AbstractCallbackSwitch`
and implementing the only abstract method to choose which callback to use 

### 

## Installation

Require this package with `composer require riktad/callback-switch`

## Usage

### Cases

Regardless of your concrete call you provide your cases as `callables` with an associated key.

The `callable` will get the `$value` as first parameter, and `...$extra` for the rest of it.

What you have to choose for the key depends on the type of switch you are using.
e.g. if you use `SwitchByValue` it must match the exact `(string)$value`,
if you use `SwitchByType` it must match the type or class of `$value`

### with Instance

#### Create

You can create a class implementing `CallbackSwitch` with an optional `['key' => fn($value, ...$extra)]` array.

```php
$exampleSwitch = new SwitchByType([
    SwitchByType::string => staticfn($value) => ('Hello ' . $value),
    SwitchByType::int => staticfn($value) => ('Hello Agent '. $value),
])
```

#### Add Cases

You can add cases to a CallbackSwitchInstance with `addCase($key, $callable)`

```php
$exampleSwitch->addCase(SwitchByType::array, fn($value) => ('Hello ' . implode(' and ', $value)))
```

#### Make optional

By default, a `CallbackSwitch` shall throw an exception if neither a matching case was found, nor a default was provided.

If you mark the instance as "optional" `null` will be returned instead.

There are two ways to mark an instance as optional:

- You can pass `true` as second argument to the constructor

```php
$exampleSwitch = new SwitchByType([...], true)
```

- You can call `setOptional()` on the instance

```php
$exampleSwitch->setOptional()
```

#### Invoke

Every class implementing `SwitchCallback` is invokable, as if it would be a method.

Pass your `$value` as first parameter, it will be used to determine the case and passed to the callback.
Any extraneous parameters you pass here will be passed to the callback as well. 

```
echo $exampleSwitch(['Anna', 'Bob']);
```

#### Full example

```php
$exampleSwitch = new SwitchByType([
    SwitchByType::string => staticfn($value) => ('Hello ' . $value),
    SwitchByType::int => staticfn($value) => ('Hello Agent '. $value),
]);

$exampleSwitch
    ->addCase(SwitchByType::array, fn($value) => ('Hello ' . implode(' and ', $value)))
    ->setOptional();

echo $exampleSwitch('World'); // Hello World
echo $exampleSwitch(['Jay', 'Bob']); // Hello Jay and Bob
echo $exampleSwitch(47); // Hello Agent 47
```

### without Instance

If you need the CallbackSwitch only once, you can call the static version.

```php
$value = 'World';
echo SwitchByType::switch($value, [
    SwitchByType::string => staticfn($value) => ('Hello ' . $value),
    SwitchByType::int => staticfn($value) => ('Hello Agent '. $value),
    SwitchByType::array => staticfn($value) => ('Hello ' . implode(' and ', $value)))
]) // Hello World;
```

#### Make optional

By default, a `CallbackSwitch` shall throw an exception if neither a matching case was found, nor a default was provided.

If you call `::switchOptional` instead of `::switch`, `null` will be returned instead.

## Contributing

Contributions are always welcome.

Should you want to contribute I suggest you take 
a look at [CONTRIBUTING.md](.github/CONTRIBUTING.md) first.

## License

[MIT](./LICENSE)

[b-packagist-version]:[https://img.shields.io/packagist/v/riktad/callback-switch]
[b-php-version]:[https://img.shields.io/packagist/php-v/riktad/callback-switch]
[b-license]:[https://img.shields.io/github/license/RiktaD/CallbackSwitch]
[b-last-release-date]:[https://img.shields.io/github/release-date/RiktaD/CallbackSwitch]
[b-last-commit-date]:[https://img.shields.io/github/last-commit/RiktaD/CallbackSwitch]
[b-commits-since-last-release]:[https://img.shields.io/github/commits-since/RiktaD/CallbackSwitch/latest]
