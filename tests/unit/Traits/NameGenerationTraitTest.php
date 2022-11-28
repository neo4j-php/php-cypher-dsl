<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Traits\NameGenerationTrait;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\NameGenerationTrait
 */
final class NameGenerationTraitTest extends TestCase
{
    private $trait;

    protected function setUp(): void
    {
        $this->trait = new class()
        {
            use NameGenerationTrait {
                generateIdentifier as public;
            }
        };
    }

    public function testGenerateIdentifierWithoutPrefix(): void
    {
        $this->assertMatchesRegularExpression('/var\w{32}/', $this->trait->generateIdentifier());
    }

    public function testGenerateIdentifierWithPrefix(): void
    {
        $this->assertMatchesRegularExpression('/x\w{32}/', $this->trait->generateIdentifier('x'));
    }

    public function testGenerateIdentifierWithPrefixAndLength(): void
    {
        $this->assertMatchesRegularExpression('/x\w{16}/', $this->trait->generateIdentifier('x', 16));
    }
}
