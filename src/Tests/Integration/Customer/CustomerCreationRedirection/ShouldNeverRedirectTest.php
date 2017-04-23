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

namespace Rossmitchell\Twofactor\Tests\Integration\Customer\CustomerCreationRedirection;

use Rossmitchell\Twofactor\Tests\Integration\Abstracts\AbstractTestClass;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\ConfigurationLoader;
use Magento\Framework\Message\MessageInterface;

class ShouldNeverRedirectTest extends AbstractTestClass
{
    use ConfigurationLoader;

    public static function getConfigurationDataPath()
    {
        return __DIR__.'/../_files/two_factor_enabled.php';
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture   loadConfiguration
     */
    public function testCustomerOptsInTwoFactorEnabled()
    {
        $this->setPostParams('optin@example.com','1');
        $this->dispatch('customer/account/createPost');
        $this->assertRedirect($this->stringContains('customer/account/'));
        $this->assertSessionMessages(
            $this->equalTo(['Thank you for registering with Main Website Store.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture   loadConfiguration
     */
    public function testCustomerOptsOutTwoFactorEnabled()
    {
        $this->setPostParams('optout@example.com', '0');
        $this->dispatch('customer/account/createPost');
        $this->assertRedirect($this->stringContains('customer/account/'));
        $this->assertSessionMessages(
            $this->equalTo(['Thank you for registering with Main Website Store.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCustomerOptsInTwoFactorDisabled()
    {
        $this->setPostParams('optin@example.com','1');
        $this->dispatch('customer/account/createPost');
        $this->assertRedirect($this->stringContains('customer/account/'));
        $this->assertSessionMessages(
            $this->equalTo(['Thank you for registering with Main Website Store.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCustomerOptsOutTwoFactorDisabled()
    {
        $this->setPostParams('optout@example.com', '0');
        $this->dispatch('customer/account/createPost');
        $this->assertRedirect($this->stringContains('customer/account/'));
        $this->assertSessionMessages(
            $this->equalTo(['Thank you for registering with Main Website Store.']),
            MessageInterface::TYPE_SUCCESS
        );
    }

    private function setPostParams($email, $useTwoFactor)
    {
        $this->getRequest()
            ->setMethod('POST')
            ->setParam('firstname', 'firstname1')
            ->setParam('lastname', 'lastname1')
            ->setParam('company', '')
            ->setParam('email', $email)
            ->setParam('password', '_Password1')
            ->setParam('password_confirmation', '_Password1')
            ->setParam('telephone', '5123334444')
            ->setParam('street', ['1234 fake street', ''])
            ->setParam('city', 'Austin')
            ->setParam('region_id', 57)
            ->setParam('region', '')
            ->setParam('postcode', '78701')
            ->setParam('country_id', 'US')
            ->setParam('default_billing', '1')
            ->setParam('default_shipping', '1')
            ->setParam('is_subscribed', '0')
            ->setParam('use_two_factor_authentication', $useTwoFactor)
            ->setPostValue('create_address', true);
    }
}
