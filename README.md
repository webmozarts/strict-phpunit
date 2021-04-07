# Strict PHPUnit

Enables type-safe comparisons of objects in PHPUnit.

## Problem

PHPUnit has a very powerful comparison system that helps you comparing objects
with expected values:

```php
class ValueObject
{
    public ?string $property;
    
    public function __construct(?string $property)
    {
        $this->property = $property;
    }
}

$actual = new ValueObject('foo!');

self::assertEquals(new ValueObject('foo'), $actual);
// => fails with a very helpful error message
```

This comparison system will give you a meaningful exception that guides you
precisely to the problem that caused the assertion to fail. Strings are
furthermore diffed so that you see exactly which character of the string
causes a mismatch.

PHPUnit compares each scalar property of an object with relaxed types. It is a 
little more intelligent than using just `==` under the hood, but still that
will not always provide the results you want:

```php
var_dump('Hi' == true);
// => true

self::assertEquals(new ValueObject('Hi'), new ValueObject(true));
// => fails

var_dump('' == null);
// => true

self::assertEquals(new ValueObject(''), new ValueObject(null));
// => succeeds
```

## Solution

This extension enables a comparator for scalar values that fights this problem.
With this extension, whenever PHPUnit finds a scalar value during 
`assertEquals()` (even recursively within objects or arrays), it will compare
the value with `===`.

Objects are still not checked for identity, hence you can still construct
example objects to compare against.

Error messages stay meaningful.

```php
self::assertEquals(new ValueObject(''), new ValueObject(null));
// => fails with a meaningful error

self::assertEquals(new ValueObject('foo!'), new ValueObject('foo'));
// => fails with a meaningful error

self::assertEquals(new ValueObject('foo!'), new ValueObject('foo!'));
// => succeeds
```

## Installation

The extension can be installed with Composer:

```bash
$ composer require --dev webmozarts/strict-phpunit
```

Add the extension to your `phpunit.xml.dist` file to enable it:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd">
    <!-- ... -->
    
    <extensions>
        <extension class="Webmozarts\StrictPHPUnit\StrictPHPUnitExtension"/>
    </extensions>
    
    <!-- ... -->
</phpunit>
```

## Authors

* [Bernhard Schussek]
* [The Community Contributors]

## Contribute

Contributions to the package are always welcome!

* Report any bugs or issues you find on the [issue tracker].
* You can grab the source code at the package's [Git repository].

Note that this repository is a subtree-split of a monorepo and hence read only.
PRs will be ported to the (internal) monorepo.

License
-------

All contents of this package are licensed under the [MIT license].

[Composer]: https://getcomposer.org
[Bernhard Schussek]: http://webmozarts.com
[The Community Contributors]: https://github.com/webmozarts/strict-phpunit/graphs/contributors
[issue tracker]: https://github.com/webmozarts/strict-phpunit/issues
[Git repository]: https://github.com/webmozarts/strict-phpunit
[MIT license]: LICENSE
