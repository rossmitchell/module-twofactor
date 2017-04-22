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

namespace Rossmitchell\Twofactor\Tests\Integration\Customer;

use Magento\TestFramework\TestCase\AbstractController;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\ConfigurationLoader;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\CustomerLoader;

class LoginEverythingEnabledTest extends AbstractController
{

    use CustomerLoader;
    use ConfigurationLoader;

    static function getCustomerData()
    {
        require __DIR__.'/_files/customer.php';
        if (!isset($customerData)) {
            throw new \Exception("No Customer Data has been set");
        }

        return $customerData;
    }

    static function getConfigurationData()
    {
        require __DIR__.'/_files/two_factor_enabled.php';
        if(!isset($configurationData)) {
            throw new \Exception('No Configuration data has been set');
        }

        return $configurationData;
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoConfigFixture default/two_factor_customers/details/enable 1
     * @magentoDataFixture loadCustomer
     * @magentoDataFixture loadConfiguration
     */
    public function testLoader()
    {
        $this->getRequest()->setMethod('POST')->setPostValue(
            [
                'form_key' => $this->getFormKey(),
                'login' => [
                    'username' => 'customer@example.com',
                    'password' => 'password',
                ],
            ]
        );
        $this->dispatch('customer/account/loginPost');

        $this->assertEquals('', $this->getResponse()->getBody());

        $this->assertRedirect($this->stringContains('twofactor/customerlogin/index'));
    }

    public function getFormKey()
    {
        return $this->_objectManager->get('Magento\Framework\Data\Form\FormKey')->getFormKey();
    }
}
