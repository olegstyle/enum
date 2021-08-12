<?php

namespace OlegStyle\Enum;

use JsonSerializable;
use ReflectionClass;
use UnexpectedValueException;
use BadMethodCallException;

/**
 * Create an enum by implementing this class and adding class constants with magic methods in phpdoc.
 */
abstract class Enum implements JsonSerializable
{
    /**
     * Enum value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Store existing constants in a static cache per object.
     *
     * @var array
     */
    protected static $cache = array();

    /**
     * Creates a new value of some type
     *
     * @param mixed $value
     *
     * @throws UnexpectedValueException if incompatible type is given.
     */
    public function __construct($value)
    {
        $value = $this->prepareValue($value);
        if (!static::isValid($value)) {
            throw new UnexpectedValueException("Value '$value' is not part of the enum " . get_called_class());
        }
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function prepareValue($value)
    {
        return $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * Returns the enum key (i.e. the constant name).
     *
     * @return mixed
     */
    public function getKey()
    {
        return static::search($this->value);
    }

    /**
     * Strict compare constant value
     *
     * @param static|mixed $value
     * @return bool
     */
    public function isEqual($value): bool
    {
        if ($value instanceof static) {
            return $this->getValue() === $value->getValue();
        }

        return $this->value === $value;
    }

    /**
     * Strict compare constant name
     *
     * @param static|mixed $key
     * @return bool
     */
    public function isEqualKey($key): bool
    {
        if ($key instanceof static) {
            return $this->getKey() === $key->getKey();
        }

        return $this->getKey() === $key;
    }

    /**
     * Returns all possible values as an array
     *
     * @return array Constant name in key, constant value in value
     */
    public static function toArray(): array
    {
        $class = get_called_class();
        if (!array_key_exists($class, static::$cache)) {
            $reflection = new ReflectionClass($class);
            static::$cache[$class] = $reflection->getConstants();
        }

        return static::$cache[$class];
    }

    /**
     * @return static[]
     */
    public static function toArrayOfEnums(): array
    {
        $data = static::toArray();
        $result = [];
        foreach ($data as $key => $value) {
            $result[$key] = new static($value);
        }

        return $result;
    }

    /**
     * @param mixed $value
     */
    public static function search($value): ?string
    {
        return array_search($value, static::toArray(), true);
    }

    /**
     * @param mixed $value
     * @return null|string
     */
    public static function getName($value): ?string
    {
        return static::search($value);
    }

    /**
     * Check if is valid enum value
     *
     * @param mixed $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return in_array($value, static::toArray(), true);
    }

    /**
     * Check if is valid enum key
     *
     * @param mixed $key
     * @return bool
     */
    public static function isValidKey($key): bool
    {
        $array = static::toArray();

        return isset($array[$key]);
    }

    /**
     * Returns a value when called statically like: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
     *
     * @param string $name
     * @param array $arguments
     * @return static
     * @throws BadMethodCallException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $array = static::toArray();
        if (isset($array[$name])) {
            return static::instanceFromValue($array[$name]);
        }

        throw new BadMethodCallException("No static method or enum constant '$name' in class " . get_called_class());
    }


    /**
     * @param mixed $value
     * @return Enum
     * @throws UnexpectedValueException
     */
    public static function instanceFromValue($value): self
    {
        return new static($value);
    }

    public function jsonSerialize(): array
    {
        return $this->getValue();
    }
}
