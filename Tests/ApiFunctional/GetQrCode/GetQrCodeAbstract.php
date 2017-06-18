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

namespace Rossmitchell\Twofactor\Tests\ApiFunctional\GetQrCode;

use Magento\Customer\Api\AccountManagementMeTest;
use Magento\Framework\Webapi\Rest\Request;
use Rossmitchell\Twofactor\Tests\ApiFunctional\TestAbstract;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\ConfigurationLoader;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\CustomerLoader;
use Rossmitchell\Twofactor\Tests\Integration\Helpers\Customer;

abstract class GetQrCodeAbstract extends TestAbstract
{
    use CustomerLoader;
    use ConfigurationLoader;

    private $customerHelper;
    private $token;
    public static $disabled = null;

    public static function getCustomerDataPath()
    {
        return __DIR__.'/../../Integration/Customer/_files/customer.php';
    }

    public static function getConfigurationDataPath()
    {
        if (static::$disabled === true) {
            return __DIR__.'/../../Integration/Customer/_files/two_factor_disabled.php';
        }

        return __DIR__.'/../../Integration/Customer/_files/two_factor_enabled.php';
    }

    /**
     * This is a central place for all of the requests to the getQrCode call to be made
     *
     * @return array|bool|float|int|string
     */
    public function makeRequest()
    {
        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/twofactor/getqrcode',
                'httpMethod'   => Request::HTTP_METHOD_GET,
                'token'        => $this->token,
            ],
        ];

        $response = $this->_webApiCall($serviceInfo);

        return $this->stripQrCode($response);
    }

    /**
     * This is used to make requests that should trigger an error. It will catch the exception and then compare the
     * response code with the one provided. It will also check to see if there is a stack trace and remove it if it
     * exists.
     *
     * These last two behaviours can be disabled by passing through false to either of the optional parameters. This
     * should only be used when debugging your tests, and should not make it into the final versions of the tests
     *
     * @param int  $expectedCode        - The HTTP return code that the error should return
     * @param bool $checkResponseCode   - If set to false then this will not check the return code
     * @param bool $stripTrace          - If set to false then the stack trace will not be removed from the response.
     *                                  You must run the tests in developer mode for this to be returned
     *
     * @return array|bool|float|int|string
     */
    public function makeRequestThatShouldFail($expectedCode, $checkResponseCode = true, $stripTrace = true)
    {
        try {
            $result     = $this->makeRequest();
            $actualCode = 200;
        } catch (\Exception $e) {
            $result     = $e->getMessage();
            $actualCode = $e->getCode();
        }

        if ($checkResponseCode !== false) {
            $this->assertEquals($expectedCode, $actualCode);
        }

        if ($stripTrace !== false) {
            $result = $this->stripTraceDetails($result);
        }

        return $result;
    }

    public function logInCustomer($customerEmail)
    {
        $this->getCustomerHelper()->login($customerEmail);
    }

    /**
     * @return Customer
     */
    private function getCustomerHelper()
    {
        if (null === $this->customerHelper) {
            $this->customerHelper = new Customer();
        }

        return $this->customerHelper;
    }

    public function resetTokenForCustomer($username, $password)
    {
        // get customer ID token
        $serviceInfo = [
            'rest' => [
                'resourcePath' => AccountManagementMeTest::RESOURCE_PATH_CUSTOMER_TOKEN,
                'httpMethod'   => Request::HTTP_METHOD_POST,
            ],
        ];
        $requestData = ['username' => $username, 'password' => $password];
        $this->token = $this->_webApiCall($serviceInfo, $requestData);
    }

    /**
     * Testing has shown that the QR code is different on different machines, so I need a way to ensure that the tests
     * will pass both locally and on travis. To do this we are going to replace the actual QR code with a simple
     * string, and work on the assumption that it is being returned correctly.
     *
     * I have only myself to blame when I look at this method in the future and ask what was I thinking
     *
     * @param $response
     *
     * @return mixed
     */
    private function stripQrCode($response)
    {
        if (!is_object($response)) {
            $response = json_decode(json_encode($response));
        }

        if (is_object($response) && property_exists($response, 'qr_code')) {
            $response->qr_code = $this->getQrCodeForEnabledCustomer();
        }

        return $response;
    }

    /**
     * Get a fake QR code, at least this should be obvious enough that it has been stripped out if I debug the tests
     *
     * @return string
     */
    public function getQrCodeForEnabledCustomer()
    {
        return 'A valid QR Code';
    }


}
