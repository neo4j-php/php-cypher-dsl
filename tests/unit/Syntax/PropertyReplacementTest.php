<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Syntax;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement;

/**
 * @covers \WikibaseSolutions\CypherDSL\Syntax\PropertyReplacement
 */
final class PropertyReplacementTest extends TestCase
{
    public function testToQuery(): void
    {
        $propRepl = new PropertyReplacement(Query::variable('a')->property('b'), Query::boolean(true));

        $this->assertSame("a.b = true", $propRepl->toQuery());

        $propRepl->setMutate();

        $this->assertSame("a.b += true", $propRepl->toQuery());
    }

    public function testMutates(): void
    {
        $propRepl = new PropertyReplacement(Query::variable('a')->property('b'), Query::boolean(true));

        $this->assertFalse($propRepl->mutates());

        $propRepl->setMutate();

        $this->assertTrue($propRepl->mutates());

        $propRepl->setMutate(false);

        $this->assertFalse($propRepl->mutates());
    }

    public function testSetMutateReturnsSameInstance(): void
    {
        $expected = new PropertyReplacement(Query::variable('a')->property('b'), Query::boolean(true));
        $actual = $expected->setMutate();

        $this->assertSame($expected, $actual);
    }
}
