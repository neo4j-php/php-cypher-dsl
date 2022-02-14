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

use ReflectionClass;
use TypeError;

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
     * @throws TypeError
     */
    private function assertClass(string $varName, $classNames, $userInput): void
    {
        if (!is_array($classNames)) {
            $classNames = [$classNames];
        }

        foreach ($classNames as $class) {
            if ($userInput instanceof $class)
                return;
        }

        throw new TypeError(
            $this->getTypeErrorText(
                $varName,
                $classNames,
                $userInput
            )
        );
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
}
