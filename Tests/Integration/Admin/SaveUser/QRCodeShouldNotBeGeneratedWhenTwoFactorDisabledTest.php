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

namespace Rossmitchell\Twofactor\Tests\Integration\Admin\EnterCodePage;

use Magento\User\Model\User;
use Rossmitchell\Twofactor\Tests\Integration\Abstracts\AbstractTestClass;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\AdminUserLoader;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\ConfigurationLoader;

class QRCodeShouldNotBeGeneratedWhenTwoFactorDisabledTest extends AbstractTestClass
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
     * @magentoDataFixture   loadAdminUsers
     * @magentoDataFixture   loadConfiguration
     */
    public function testQrCodeOnSave()
    {
        $this->disableSecretKeys();
        $this->loginAdmin('two_factor_disabled', 'password123');
        $this->setPostParams(0, 'password123');
        $this->dispatch('/backend/admin/system_account/save');
        /** @var User $user */
        $user = $this->createObject(User::class);
        $user->loadByUsername('two_factor_disabled');
        $this->assertEmpty($user->getData('two_factor_secret'));
    }

    private function setPostParams($useTwoFactor, $password)
    {
        $adminUser = $this->getAdminSession()->getUser();

        $this->getRequest()
            ->setMethod('POST')
            ->setParam('form_key', $this->getFormKey())
            ->setParam('username', $adminUser->getUserName())
            ->setParam('firstname', $adminUser->getFirstName())
            ->setParam('lastname', $adminUser->getLastName())
            ->setParam('user_id', $adminUser->getId())
            ->setParam('email', $adminUser->getEmail())
            ->setParam('password', '')
            ->setParam('password_confirmation', '')
            ->setParam('interface_locale', $adminUser->getInterfaceLocale())
            ->setParam('use_two_factor', "$useTwoFactor")
            ->setParam('current_password', $password);
    }
}
