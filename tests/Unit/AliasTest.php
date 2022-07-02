<?php

namespace WikibaseSolutions\CypherDSL\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Alias;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Alias
 */
class AliasTest extends TestCase
{
    private Alias $alias;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alias = new Alias(
            new Variable("a"),
            new Variable("b")
        );
    }

    public function testToQuery(): void
    {
        $this->assertSame("a AS b", $this->alias->toQuery());
    }

    public function testGetOriginal(): void
    {
        $this->assertEquals(new Variable("a"), $this->alias->getOriginal());
    }

    public function testGetVariable(): void
    {
        $this->assertEquals(new Variable("b"), $this->alias->getVariable());
    }
}
