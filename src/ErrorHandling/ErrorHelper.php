<?php

namespace WikibaseSolutions\CypherDSL\ErrorHandling;

use TypeError;

/**
 * Convenience class including simple assertions and error reporting functions
 */
class ErrorHelper {

    /**
     * Asserts that $userInput is an instance of one of the provided $classNames (polyfill for php 8.0 Union types)
     *
     * @param  string          $varName     The name of the userinput variable, to be used in the error message.
     * @param  string|string[] $classNames  The classnames that should be tested against
     * @param  mixed           $userInput   The input that should be tested
     * @throws TypeError
     */
    public static function assertClass(string $varName, $classNames, $userInput) : void {
        if (!is_array($classNames)) {
            $classNames = [$classNames];
        }
        foreach ($classNames as $class) {
            if ($userInput instanceof $class)
                return;
        }
        throw new TypeError(
            self::getTypeErrorText(
                $varName,
                $classNames,
                $userInput
            )
        );
    }

    /**
     * Give a nice error message about $userInput not being an object with one of the $classNames types.
     *
     * @param string $varname     The name of the variable to be used in the message (without trailing '$')
     * @param array  $classNames  The classnames that should be mentioned in the message
     * @param mixed  $userInput   The input that has been given.
     */
    public static function getTypeErrorText(
        string $varName,
        array $classNames,
        $userInput
    ) : string {
        return
            "\$$varName should be a " .
            implode(' or ', $classNames) . " object, " .
            self::getUserInputInfo($userInput) . "given.";
    }

    /**
     * Simple function to determine what $userInput is.
     *
     * @param mixed $userInput
     * @return string A description of $userInput.
     */
    public static function getUserInputInfo($userInput) : string {
        $info = gettype( $userInput );
        if ( $info === 'object' ) {
            $info = get_class( $userInput );
        } else {
            $info .= ' "' . (string) $userInput . '"';
        }
        return $info;
    }
}
