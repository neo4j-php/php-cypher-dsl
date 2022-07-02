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

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Traits\HelperTraits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * This class represents a CALL procedure clause. The CALL clause is used to call a procedure deployed in the database.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 122)
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/call/
 * @see Query::callProcedure() for a more convenient method to construct this class
 */
class CallProcedureClause extends Clause
{
    use EscapeTrait;
    use ErrorTrait;

    /**
     * @var string|null The procedure to call
     */
    private ?string $procedure = null;

    /**
     * @var PropertyType[] The arguments passed to the procedure
     */
    private array $arguments = [];

    /**
     * @var Variable[] The result fields that will be returned
     */
    private array $yields = [];

    /**
     * Sets the procedure to call. This can be for instance "apoc.load.json". This procedure name is automatically
	 * escaped.
     *
     * @param string $procedure The procedure to call
     * @return $this
     */
    public function setProcedure(string $procedure): self
    {
        $this->procedure = $procedure;

        return $this;
    }

	/**
	 * Sets the literal arguments to pass to this procedure call. This overwrites any previously passed
	 * arguments.
	 *
	 * @param AnyType[] $arguments The arguments to pass to the procedure
	 * @return $this
	 */
	public function setArguments(array $arguments): self
	{
		foreach ($arguments as $argument) {
			$this->assertClass('arguments', AnyType::class, $argument);
		}

		$this->arguments = $arguments;

		return $this;
	}

    /**
     * Add a literal argument to pass to this procedure call.
     *
     * @param AnyType $argument The argument to pass to the procedure
     * @return $this
     */
    public function addArgument(AnyType $argument): self
    {
		$this->arguments[] = $argument;

		return $this;
    }

	/**
	 * Used to explicitly select which available result fields are returned as newly-bound
	 * variables. If a key is non-numerical, it will be used as an alias.
	 *
	 * @param Variable[] $variables
	 * @return $this
	 */
	public function setYields(array $variables): self
	{
		foreach ($variables as $variable) {
			$this->assertClass('variables', Variable::class, $variable);
		}

		$this->yields = $variables;

		return $this;
	}

	/**
	 * Adds a variable to yield.
	 *
	 * @param Variable $variable The variable to yield
	 * @param string|null $alias Optionally the alias to use for the variable
	 * @return $this
	 */
	public function addYield(Variable $variable, ?string $alias = null): self
	{
		if ($alias !== null) {
			$this->yields[$alias] = $variable;
		} else {
			$this->yields[] = $variable;
		}

		return $this;
	}

    /**
     * Used to explicitly select which available result fields are returned as newly-bound
     * variables.
     *
     * @param Variable[] $variables
     * @return $this
	 * @deprecated Use CallProcedureClause::setYields(...) instead. This function was deprecated because it does
	 *  not conform to the naming scheme (see #23).
     */
    public function yields(array $variables): self
    {
        return $this->setYields($variables);
    }

	/**
	 * Sets the literal arguments to pass to this procedure call. This overwrites any previously passed
	 * arguments.
	 *
	 * @param AnyType[] $arguments The arguments to pass to the procedure
	 * @return $this
	 * @deprecated Use CallProcedureClause::setArguments(...) instead. This function was deprecated because it does
	 *  not conform to the naming scheme (see #23).
	 */
	public function withArguments(array $arguments): self
	{
		return $this->setArguments($arguments);
	}

    /**
     * Returns the procedure to call.
     *
     * @return string|null
     */
    public function getProcedure(): ?string
    {
        return $this->procedure;
    }

    /**
     * Returns the arguments of the procedure.
     *
     * @return AnyType[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Returns the variables to yield.
     *
     * @return Variable[]
     */
    public function getYields(): array
    {
        return $this->yields;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "CALL";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (!isset($this->procedure)) {
            return "";
        }

		$procedure = implode(
			'.',
			array_map(fn (string $part): string => $this->escape($part), explode('.', $this->procedure))
		);

        $arguments = implode(
            ", ",
            array_map(fn (AnyType $pattern): string => $pattern->toQuery(), $this->arguments)
        );

        if (count($this->yields) > 0) {
			$yieldParameters = [];
			foreach ($this->yields as $alias => $yieldVariable) {
				$yieldParameters[] = is_int($alias) ?
					$yieldVariable->toQuery() :
					sprintf("%s AS %s", $yieldVariable->toQuery(), $this->escape($alias));
			}

            return sprintf("%s(%s) YIELD %s", $procedure, $arguments, implode(", ", $yieldParameters));
        }

        return sprintf("%s(%s)", $procedure, $arguments);
    }
}
