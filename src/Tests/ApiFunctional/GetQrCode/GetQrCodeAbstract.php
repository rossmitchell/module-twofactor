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

        return $this->_webApiCall($serviceInfo);
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

    public function getQrCodeForEnabledCustomer()
    {
        return 'data:image\/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAIAAAAiOjnJAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAFBUlEQVR4nO3d3crVSBCG0VHm\/i9Z5kRkgwY60\/Xk53OtQ8nuRHlpi06n+tuPHz\/+gWnf734AvibBIiFYJASLhGCRECwSgkVCsEgIFgnBIiFYJASLhGCRECwSgkVCsEgIFgnBIiFYJASLhGCRECwSgkVCsEgIFol\/Z4f7\/n0+qZ\/fap8df+W3R9+Cr1y\/8jxnn+HzmrPPtmP2m3gzFgnBIiFYJIZrrE87\/2efrYeOfrtSrxxZqW926q2da3au\/1TUaj9HjsblLydYJASLRFhjfZqqP47GPPrtk8fcqf9W7nXkmh6OZiwSgkVCsEhcVGMVVtaQztY3O+8Wi\/Wnog67hhmLhGCRECwSL66xdkztwZraa9W9s7vLV\/v78BCCRUKwSFxUYxVrMGfrlZ09Xmevv6t+es5alxmLhGCRECwSYY1V1BZn3\/1N7U9\/wn2PPHMN7InPxBcgWCQEi8S356x8rCj6LxxdP3XN0X13+lA8nxmLhGCRECwSF\/XHmtq3VNQoO9\/9Fe8fz65dTfVu0B+LFxAsEoJF4uZ1rKIn59RvV0z1ZZjqrfqcdTIzFgnBIiFYJMIa62l1Q\/E94IqpvV9HY07VcGosXkCwSAgWiZu\/K5x6t3j2Ga7cd1VUscX++llmLBKCRUKwSDyoP1b9rd\/Kfaf2fq2Mf+U7yit7zf8ceXAs+EWwSAgWiRvOK5xalzqrWO+Z6tF19l5nn+H6XXdmLBKCRUKwSFz0XeGRuldn0Vf9bK2zM+aKqbN9ZpmxSAgWCcEi8aD+WE\/oobDyPFPjXNnX9Ij9WLyMYJEQLBLDNdZd5\/Ht7JHf+d7wSNEr9QnfJ64zY5EQLBKCRWL4XeGVe9LrfeifztYodQ\/So+t39oHNMmORECwSgkXi5u8Kp\/oLFL2vztYlU2tCU+db77xj3WfGIiFYJASLxEXvCleu\/3TXu7+6b9aOJ\/QYW2fGIiFYJASLxEXvCo+uKWqUqV5TU+46J\/HKcw\/\/MPLgWPCLYJEQLBIPPUvn7PhT615H19\/ba+p3U98eWsfiZQSLhGCRCPtjTfU7+FTXScW5zvXZiGevv6ZGNGORECwSgkXihj3vZ98hPm1daudbwiv7Wazc1zoWLyNYJASLRLjn\/VPdS3PFlXXb2Weo\/x30x+KLECwSgkXionWsJ+zNmtpHfzRmUSe95ZvHP9zlgnvwFxIsEoJF4ub+WJ\/q3k7F3qaV356tyaae05nQfEGCRUKwSLygP9bK9WcVNUfRT3Xlt1P3nWXGIiFYJASLxM3vCosznu+611SP+J1rPhXnGq0zY5EQLBKCRSLsj1Wo95Xv7GFfUfdl2Kn51Fi8gGCRECwSYX+sKSs9Hab+vOifftZUf3bnFfIFCRYJwSIRvissepDuXL\/Td7TeB1b3Z9\/p6fX\/mLFICBYJwSLxoN4NU+\/Cps4HnFpPmloDq\/tfzDJjkRAsEoJF4kG9G2p39USYOuP57Pj31mRmLBKCRUKwSLysxqrXulau3zkTsPhecuUZrjy35+cdL74ffwnBIiFYJC6qsabWSIp9RVP7tM4+w9l1sqn3m86E5sUEi4RgkQhrrOvXTn6\/70odttMPYuUZVsY5+5x1zTow8uBY8ItgkRAsEi\/rj8VbmLFICBYJwSIhWCQEi4RgkRAsEoJFQrBICBYJwSIhWCQEi4RgkRAsEoJFQrBICBYJwSIhWCQEi4RgkRAsEoJFQrBI\/AfzPNxSbl8SyAAAAABJRU5ErkJggg==';
    }


}
