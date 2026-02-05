<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

enum Precedence: int
{
    case DISJUNCTION = 1;
    case EXCLUSIVE_DISJUNCTION = 2;
    case CONJUNCTION = 3;
    case NEGATION = 4;
    case COMPARISON = 5;
    case ADDITIVE = 6;
    case MULTIPLICATIVE = 7;
    case EXPONENTIATION = 8;
    case UNARY = 9;
    case FUNCTIONAL = 10;
}
