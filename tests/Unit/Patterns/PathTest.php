<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Patterns;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;

class PathTest extends TestCase
{
    public function testEmpty(): void
    {
        self::assertEquals('', (new Path())->toQuery());
        self::assertEquals('', (new Path(null, []))->toQuery());
        self::assertEquals('', (new Path([], []))->toQuery());
        self::assertEquals('', (new Path([], [new Relationship(Relationship::DIR_UNI)]))->toQuery());
    }

    public function testSingleNode(): void
    {
        $path = new Path(new Node());

        self::assertEquals('()', $path->toQuery());
    }

    public function testSingleNodeNamed(): void
    {
        $path = new Path(new Node());
        $path->named('a');

        self::assertEquals('a = ()', $path->toQuery());
    }

    public function testSingle(): void
    {
        $path = new Path(new Node(), new Relationship(Relationship::DIR_UNI));

        self::assertEquals('()', $path->toQuery());
    }

    public function testSingleNoRel(): void
    {
        $path = new Path([new Node(), new Node()]);

        self::assertEquals('()', $path->toQuery());
    }

    public function testRelationshipLong(): void
    {
        $path = new Path(new Node());
        $path->relationshipUni(new Node('Label'))
            ->relationshipTo((new Node())->named('b'))
            ->relationshipFrom(new Node(), 'TYPE', ['x' => Query::literal('y')], 'c')
            ->relationship(new Relationship(Relationship::DIR_UNI), (new Node())->named('d'))
            ->named('a');

        self::assertEquals('a = ()-[]-(:Label)-[]->(b)<-[c:TYPE {x: \'y\'}]-()-[]-(d)', $path->toQuery());

        self::assertEquals([
            new Node(),
            new Node('Label'),
            (new Node())->named('b'),
            new Node(),
            (new Node())->named('d')
        ], $path->getNodes());

        self::assertEquals([
            new Relationship(Relationship::DIR_UNI),
            new Relationship(Relationship::DIR_RIGHT),
            (new Relationship(Relationship::DIR_LEFT))
                ->withType('TYPE')
                ->withProperties(['x' => Query::literal('y')])
                ->named('c'),
            new Relationship(Relationship::DIR_UNI)
        ], $path->getRelationships());
    }
}
