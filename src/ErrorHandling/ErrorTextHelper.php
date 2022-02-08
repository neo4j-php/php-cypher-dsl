<?php

namespace WikibaseSolutions\CypherDSL\ErrorHandling;

class ErrorTextHelper {
    public static function getTypeErrorObjectArrayText(
        string $varName,
        $typeNames,
        $userInput
    ) {
        $userInputInfo = self::getUserInputInfo($userInput);

        if (is_array($typeNames)) {
            $typeNames = implode( ' or ', $typeNames );
        }

        return "\$$varName should consist of only $typeNames objects, $userInputInfo given.";
    }

    public static function getUserInputInfo($userInput) : string {
        $info = gettype( $userInput );
        if ( $info === 'object' ) {
            $info = get_class( $userInput );
        } else {
            $givenType .= ' "' . (string) $userInput . '"';
        }
    }
}
