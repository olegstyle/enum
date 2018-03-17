# PHP Enumerator

# Requires

PHP >= 7.1

# Installation

#### 1. Append to composer package requires
```
composer require olegstyle/enum
```

#### 2. Use it `^_^`

# Usage

#### 1. Create new class

```php
/**
 * -- Magic methods
 * @method static static EXPIRED()
 * @method static static ERROR()
 * @method static static STARTED()
 * @method static static PENDING()
 * @method static static DONE()
 */
class StatusEnum extends OlegStyle\Enum\Enum
{
    EXPIRED = -2;
    ERROR = -1;
    STARTED = 0;
    PENDING = 1;
    DONE  = 2;
}
```

#### 2. use it like this
```php
$started = StatusEnum::STARTED(); // will return Enum object by magik command
$error = StatusEnum::instanceFromValue(-1); // will be created object from value
$expired = new StatusEnum(-2); // will be created object from value

$isExpired = $started->isEqual(StatusEnum::EXPIRED()); // will be returned false
$isExpired2 = $expired->isEqual(-2); // will be returned true 
$isExpired3 = $started->isEqual('0'); // will be returned false because is using strict mode comparation
$isStartedKey = $started->isEqualKey('STARTED'); // will be returned true
$isStartedKeyEnum = $started->isEqualKey(StatusEnum::STARTED()); // will be returned true

$array = StatusEnum::toArray(); // will be return all constants from StatusEnum class
$pendingKey = StatusEnum::search(1); // will be returned contant name - 'PENDING'
$unknownKey = StatusEnum::search('1'); // will be returned null because is using strict mode
$pendingKey = StatusEnum::getName(1); // similar to search

StatusEnum::isValid(2); // valid key. should be returned true
StatusEnum::isValid('2'); // should be returned false because is using strict mode
```