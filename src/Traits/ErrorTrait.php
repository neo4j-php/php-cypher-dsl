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

use InvalidArgumentException;
use ReflectionClass;
use TypeError;
use function gettype;
use function is_array;
use function is_numeric;
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
    private function assertClass(string $varName, $classNames, $userInput): void
    {
        if (!$this->isClass($classNames, $userInput)) {
            throw new TypeError(
                $this->getTypeErrorText(
                    $varName,
                    $classNames,
                    $userInput
                )
            );
        }
    }

    private function assertClassOrType(string $varName, $classOrTypes, $userInput): void
    {
        if (!$this->isClass($classOrTypes, $userInput) && !$this->isType($classOrTypes, $userInput)) {
            throw new TypeError($this->getTypeErrorText($varName, $classOrTypes, $userInput));
        }
    }

    private function assertType(string $varName, $types, $userInput): void
    {
        if (!$this->isType($types, $userInput)) {
            throw new TypeError(
                $this->getTypeErrorText(
                    $varName,
                    $types,
                    $userInput
                )
            );
        }
    }

    /**
     * Give a nice error message about $userInput not being an object with one of the $classNames types.
     *
     * @param string $varName The name of the variable to be used in the message (without trailing '$')
     * @param array $classNames The class names that should be mentioned in the message
     * @param mixed $userInput The input that has been given
     * @return string
     */
    private function getTypeErrorText(string $varName, array $classNames, $userInput): string
    {
        return sprintf(
            '$%s should be a %s object, %s given.',
            $varName,
            implode(' or ', $classNames),
            $this->getUserInputInfo($userInput)
        );
    }

    /**
     * Simple function to determine what $userInput is.
     *
     * @param mixed $userInput
     * @return string A description of $userInput
     */
    private function getUserInputInfo($userInput): string
    {
        $info = gettype($userInput);

        if ($info === 'object') {
            if ((new ReflectionClass($userInput))->isAnonymous()) {
                $info = 'anonymous class instance';
            } else {
                $info = get_class($userInput);
            }
        } elseif (is_scalar($userInput)) {
            $info .= ' "' . (string)$userInput . '"';
        }

        return $info;
    }

    /**
     * @param $classNames
     * @param $userInput
     * @return bool
     */
    private function isClass($classNames, $userInput): bool
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
    private function isType($types, $userInput): bool
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
    private static function validateName(string $name): void
    {
        $name = trim($name);

        if ($name === "") {
            throw new InvalidArgumentException("A name cannot be an empty string");
        }

        if (!ctype_alnum($name)) {
            throw new InvalidArgumentException('A name can only contain alphanumeric characters');
        }

        if (is_numeric($name[0])) {
            throw new InvalidArgumentException('A name cannot begin with a numeric character');
        }

        if (strlen($name) >= 65535) {
            throw new InvalidArgumentException('A name cannot be longer than 65534 characters');
        }
    }
}
