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
        $path->setVariable('a');

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

    public function testPathMerge(): void
    {
        $pathX = new Path([new Node(), new Node()], [new Relationship(Relationship::DIR_UNI)]);
        $pathY = new Path([new Node(), new Node()], [new Relationship(Relationship::DIR_UNI)]);

        $pathX->setVariable('x')->relationshipTo($pathY->setVariable('y'));

        $this->assertEquals('x = ()-[]-()-[]->()-[]-()', $pathX->toQuery());
    }

    public function testRelationshipLong(): void
    {
        $path = new Path(new Node());
        $path->relationshipUni(new Node('Label'))
            ->relationshipTo((new Node())->setVariable('b'))
            ->relationshipFrom(new Node(), 'TYPE', ['x' => Query::literal('y')], 'c')
            ->relationship(new Relationship(Relationship::DIR_UNI), (new Node())->setVariable('d'))
            ->setVariable('a');

        $this->assertEquals('a = ()-[]-(:Label)-[]->(b)<-[c:TYPE {x: \'y\'}]-()-[]-(d)', $path->toQuery());

        $this->assertEquals([
            new Node(),
            new Node('Label'),
            (new Node())->setVariable('b'),
            new Node(),
            (new Node())->setVariable('d'),
        ], $path->getNodes());

        $this->assertEquals([
            new Relationship(Relationship::DIR_UNI),
            new Relationship(Relationship::DIR_RIGHT),
            (new Relationship(Relationship::DIR_LEFT))
                ->withType('TYPE')
                ->addProperties(['x' => Query::literal('y')])
                ->setVariable('c'),
            new Relationship(Relationship::DIR_UNI),
        ], $path->getRelationships());
    }
}
