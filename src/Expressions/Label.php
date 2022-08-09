<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions;

use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents a label. A label in Cypher would be something like "n:German" or "n:German:Swedish". Label implements
 * BooleanType, since it can be used in a "WHERE" clause like so:
 *
 *  MATCH (n) WHERE n:label1:label2 RETURN n
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 85)
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/#filter-on-node-label
 */
final class Label implements BooleanType
{
    use BooleanTypeTrait;

    use EscapeTrait;

    /**
     * @var Variable The variable to which this label belongs
     */
    private Variable $variable;

    /**
     * @var string[] The names of the labels
     */
    private array $labels;

    /**
     * Label constructor.
     *
     * @param Variable $variable The variable to attach the labels to
     * @param string ...$labels The labels to attach to the variable
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(Variable $variable, string ...$labels)
    {
        $this->variable = $variable;
        $this->labels = $labels;
    }

    /**
     * Adds one or more labels to this class.
     *
     * @param string ...$labels One or more labels to add
     * @return $this
     */
    public function addLabels(string ...$labels): self
    {
        $this->labels = array_merge($this->labels, $labels);

        return $this;
    }

    /**
     * Returns the labels in this class.
     *
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Returns the variable to which the labels are attached.
     *
     * @return Variable
     */
    public function getVariable(): Variable
    {
        return $this->variable;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $query = $this->variable->toQuery();

        foreach ($this->labels as $label) {
            $query = sprintf("%s:%s", $query, $this->escape($label));
        }

        return $query;
    }
}
