<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\CompositeTypeTraits\ListTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;

/**
 * This class represents a list of expressions. For example, this class can represent the following
 * construct:
 *
 * ['a', 2, n.property]
 *
 * @see Map for a construct that takes keys into account
 */
final class List_ implements ListType
{
    use ListTypeTrait;

    use CastTrait;
    use ErrorTrait;

    /**
     * @var AnyType[] The list of expressions
     */
    private array $expressions;

    /**
     * @param AnyType[] $expressions The list of expressions
     * @internal This method is not covered by the backwards compatibility promise of php-cypher-dsl
     */
    public function __construct(array $expressions = [])
    {
        self::assertClassArray('expressions', AnyType::class, $expressions);
        $this->expressions = $expressions;
    }

    /**
     * Add one or more expressions to the list.
     *
     * @param AnyType|Pattern|int|float|string|bool|array ...$expressions
     * @return $this
     */
    public function addExpression(...$expressions): self
    {
        $this->expressions = array_merge($this->expressions, array_map([self::class, 'toAnyType'], $expressions));

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
     *
     * @return bool
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
            fn (AnyType $expression): string => $expression->toQuery(),
            $this->expressions
        );

        return sprintf("[%s]", implode(", ", $expressions));
    }
}
