<?php

namespace WikibaseSolutions\CypherDSL\Traits;

use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * Trait used by objects that can contain a variable (such as relationships, nodes or paths).
 */
trait HasVariableTrait
{
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