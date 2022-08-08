<?php

namespace WikibaseSolutions\CypherDSL\Traits\PatternTraits;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;

/**
 * This trait provides a default implementation to satisfy the "Pattern" interface.
 *
 * @implements Pattern
 */
trait PatternTrait
{
    use CastTrait;

    /**
     * @var Variable|null The variable that this object is assigned
     */
    protected ?Variable $variable = null;

    /**
     * Explicitly assign a named variable to this object.
     *
     * @param Variable|string $variable
     * @return $this
     */
    public function withVariable($variable): self
    {
        $this->variable = self::toVariable($variable);

        return $this;
    }

    /**
     * Returns the variable of this object. This function generates a variable if none has been set.
     *
     * @return Variable
     */
    public function getVariable(): Variable
    {
        if (!isset($this->variable)) {
            $this->variable = new Variable();
        }

        return $this->variable;
    }
}
