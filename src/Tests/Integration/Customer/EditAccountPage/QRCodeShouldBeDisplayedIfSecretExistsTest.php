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

namespace Rossmitchell\Twofactor\Tests\Integration\Customer\EditAccountPage;

use Rossmitchell\Twofactor\Tests\Integration\Abstracts\AbstractTestClass;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\ConfigurationLoader;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\CustomerLoader;
class QRCodeShouldBeDisplayedIfSecretExistsTest extends AbstractTestClass
{
    use ConfigurationLoader;
    use CustomerLoader;

    public static function getCustomerDataPath()
    {
        return __DIR__.'/../_files/customer.php';
    }

    public static function getConfigurationDataPath()
    {
        return __DIR__.'/../_files/two_factor_enabled.php';
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadCustomer
     * @magentoDataFixture loadConfiguration
     */
    public function testUseTwoFactorIsDisplayed()
    {
        $this->login('not_enabled_but_has_secret@example.com');
        $this->dispatch('/customer/account/edit');
        $body = $this->getResponse()->getBody();
        $this->assertContains('Use Two Factor Authentication', $body);
        $this->assertContains('Please scan the image below to get your authentication code', $body);
    }
}
