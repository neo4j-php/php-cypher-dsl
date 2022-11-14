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

use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\IntegerType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * This class represents a SKIP clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/skip/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 96)
 * @see Query::skip() for a more convenient method to construct this class
 */
final class SkipClause extends Clause
{
    use CastTrait;

    /**
     * The expression of the SKIP statement.
     */
    private ?IntegerType $skip = null;

    /**
     * Sets the expression that returns the skip.
     *
     * @param int|IntegerType $skip The amount to skip
     *
     * @return SkipClause
     */
    public function setSkip($skip): self
    {
        $this->skip = self::toIntegerType($skip);

        return $this;
    }

    /**
     * Returns the amount to skip.
     *
     * @return null|NumeralType
     */
    public function getSkip(): ?IntegerType
    {
        return $this->skip;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "SKIP";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (isset($this->skip)) {
            return $this->skip->toQuery();
        }

        return "";
    }
}
