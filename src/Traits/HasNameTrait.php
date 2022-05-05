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

namespace WikibaseSolutions\CypherDSL\Traits;

use function mt_rand;

trait HasNameTrait
{
    use ErrorTrait;

    private string $name;

    private static int $automaticVariableLength = 32;

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string
    {
        if (!isset($this->name)) {
            $this->configureName(null, 'var');
        }

        return $this->name;
    }

    /**
     * Generates a unique random identifier.
     *
     * @note It is not entirely guaranteed that this function gives a truly unique identifier. However, because the
     * number of possible IDs is so huge, it should not be a problem.
     *
     * @param string $prefix The prefix to put before the name. Must start with a letter to adhere to cypher namings.
     *
     * @param int|null $length The length of the generated name in bytes.
     *
     * @return string
     */
    private function generateName(string $prefix = 'var', int $length = null): string
    {
        $length ??= self::$automaticVariableLength;

        $random = '';
        for ($i = 0; $i < $length; ++$i) {
            $random .= dechex(mt_rand(0, 15));
        }
        return $prefix . $random;
    }

    private function configureName(?string $givenName, string $prefix, int $length = null): void
    {
        $name = $givenName ?? $this->generateName($prefix, $length);

        self::assertValidName($name);

        $this->name = $name;
    }
}
