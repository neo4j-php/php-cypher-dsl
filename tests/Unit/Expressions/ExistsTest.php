<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Expressions;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\MatchClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Expressions\Exists;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Expressions\Literals\String_;

/**
 * @covers \WikibaseSolutions\CypherDSL\Expressions\Exists
 */
class ExistsTest extends TestCase
{

    public function testToQuery()
    {
        $exists = new Exists(
            (new MatchClause)->addPattern(
                new Path(
                    [(new Node)->withVariable('person'),(new Node('Dog'))->withVariable('dog')],
                    [(new Relationship(Relationship::DIR_RIGHT))->addType('HAS_DOG')]
                )
            )
        );

        $this->assertSame("EXISTS { MATCH (person)-[:HAS_DOG]->(dog:Dog) }", $exists->toQuery());

        $exists = new Exists(
            (new MatchClause)->addPattern(
                new Path(
                    [(new Node)->withVariable('person'),(new Node('Dog'))->withVariable('dog')],
                    [(new Relationship(Relationship::DIR_RIGHT))->addType('HAS_DOG')]
                )
            ),
            (new WhereClause)->addExpression(
                new Equality(new Property(new Variable('toy'), 'name'),new String_('Banana'), false)
            )
        );

        $this->assertSame("EXISTS { MATCH (person)-[:HAS_DOG]->(dog:Dog) WHERE toy.name = 'Banana' }", $exists->toQuery());
    }
}
