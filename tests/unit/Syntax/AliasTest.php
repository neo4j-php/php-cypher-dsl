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
use WikibaseSolutions\CypherDSL\Syntax\Alias;

/**
 * @covers \WikibaseSolutions\CypherDSL\Syntax\Alias
 */
final class AliasTest extends TestCase
{
    public function testGetOriginal(): void
    {
        $original = Query::variable('a');
        $variable = Query::variable('b');

        $alias = new Alias($original, $variable);

        $this->assertSame($original, $alias->getOriginal());
    }

    public function testGetVariable(): void
    {
        $original = Query::variable('a');
        $variable = Query::variable('b');

        $alias = new Alias($original, $variable);

        $this->assertSame($variable, $alias->getVariable());
    }

    public function testToQuery(): void
    {
        $original = Query::variable('a');
        $variable = Query::variable('b');

        $alias = new Alias($original, $variable);

        $this->assertSame('a AS b', $alias->toQuery());
    }
}
