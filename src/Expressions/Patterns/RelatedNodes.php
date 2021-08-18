<?php

namespace WikibaseSolutions\CypherDSL\Expressions\Patterns;

use InvalidArgumentException;

class RelatedNodes implements Pattern
{
    const DIR_RIGHT = ["-", "-", ">"];
    const DIR_LEFT = ["<", "-", "-"];

    private Pattern $left;
    private Pattern $right;
    private string $direction;

    /**
     * @param Pattern $left
     * @param Pattern $right
     */
    public function __construct(Pattern $left, Pattern $right, array $direction)
    {
        $this->left = $left;
        $this->right = $right;

        if ($direction !== self::DIR_RIGHT && $direction !== self::DIR_LEFT) {
            throw new InvalidArgumentException("The direction must be either 'DIR_LEFT', 'DIR_RIGHT' or 'RELATED_TO'");
        }

        $this->direction = $direction;
    }


    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $left = $this->left->toQuery();
        $right = $this->right->toQuery();

        return $left . implode("", $this->direction) . $right;
    }
}