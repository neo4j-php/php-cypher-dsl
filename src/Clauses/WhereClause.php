<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Clauses;

use InvalidArgumentException;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * This class represents a WHERE clause.
 *
 * WHERE adds constraints to the patterns in a MATCH or OPTIONAL MATCH clause or filters the results of a WITH clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 83)
 * @see Query::where() for a more convenient method to construct this class
 */
final class WhereClause extends Clause
{
    public const AND = 'and';

    public const OR = 'or';

    public const XOR = 'xor';

    /**
     * @var null|BooleanType The expression to match
     */
    private ?BooleanType $expression = null;

    /**
     * Add an expression to this WHERE clause.
     *
     * @param bool|BooleanType $expression The expression to add to the WHERE clause
     * @param string           $operator   The operator to use to combine the given expression with the existing expression, should
     *                                     be one of WhereClause::AND, WhereClause::OR or WhereClause::XOR
     *
     * @return $this
     */
    public function addExpression(BooleanType|bool $expression, string $operator = self::AND): self
    {
        $expression = CastUtils::toBooleanType($expression);

        if ($operator !== self::AND && $operator !== self::OR && $operator !== self::XOR) {
            throw new InvalidArgumentException('$operator must either be "and", "xor" or "or"');
        }

        if ($this->expression === null) {
            $this->expression = $expression;
        } elseif ($operator === self::AND) {
            $this->expression = $this->expression->and($expression);
        } elseif ($operator === self::OR) {
            $this->expression = $this->expression->or($expression);
        } elseif ($operator === self::XOR) {
            $this->expression = $this->expression->xor($expression);
        }

        return $this;
    }

    /**
     * Returns the expression to match.
     */
    public function getExpression(): ?BooleanType
    {
        return $this->expression;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "WHERE";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (!isset($this->expression)) {
            return "";
        }

        return $this->expression->toQuery();
    }
}
