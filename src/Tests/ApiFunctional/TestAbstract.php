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

namespace Rossmitchell\Twofactor\Tests\ApiFunctional;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Rossmitchell\Twofactor\Tests\ApiFunctional\Helpers\Json;

class TestAbstract extends WebapiAbstract
{
    /** @var Json */
    private $jsonHelper;

    /**
     * Called before each Test
     */
    public function setUp()
    {
        parent::setUp();
        $this->jsonHelper = new Json();
    }

    /**
     * Used to get the JsonHelper in Child classes
     *
     * @return Json
     */
    public function getJsonHelper()
    {
        return $this->jsonHelper;
    }

    public function stripTraceDetails($response)
    {
        if(!is_object($response)) {
            $response = json_decode($response);
        }

        if(is_object($response) && property_exists($response, 'trace')) {
            unset($response->trace);
        }

        return $this->getJsonHelper()->convertReturnedDataToJson($response);
    }
}
