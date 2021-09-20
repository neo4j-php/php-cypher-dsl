<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

class DeleteClause extends Clause
{
    /**
     * Used for checking if DETACH clause is needed
     *
     * @var bool $isDetach
     */
    private bool $isDetach = false;

    /**
     * The node that needs to be deleted
     *
     * @var Pattern $node
     */
    private Pattern $node;

    /**
     * sets the DETACH check
     *
     * @param bool $isDetach
     */
    public function setDetach(bool $isDetach): void
    {
        $this->isDetach = $isDetach;
    }

    /**
     * Set the node that needs to be deleted
     *
     * @param Pattern $node
     */
    public function setNode(Pattern $node): void
    {
        $this->node = $node;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        if ($this->isDetach ) {
            return "DETACH DELETE";
        }

        return "DELETE";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (!isset($this->node) ) {
            return "";
        }

        return $this->node->toQuery();
    }
}