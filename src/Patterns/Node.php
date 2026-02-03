<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PropertyPatternTrait;
use WikibaseSolutions\CypherDSL\Utils\NameUtils;

/**
 * This class represents a node.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-node Corresponding documentation on Neo4j.com
 */
final class Node implements CompletePattern, PropertyPattern, RelatablePattern
{
    use PropertyPatternTrait;

    /**
     * @var string[] The labels of this node
     */
    private array $labels = [];

    /**
     * @param null|string $label The initial label to include on this node
     *
     * @internal This method is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(?string $label = null)
    {
        if ($label !== null) {
            $this->labels[] = $label;
        }
    }

    /**
     * Sets the labels of this node. This overwrites any previously set labels.
     *
     * @param string[] $labels
     *
     * @return $this
     */
    public function withLabels(array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * Adds one or more labels to this node.
     *
     * @return $this
     */
    public function addLabel(string ...$label): self
    {
        $this->labels = array_merge($this->labels, $label);

        return $this;
    }

    /**
     * Returns the labels of the node.
     *
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Returns a label with the variable in this node.
     *
     * @param string ...$labels The labels to attach to the variable in this node
     */
    public function labeled(string ...$labels): Label
    {
        return new Label($this->getVariable(), ...$labels);
    }

    /**
     * @inheritDoc
     */
    public function relationship(Relationship $relationship, Pattern $pattern): Path
    {
        return (new Path($this))->relationship($relationship, $pattern);
    }

    /**
     * @inheritDoc
     */
    public function relationshipTo(Pattern $pattern, ?string $type = null, $properties = null, $name = null): Path
    {
        return (new Path($this))->relationshipTo($pattern, $type, $properties, $name);
    }

    /**
     * @inheritDoc
     */
    public function relationshipFrom(Pattern $pattern, ?string $type = null, $properties = null, $name = null): Path
    {
        return (new Path($this))->relationshipFrom($pattern, $type, $properties, $name);
    }

    /**
     * @inheritDoc
     */
    public function relationshipUni(Pattern $pattern, ?string $type = null, $properties = null, $name = null): Path
    {
        return (new Path($this))->relationshipUni($pattern, $type, $properties, $name);
    }

    /**
     * Returns the string representation of this relationship that can be used directly
     * in a query.
     */
    public function toQuery(): string
    {
        return sprintf("(%s)", $this->nodeInnerToString());
    }

    /**
     * Returns the string representation of the inner part of a node.
     */
    private function nodeInnerToString(): string
    {
        $nodeInner = "";

        if (isset($this->variable)) {
            $nodeInner .= $this->variable->toQuery();
        }

        if ($this->labels !== []) {
            foreach ($this->labels as $label) {
                $nodeInner .= ":" . NameUtils::escape($label);
            }
        }

        if (isset($this->properties)) {
            if ($nodeInner !== "") {
                $nodeInner .= " ";
            }

            if (!$this->properties instanceof Map || !$this->properties->isEmpty()) {
                $nodeInner .= $this->properties->toQuery();
            }
        }

        return $nodeInner;
    }
}
