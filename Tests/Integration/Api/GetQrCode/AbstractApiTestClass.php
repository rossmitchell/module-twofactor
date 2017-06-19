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

use Rossmitchell\Twofactor\Tests\Integration\Abstracts\AbstractTestClass;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\ConfigurationLoader;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\CustomerLoader;

/**
 * Class AbstractApiTestClass
 *
 * It is an unfortunate fact that the API tests do not provide coverage information, meaning that they have to be
 * duplicated to gather this. This is a first attempt at allowing the test cases to be as similar as possible - although
 * I will almost certainly come back to this and make improvements in the future.
 *
 * @date 20/06/2020 - No I haven't
 *
 * @package Rossmitchell\Twofactor\Tests\Integration\Api\GetQrCode
 */
abstract class AbstractApiTestClass extends AbstractTestClass
{

    use ConfigurationLoader;
    use CustomerLoader;

    public static function getCustomerDataPath()
    {
        return __DIR__.'/../../Customer/_files/customer.php';
    }

    public static function getConfigurationDataPath()
    {
        return __DIR__.'/../../Customer/_files/two_factor_enabled.php';
    }


    public function makeRequest()
    {
        ob_start();
        $this->dispatch('/rest/V1/twofactor/getqrcode');
        $rawResult = ob_get_clean();

        $result = $this->stripQrCodeFromResult($rawResult);

        return $result;
    }

    public function makeRequestThatShouldFail()
    {
        ob_start();
        $this->dispatch('/rest/V1/twofactor/getqrcode');
        $rawResult = ob_get_clean();

        $result = $this->stripTraceFromResult($rawResult);

        return $result;
    }

    private function stripQrCodeFromResult($result)
    {
        $replaceWith = $this->getQrCodeForEnabledCustomer();;

        return $this->stripElementFromResult($result, 'qr_code', $replaceWith);
    }

    private function stripTraceFromResult($result)
    {
        return $this->stripElementFromResult($result, 'trace');
    }

    private function stripElementFromResult($result, $element, $replace = null)
    {
        if (!is_object($result)) {
            $result = json_decode($result);
        }

        if (is_object($result) && property_exists($result, $element)) {
            if ($replace === null) {
                unset($result->$element);
            } else {
                $result->$element = $replace;
            }
        }

        $result = json_encode($result);

        return $result;
    }

    public function getQrCodeForEnabledCustomer()
    {
        return 'A valid QR Code';
    }
}
