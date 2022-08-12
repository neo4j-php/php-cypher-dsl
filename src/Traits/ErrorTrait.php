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
use TypeError;

/**
 * Convenience trait including simple assertions and error reporting functions.
 *
 * @internal This trait is not covered by the backwards compatibility guarantee of php-cypher-dsl
 */
trait ErrorTrait
{
    /**
     * Asserts that all values of $userInput are an instance of one of the provided $classNames.
     *
     * @param string $varName THe name of the user input variable, to be used in the error message
     * @param string|string[] $classNames The classnames that should be tested against
     * @param array $userInput The input array that should be tested
     *
     * @throws TypeError
     */
    private static function assertClassArray(string $varName, $classNames, array $userInput): void
    {
        foreach ($userInput as $value) {
            self::assertClass($varName, $classNames, $value);
        }
    }

    /**
     * Asserts that $userInput is an instance of one of the provided $classNames (polyfill for php 8.0 Union types).
     *
     * @param string $varName The name of the user input variable, to be used in the error message
     * @param string|string[] $classNames The classnames that should be tested against
     * @param mixed $userInput The input that should be tested
     *
     * @throws TypeError
     */
    private static function assertClass(string $varName, $classNames, $userInput): void
    {
        if (!is_array($classNames)) {
            $classNames = [$classNames];
        }

        if (!self::isClass($classNames, $userInput)) {
            throw self::typeError($varName, $classNames, $userInput);
        }
    }

    /**
     * Get debug type method stolen and refactored from the symfony polyfill
     *
     * @see https://github.com/symfony/polyfill/blob/main/src/Php80/Php80.php
     */
    private static function getDebugType($value): string
    {
        return self::detectScalar($value)
            ?? self::detectClass($value)
            ?? self::detectResource($value)
            ?? 'unknown';
    }

    /**
     * @param string[] $classNames
     * @param mixed $userInput
     * @return bool
     */
    private static function isClass(array $classNames, $userInput): bool
    {
        foreach ($classNames as $class) {
            if (is_a($userInput, $class) || $class === self::getDebugType($userInput)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $varName
     * @param string[] $classNames
     * @param mixed $userInput
     * @return TypeError
     */
    private static function typeError(string $varName, array $classNames, $userInput): TypeError
    {
        $errorText = sprintf(
            '$%s should be a %s, %s given.',
            $varName,
            implode(' or ', $classNames),
            self::getDebugType($userInput)
        );

        return new TypeError($errorText);
    }

    /**
     * Returns the name of the scalar type of the value if it is one.
     *
     * @param mixed $value
     * @return string|null
     */
    private static function detectScalar($value): ?string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return 'bool';
        }

        if (is_string($value)) {
            return 'string';
        }

        if (is_array($value)) {
            return 'array';
        }

        if (is_int($value)) {
            return 'int';
        }

        if (is_float($value)) {
            return 'float';
        }

        return null;
    }

    /**
     * Returns the name of the class of the value if it is one.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    private static function detectClass($value): ?string
    {
        if ($value instanceof __PHP_Incomplete_Class) {
            return '__PHP_Incomplete_Class';
        }

        if (is_object($value)) {
            $class = get_class($value);

            if (false === strpos($class, '@')) {
                return $class;
            }

            return (get_parent_class($class) ?: key(class_implements($class)) ?: 'class').'@anonymous';
        }

        return null;
    }

    /**
     * Returns the name of the resource of the value if it is one.
     *
     * @param mixed $value
     *
     * @return string|null
     */
    private static function detectResource($value): ?string
    {
        if (is_resource($value)) {
            $type = @get_resource_type($value);
            if (null === $type) {
                return 'unknown';
            }

            if ('Unknown' === $type) {
                $type = 'closed';
            }

            return "resource ($type)";
        }

        return null;
    }
}
