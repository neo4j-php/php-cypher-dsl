<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Utils\CastUtils;

/**
 * This class represents a list of expressions. For example, this class can represent the following
 * construct:.
 *
 * ['a', 2, n.property]
 *
 * @see Map for a construct that takes keys into account
 */
final class List_ implements ListType
{
    use ListTypeTrait;

    /**
     * @var AnyType[] The list of expressions
     */
    private array $expressions;

    /**
     * @param array $expressions The list of expressions
     *
     * @internal This method is not covered by the backwards compatibility promise of php-cypher-dsl
     */
    public function __construct(array $expressions = [])
    {
        $this->expressions = array_map(CastUtils::toAnyType(...), $expressions);
    }

    /**
     * Add one or more expressions to the list.
     *
     * @param AnyType|array|bool|float|int|Pattern|string ...$expressions
     *
     * @return $this
     */
    public function addExpression(AnyType|bool|float|int|array|Pattern|string ...$expressions): self
    {
        $this->expressions = array_merge($this->expressions, array_map(CastUtils::toAnyType(...), $expressions));

        return $this;
    }

    /**
     * The homogeneous list of expressions.
     *
     * @return AnyType[]
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }

    /**
     * Returns whether this list is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->expressions);
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $expressions = array_map(
            static fn (AnyType $expression): string => $expression->toQuery(),
            $this->expressions
        );

        return sprintf("[%s]", implode(", ", $expressions));
    }
}
