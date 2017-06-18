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

namespace Rossmitchell\Twofactor\Plugin\Magento\Backend\Controller\Adminhtml\System\Account;

use Magento\Backend\Controller\Adminhtml\System\Account\Save as OriginalClass;
use Rossmitchell\Twofactor\Model\Admin\AdminUser;
use Rossmitchell\Twofactor\Model\Admin\Attribute\IsUsingTwoFactor;

class Save
{
    /**
     * @var AdminUser
     */
    private $adminUser;
    /**
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;

    /**
     * Save constructor.
     *
     * @param AdminUser        $adminUser
     * @param IsUsingTwoFactor $isUsingTwoFactor
     */
    public function __construct(AdminUser $adminUser, IsUsingTwoFactor $isUsingTwoFactor)
    {
        $this->adminUser = $adminUser;
        $this->isUsingTwoFactor = $isUsingTwoFactor;
    }

    public function beforeExecute(OriginalClass $subject)
    {
        $useTwoFactor = $subject->getRequest()->getParam('use_two_factor');

        if (null !== $useTwoFactor) {
            $this->updateAdminUser($useTwoFactor);
        }

        return [];
    }

    private function updateAdminUser($value)
    {
        $adminUser = $this->adminUser->getAdminUser();
        $this->isUsingTwoFactor->setValue($adminUser, $value);
        $this->adminUser->saveAdminUser($adminUser);
    }
}
