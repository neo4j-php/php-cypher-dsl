<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Expressions\Exists;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Direction;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Exists
 */
final class ExistsTest extends TestCase
{
    public function testToQuery(): void
    {
        $exists = new Exists(
            (new MatchClause)->addPattern(
                new Path(
                    [(new Node)->withVariable('person'), (new Node('Dog'))->withVariable('dog')],
                    (new Relationship(Direction::RIGHT))->addType('HAS_DOG')
                )
            )
        );

        $this->assertSame("EXISTS { MATCH (person)-[:HAS_DOG]->(dog:Dog) }", $exists->toQuery());

        $exists = new Exists(
            (new MatchClause)->addPattern(
                new Path(
                    [(new Node)->withVariable('person'), (new Node('Dog'))->withVariable('dog')],
                    [(new Relationship(Direction::RIGHT))->addType('HAS_DOG')]
                )
            ),
            (new WhereClause)->addExpression(
                new Equality(new Property(new Variable('toy'), 'name'), new String_('Banana'))
            )
        );

        $this->assertSame("EXISTS { MATCH (person)-[:HAS_DOG]->(dog:Dog) WHERE toy.name = 'Banana' }", $exists->toQuery());
    }
}
