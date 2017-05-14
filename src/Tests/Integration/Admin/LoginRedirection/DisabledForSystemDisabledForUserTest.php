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

namespace Rossmitchell\Twofactor\Tests\Integration\Admin\LoginRedirection;

use Rossmitchell\Twofactor\Model\Admin\Session;
use Rossmitchell\Twofactor\Tests\Integration\Abstracts\AbstractTestClass;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\AdminUserLoader;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\ConfigurationLoader;

class DisabledForSystemDisabledForUserTest extends AbstractTestClass
{
    use AdminUserLoader;
    use ConfigurationLoader;

    public static function getAdminUserDataPath()
    {
        return __DIR__ . '/../_files/adminUser.php';
    }

    public static function getConfigurationDataPath()
    {
        return __DIR__ . '/../_files/two_factor_disabled.php';
    }

    /**
     * @magentoDbIsolation   enabled
     * @magentoAppIsolation  enabled
     * @magentoDataFixture   loadConfiguration
     * @magentoDataFixture   loadAdminUsers
     */
    public function testNoRedirectionToVerification()
    {

        $this->getRequest()->setMethod('POST')->setPostValue(
            [
                'form_key' => $this->getFormKey(),
                'login'    => [
                    'username' => 'two_factor_disabled',
                    'password' => 'password123',
                ],
            ]
        );
        $this->dispatch('/backend/admin/index/index');
        $this->assertTrue($this->getResponse()->isRedirect());

        $adminSession = $this->getAdminSession();
        $this->assertTrue($adminSession->isLoggedIn());

        $redirect = $this->getResponse()->getHeader('Location');
        $this->assertFalse(strpos($redirect, 'twofactor/adminlogin/index'));
    }
}
