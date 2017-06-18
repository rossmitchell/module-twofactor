<?php
/**
 * A two factor authentication module that protects both the admin and customer logins
 * Copyright (C) 2017  Ross Mitchell
 *
 * This file is part of Rossmitchell/Twofactor.
 *
 * Rossmitchell/Twofactor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Rossmitchell\Twofactor\Tests\ApiFunctional\Helpers;

use InvalidArgumentException;

/**
 * The API requests that are used in these tests require the actual post data to be sent through to be PHP objects /
 * arrays, rather than the JSON that is used.
 *
 * When I'm testing I want to see the actual JSON in the tests, which means everything has to be converted back and
 * forth.
 *
 * Rather than doing this in each and every test, this helper class will handle it
 *
 * Class Json
 * @package Rossmitchell\Twofactor\Tests\ApiFunctional\Helpers
 */
class Json
{
    /**
     * Used to convert the JSON that should be sent through as part of the request into a PHP object / array
     *
     * @param $jsonString
     *
     * @return mixed
     */
    public function convertJsonToPostData($jsonString)
    {
        $decodedString = json_decode($jsonString);
        if (empty($decodedString)) {
            throw new InvalidArgumentException('Could not decode the JSON');
        }

        return $decodedString;
    }

    /**
     * Used to convert the JSON that is returned, which has already been converted into a PHP array / object, back into
     * JSON again. We use the Pretty Print option for json_encode to ensure the returned data can be checked by eye
     * more easily
     *
     * @param $returnedData
     *
     * @return string
     */
    public function convertReturnedDataToJson($returnedData)
    {
        $encodedString = json_encode($returnedData, JSON_PRETTY_PRINT);
        if (empty($encodedString)) {
            throw new InvalidArgumentException('Could not decode the JSON');
        }

        return $encodedString;
    }
}
