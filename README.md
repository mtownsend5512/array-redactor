A PHP package to redact an array's values by their keys no matter how deep the array.


<p align="center">
<img src="https://i.imgur.com/zkRNi2A.jpg">
</p>

## Why?

Have you ever built or interacted with an api and needed to log all outgoing and incoming calls? Chances are that somewhere in that process is an authentication, either by an app or on behalf of a user. Logs are useful for debugging, but storing sensitive information such as passwords or api keys is not something you want to have in your logs for anyone to see. The usage goes beyond just this example, but that is what prompted me to create the ArrayRedactor package.

Whatever your usage needs may be, this package aims to provide a dead-simple, lightweight way to censor sensitive information in an array no matter how deeply it is nested.

## Installation

Install via composer:

```
composer require mtownsend/array-redactor
```

*This package is designed to work with any PHP 5.6+ application but has special Facade support for Laravel.*

### Registering the service provider (Laravel users)

For Laravel 5.4 and lower, add the following line to your ``config/app.php``:

```php
/*
 * Package Service Providers...
 */
Mtownsend\ArrayRedactor\Providers\ArrayRedactorServiceProvider::class,
```

For Laravel 5.5 and greater, the package will auto register the provider for you.

### Using Lumen

To register the service provider, add the following line to ``app/bootstrap/app.php``:

```php
$app->register(Mtownsend\ArrayRedactor\Providers\ArrayRedactorServiceProvider::class,);
```

### Publishing the config file (Laravel users)

````
php artisan vendor:publish --provider="Mtownsend\ArrayRedactor\Providers\ArrayRedactorServiceProvider"
````

Once your ``arrayredactor.php`` has been published to your config folder, you will see a file with 2 keys in it: ``keys`` and ``ink``. You can replace these values with anything you want, but please note: **these values will only be applied when using the Laravel Facade**.

## Quick start

### Using the class

```php
use Mtownsend\ArrayRedactor\ArrayRedactor;

// An example array, maybe a request being made to/from an API application you wish to log in your database
$login = [
    'email' => 'john_doe@domain.com',
    'password' => 'secret123',
    'data' => [
        'session_id' => 'z481jf0an4kasnc8a84aj831'
    ],
];

$redactor = (new ArrayRedactor($login, ['password', 'session_id']))->redact();

// $redactor will return:
[
    'email' => 'john_doe@domain.com',
    'password' => '[REDACTED]',
    'data' => [
        'session_id' => '[REDACTED]'
    ],
];
```

### Advanced usage

Array Redactor can also receive valid json instead of an array of content.

```php
$json = json_encode([
    'email' => 'john_doe@domain.com',
    'password' => 'secret123',
    'data' => [
        'session_id' => 'z481jf0an4kasnc8a84aj831'
    ],
]);

$redactor = (new ArrayRedactor($json, ['password', 'session_id']))->redact();

// $redactor will return:
[
    'email' => 'john_doe@domain.com',
    'password' => '[REDACTED]',
    'data' => [
        'session_id' => '[REDACTED]'
    ],
];
```

You can also receive your content back as json instead of an array.

```php
$login = [
    'email' => 'john_doe@domain.com',
    'password' => 'secret123',
    'data' => [
        'session_id' => 'z481jf0an4kasnc8a84aj831'
    ],
];

$redactor = (new ArrayRedactor($login, ['password', 'session_id']))->redactToJson();

// $redactor will return:
"{
	"email": "john_doe@domain.com",
	"password": "[REDACTED]",
	"data": {
		"session_id": "[REDACTED]"
	}
}"
```

You can change the redaction value (default: [REDACTED]), known as the ``ink``, by passing it as the third argument of the constructor, or using the dedicated ``->ink()`` method.

```php
$login = [
    'email' => 'john_doe@domain.com',
    'password' => 'secret123',
    'data' => [
        'session_id' => 'z481jf0an4kasnc8a84aj831'
    ],
];

$redactor = (new ArrayRedactor($login, ['password', 'session_id'], null))->redact();
// or...
$redactor = (new ArrayRedactor($login, ['password', 'session_id']))->ink(null)->redact();

// $redactor will return:
[
    'email' => 'john_doe@domain.com',
    'password' => null,
    'data' => [
        'session_id' => null
    ],
];
```

You can call the ``ArrayRedactor`` as a function and the magic ``__invoke()`` method will call the ``redact`` method for you.

```php
$login = [
    'email' => 'john_doe@domain.com',
    'password' => 'secret123',
    'data' => [
        'session_id' => 'z481jf0an4kasnc8a84aj831'
    ],
];

$redactor = (new ArrayRedactor($login, ['password', 'session_id'], null))();

// $redactor will return:
[
    'email' => 'john_doe@domain.com',
    'password' => null,
    'data' => [
        'session_id' => null
    ],
];
```

Lastly, you can skip the constructor arguments entirely if you prefer.

```php
$login = [
    'email' => 'john_doe@domain.com',
    'password' => 'secret123',
    'data' => [
        'session_id' => 'z481jf0an4kasnc8a84aj831'
    ],
];

$redactor = (new ArrayRedactor)->content($login)->keys(['password'])->ink(null)->redact();

// $redactor will return:
[
    'email' => 'john_doe@domain.com',
    'password' => null,
    'data' => [
        'session_id' => 'z481jf0an4kasnc8a84aj831'
    ],
];
```

### Using the global helper

This package provides a convenient helper function which is globally accessible.

```php
array_redactor($array, $keys, $ink)->redact();
// or...
array_redactor()->content($array)->keys(['current_password', 'new_password'])->ink('████████')->redact();
```

### Using the facade (Laravel users)

If you are using Laravel, this package provides a facade. To register the facade add the following line to your ``config/app.php`` under the ``aliases`` key.

**Please note:** this is the only method for Laravel users that will prefill your ``keys`` and ``ink`` from your ``arrayredactor.php`` config file. The global helper and direct instantiation of the class will not prefill these values for you.

````php
'ArrayRedactor' => Mtownsend\ArrayRedactor\Facades\ArrayRedactor::class,
````

```php
use ArrayRedactor;

// Laravel prefills our keys() and ink() methods for us from the config file
ArrayRedactor::content($array)->redact();
```

## Error handling

In the event you pass content that is not valid json or an array, an ``ArrayRedactorException`` will be thrown.

```php
try {
    $redactor = (new ArrayRedactor('i am an invalid argument', ['password']))->redact();
} catch (\Mtownsend\ArrayRedactor\Exceptions\ArrayRedactorException $exception) {
    // do something...
}
```

## Credits

- Mark Townsend
- [All Contributors](../../contributors)

## Testing

You can run the tests with:

```bash
./vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
