<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Traits;

use __PHP_Incomplete_Class;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use TypeError;
use function get_class;
use function gettype;
use function implode;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_numeric;
use function is_object;
use function is_string;
use function sprintf;
use function strlen;
use function trim;

/**
 * Convenience trait including simple assertions and error reporting functions
 */
trait ErrorTrait
{
    /**
     * Asserts that $userInput is an instance of one of the provided $classNames (polyfill for php 8.0 Union types)
     *
     * @param string $varName The name of the userinput variable, to be used in the error message.
     * @param string|string[] $classNames The classnames that should be tested against
     * @param mixed $userInput The input that should be tested
     *
     * @throws TypeError
     */
    private static function assertClass(string $varName, $classNames, $userInput): void
    {
        if (!self::isClass($classNames, $userInput)) {
            throw self::typeError($varName, $classNames, $userInput);
        }
    }

    private static function assertClassOrType(string $varName, $classOrTypes, $userInput): void
    {
        if (!self::isClass($classOrTypes, $userInput) && !self::isType($classOrTypes, $userInput)) {
            throw self::typeError($varName, $classOrTypes, $userInput);
        }
    }

    private static function assertType(string $varName, $types, $userInput): void
    {
        if (!self::isType($types, $userInput)) {
            throw self::typeError($varName, $types, $userInput);
        }
    }

    /**
     * Get debug type method stolen from the symfony polyfill
     *
     * @see https://github.com/symfony/polyfill/blob/main/src/Php80/Php80.php
     */
    public static function getDebugType($value): string
    {
        switch (true) {
            case null === $value: return 'null';
            case is_bool($value): return 'bool';
            case is_string($value): return 'string';
            case is_array($value): return 'array';
            case is_int($value): return 'int';
            case is_float($value): return 'float';
            case is_object($value): break;
            case $value instanceof __PHP_Incomplete_Class: return '__PHP_Incomplete_Class';
            default:
                if (null === $type = @get_resource_type($value)) {
                    return 'unknown';
                }

                if ('Unknown' === $type) {
                    $type = 'closed';
                }

                return "resource ($type)";
        }

        $class = get_class($value);

        if (false === strpos($class, '@')) {
            return $class;
        }

        return (get_parent_class($class) ?: key(class_implements($class)) ?: 'class').'@anonymous';
    }

    /**
     * @param $classNames
     * @param $userInput
     * @return bool
     */
    private static function isClass($classNames, $userInput): bool
    {
        if (!is_array($classNames)) {
            $classNames = [$classNames];
        }

        foreach ($classNames as $class) {
            if (is_a($userInput, $class)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $types
     * @param $userInput
     * @return bool
     */
    private static function isType($types, $userInput): bool
    {
        if (!is_array($types)) {
            $types = [$types];
        }

        $actualType = gettype($userInput);
        foreach ($types as $type) {
            if ($actualType === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validates the name to see if it can be used as a parameter or variable.
     *
     * @see https://neo4j.com/docs/cypher-manual/current/syntax/naming/#_naming_rules
     *
     * @param string $name
     *
     * @return void
     */
    private static function assertValidName(string $name): void
    {
        $name = trim($name);

        if ($name === "") {
            throw new InvalidArgumentException("A name cannot be an empty string");
        }

        if (!ctype_alnum(str_replace('_', '', $name))) {
            throw new InvalidArgumentException('A name can only contain alphanumeric characters and underscores');
        }

        if (is_numeric($name[0])) {
            throw new InvalidArgumentException('A name cannot begin with a numeric character');
        }

        if (strlen($name) >= 65535) {
            throw new InvalidArgumentException('A name cannot be longer than 65534 characters');
        }
    }

    /**
     * @param string $varName
     * @param $classNames
     * @param $userInput
     * @return TypeError
     */
    private static function typeError(string $varName, $classNames, $userInput): TypeError
    {
        return new TypeError(self::getTypeErrorText($varName, $classNames, $userInput));
    }

    /**
     * @param string $varName
     * @param $classNames
     * @param $userInput
     * @return string
     */
    private static function getTypeErrorText(string $varName, $classNames, $userInput): string
    {
        return sprintf(
            '$%s should be a %s object, %s given.',
            $varName,
            implode(' or ', $classNames),
            self::getDebugType($userInput)
        );
    }
}
