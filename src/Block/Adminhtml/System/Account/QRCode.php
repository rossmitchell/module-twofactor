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

namespace Rossmitchell\Twofactor\Block\Adminhtml\System\Account;

use Magento\Framework\View\Element\Template;
use Magento\User\Model\User;
use Rossmitchell\Twofactor\Model\Admin\AdminUser;
use Rossmitchell\Twofactor\Model\Admin\Attribute\TwoFactorSecret;
use Rossmitchell\Twofactor\Model\Config\Admin;
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\QRCode as GoogleQRCode;

class QRCode extends Template
{
    /**
     * @var AdminUser
     */
    private $adminUser;
    /**
     * @var TwoFactorSecret
     */
    private $twoFactorSecret;
    /**
     * @var GoogleQRCode
     */
    private $qRCode;
    /**
     * @var Admin
     */
    private $adminConfig;

    /**
     * QRCode constructor.
     *
     * @param Template\Context $context
     * @param AdminUser        $adminUser
     * @param TwoFactorSecret  $twoFactorSecret
     * @param GoogleQRCode     $qRCode
     * @param Admin            $adminConfig
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        AdminUser $adminUser,
        TwoFactorSecret $twoFactorSecret,
        GoogleQRCode $qRCode,
        Admin $adminConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->adminUser       = $adminUser;
        $this->twoFactorSecret = $twoFactorSecret;
        $this->qRCode          = $qRCode;
        $this->adminConfig     = $adminConfig;
    }

    public function getAdminUser()
    {
        return $this->adminUser->getAdminUser();
    }

    public function shouldQRCodeBeDisplayed(User $adminUser)
    {
        if ($this->adminConfig->isTwoFactorEnabled() == false) {
            return false;
        }

        if ($this->getSecret($adminUser) === null) {
            return false;
        }

        return true;
    }

    public function getQRCode(User $adminUser)
    {
        $secret = $this->getSecret($adminUser);

        return $this->qRCode->generateQRCode('Test Company', $adminUser->getEmail(), $secret);
    }

    private function getSecret(User $adminUser)
    {
        return $this->twoFactorSecret->getValue($adminUser);
    }
}
