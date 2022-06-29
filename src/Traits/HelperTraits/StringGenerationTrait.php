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

namespace WikibaseSolutions\CypherDSL\Traits\HelperTraits;

/**
 * Used for automatically generating names.
 */
trait StringGenerationTrait
{
    /**
     * Generates a unique random string.
     *
     * @note It is not entirely guaranteed that this function gives a truly unique string. However, because the
     * number of possible strings is so huge, it should not be a problem.
     *
     * @param string $prefix The prefix to put before the name. Must start with a letter to adhere to cypher namings.
     * @param int|null $length The length of the generated name in bytes.
     *
     * @return string
     */
    private function generateString(string $prefix = '', int $length = 32): string
    {
        $random = '';
        for ($i = 0; $i < $length; ++$i) {
            $random .= dechex(mt_rand(0, 15));
        }

        return $prefix . $random;
    }
}
