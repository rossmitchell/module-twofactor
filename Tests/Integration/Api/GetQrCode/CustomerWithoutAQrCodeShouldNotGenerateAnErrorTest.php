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

namespace Rossmitchell\Twofactor\Tests\Integration\Api\GetQrCode;

use Zend\Http\Headers;

class CustomerWithoutAQrCodeShouldNotGenerateAnErrorTest extends AbstractApiTestClass
{

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadCustomer
     * @magentoDataFixture loadConfiguration
     */
    public function testCustomerWithoutDoesNotError()
    {
        $token = $this->getToken('not_enabled@example.com');
        $header  = new Headers();
        $header->addHeaderLine('Authorization: Bearer ', $token);
        $this->getRequest()->setHeaders($header);
        $returnedJson = $this->makeRequest();
        $expectedJson = $this->getExpectedJson();

        $this->assertJsonStringEqualsJsonString($expectedJson, $returnedJson);
    }

    private function getExpectedJson()
    {
        return <<<JSON
{
    "email": "not_enabled@example.com",
    "is_using_two_factor": false
}
JSON;

    }
}
