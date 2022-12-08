<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\EndToEnd;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;

/**
 * This class contains some end-to-end tests to test the examples in the wiki.
 *
 * @coversNothing
 *
 * @see https://github.com/neo4j-php/php-cypher-dsl/wiki
 */
final class ExamplesTest extends TestCase
{
    public function testReadmeExample(): void
    {
        $tom = Query::node("Person")->withProperties(["name" => "Tom Hanks"]);
        $coActors = Query::node();

        $statement = Query::new()
            ->match($tom->relationshipTo(Query::node(), "ACTED_IN")->relationshipFrom($coActors, "ACTED_IN"))
            ->returning($coActors->property("name"))
            ->build();

        $this->assertStringMatchesFormat("MATCH (:Person {name: 'Tom Hanks'})-[:ACTED_IN]->()<-[:ACTED_IN]-(%s) RETURN %s.name", $statement);
    }

    public function testCallSubqueryClauseExample1(): void
    {
        $query = Query::new()
            ->call(static function (Query $query): void
            {
                $query->create(Query::node("Person"));
            })
            ->build();

        $this->assertSame("CALL { CREATE (:Person) }", $query);
    }

    public function testCallSubqueryClauseExample2(): void
    {
        $subQuery = Query::new()->create(Query::node("Person"));
        $query = Query::new()
            ->call($subQuery)
            ->build();

        $this->assertSame("CALL { CREATE (:Person) }", $query);
    }

    public function testCallSubqueryClauseExample3(): void
    {
        $person = Query::variable();
        $query = Query::new()
            ->match(Query::node('Person')->withVariable($person))
            ->call(static function (Query $query) use ($person): void
            {
                $query->remove($person->labeled('Person'));
            }, [$person])
            ->build();

        $this->assertStringMatchesFormat("MATCH (%s:Person) CALL { WITH %s REMOVE %s:Person }", $query);
    }
}
