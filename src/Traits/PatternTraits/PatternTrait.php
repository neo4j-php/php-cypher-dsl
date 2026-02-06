<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Traits\PatternTraits;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * This trait provides a default implementation to satisfy the "Pattern" interface.
 */
trait PatternTrait
{
    /**
     * @var null|Variable The variable that this object is assigned
     */
    protected ?Variable $variable = null;

    /**
     * Returns whether a variable has been set for this pattern.
     */
    public function hasVariableSet(): bool
    {
        return $this->variable !== null;
    }

    /**
     * Explicitly assign a named variable to this object.
     */
    public function withVariable(Variable|string|null $variable): self
    {
        $this->variable = $variable === null ? null : CastUtils::toName($variable);

        return $this;
    }

    /**
     * Returns the variable of this object. This function generates a variable if none has been set.
     */
    public function getVariable(): Variable
    {
        if (!isset($this->variable)) {
            $this->variable = new Variable();
        }

        return $this->variable;
    }
}
