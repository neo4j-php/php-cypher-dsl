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
    private $hasName;

    protected function setUp(): void
    {
        $this->hasName = new class()
        {
            use NameGenerationTrait {
                generateIdentifier as public;
            }
        };
    }

    public function testGenerateName(): void
    {
        $this->assertMatchesRegularExpression('/var\w{32}/', $this->hasName->generateIdentifier());
        $this->assertMatchesRegularExpression('/x\w{16}/', $this->hasName->generateIdentifier('x', 16));
    }
}
