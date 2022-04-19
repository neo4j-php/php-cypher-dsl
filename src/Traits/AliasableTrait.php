<?php

namespace WikibaseSolutions\CypherDSL\Traits;

use WikibaseSolutions\CypherDSL\Alias;
use WikibaseSolutions\CypherDSL\Variable;
use function is_string;

trait AliasableTrait
{
    use ErrorTrait;

    /**
     * Creates an alias of the current expression.
     *
     * @param string|Variable $variable
     * @return Alias
     */
    public function alias($variable): Alias
    {
        self::assertClass($variable, [Variable::class, 'string'], 'variable');

        $variable = is_string($variable) ? new Variable($variable) : $variable;

        return new Alias($this, $variable);
    }
}
