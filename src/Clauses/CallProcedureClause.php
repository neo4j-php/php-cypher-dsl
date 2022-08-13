<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021-  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;

/**
 * This class represents a CALL procedure clause.
 *
 * The CALL clause is used to call a procedure deployed in the database.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 122)
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/call/
 * @see Query::callProcedure() for a more convenient method to construct this class
 */
final class CallProcedureClause extends Clause
{
    use CastTrait;
    use EscapeTrait;
    use ErrorTrait;

    /**
     * @var Procedure|null The procedure to call
     */
    private ?Procedure $procedure = null;

    /**
     * @var Variable[] The result fields that yielded
     */
    private array $yields = [];

    /**
     * Sets the procedure to call.
     *
     * @param Procedure $procedure The procedure to call
     * @return $this
     */
    public function setProcedure(Procedure $procedure): self
    {
        $this->procedure = $procedure;

        return $this;
    }

    /**
     * Adds a variable to yield.
     *
     * TODO: Allow variables to be aliased
     *
     * @param Variable|string $yields The variable to yield
     * @return $this
     */
    public function addYield(...$yields): self
    {
        $res = [];

        foreach ($yields as $yield) {
            $res[] = self::toName($yield);
        }

        $this->yields = array_merge($this->yields, $res);

        return $this;
    }

    /**
     * Returns the procedure to call.
     *
     * @return Procedure
     */
    public function getProcedure(): ?Procedure
    {
        return $this->procedure;
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

        $subject = $this->procedure->toQuery();

        if (!empty($this->yields)) {
            $yields = array_map(fn (Variable $variable): string => $variable->toQuery(), $this->yields);
            $subject .= sprintf(" YIELD %s", implode(", ", $yields));
        }

        return $subject;
    }
}
