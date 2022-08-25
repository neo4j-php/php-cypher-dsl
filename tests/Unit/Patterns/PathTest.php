<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Patterns;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Query;

final class PathTest extends TestCase
{
    public function testEmpty(): void
    {
        $this->assertEquals('', (new Path())->toQuery());
        $this->assertEquals('', (new Path([], []))->toQuery());
        $this->assertEquals('', (new Path([], []))->toQuery());
        $this->assertEquals('', (new Path([], [new Relationship(Relationship::DIR_UNI)]))->toQuery());
    }

    public function testSingleNode(): void
    {
        $path = new Path(new Node());

        $this->assertEquals('()', $path->toQuery());
    }

    public function testSingleNodeNamed(): void
    {
        $path = new Path(new Node());
        $path->withVariable('a');

        $this->assertEquals('a = ()', $path->toQuery());
    }

    public function testSingle(): void
    {
        $path = new Path(new Node(), new Relationship(Relationship::DIR_UNI));

        $this->assertEquals('()', $path->toQuery());
    }

    public function testSingleNoRel(): void
    {
        $path = new Path([new Node(), new Node()]);

        $this->assertEquals('()', $path->toQuery());
    }

    public function testPathMerge(): void
    {
        $pathX = new Path([new Node(), new Node()], [new Relationship(Relationship::DIR_UNI)]);
        $pathY = new Path([new Node(), new Node()], [new Relationship(Relationship::DIR_UNI)]);

        $pathX->withVariable('x')->relationshipTo($pathY->withVariable('y'));

        $this->assertEquals('x = ()--()-->()--()', $pathX->toQuery());
    }

    public function testRelationshipLong(): void
    {
        $path = new Path(new Node());
        $path->relationshipUni(new Node('Label'))
            ->relationshipTo((new Node())->withVariable('b'))
            ->relationshipFrom(new Node(), 'TYPE', ['x' => Query::literal('y')], 'c')
            ->relationship(new Relationship(Relationship::DIR_UNI), (new Node())->withVariable('d'))
            ->withVariable('a');

        $this->assertEquals('a = ()--(:Label)-->(b)<-[c:TYPE {x: \'y\'}]-()--(d)', $path->toQuery());

        $this->assertEquals([
            new Node(),
            new Node('Label'),
            (new Node())->withVariable('b'),
            new Node(),
            (new Node())->withVariable('d'),
        ], $path->getNodes());

        $this->assertEquals([
            new Relationship(Relationship::DIR_UNI),
            new Relationship(Relationship::DIR_RIGHT),
            (new Relationship(Relationship::DIR_LEFT))
                ->addType('TYPE')
                ->addProperties(['x' => Query::literal('y')])
                ->withVariable('c'),
            new Relationship(Relationship::DIR_UNI),
        ], $path->getRelationships());
    }

    public function testPathCanBeTreatedAsBoolean(): void
    {
        $pathA = new Path([new Node(), new Node()], [new Relationship(Relationship::DIR_UNI)]);
        $pathB = new Path([new Node(), new Node()], [new Relationship(Relationship::DIR_RIGHT)]);

        $this->assertSame("()--() AND ()-->()", $pathA->and($pathB, false)->toQuery());
    }
}
