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

namespace Rossmitchell\Twofactor\Controller\Adminhtml\Adminlogin;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Rossmitchell\Twofactor\Model\Config\Admin as UserAdmin;
use Rossmitchell\Twofactor\Model\Admin\AdminUser;
use Rossmitchell\Twofactor\Model\Urls\Fetcher;
use Rossmitchell\Twofactor\Model\Admin\Attribute\IsUsingTwoFactor;

abstract class AbstractController extends Action
{
    /**
     * @var UserAdmin
     */
    private $userAdmin;
    /**
     * @var AdminUser
     */
    private $adminGetter;
    /**
     * @var Fetcher
     */
    private $fetcher;
    /**
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;

    private $redirectAction;

    private $adminModel;

    /**
     * @param Context          $context
     * @param UserAdmin        $userAdmin
     * @param AdminUser        $adminGetter
     * @param Fetcher          $fetcher
     * @param IsUsingTwoFactor $isUsingTwoFactor
     */
    public function __construct(
        Context $context,
        UserAdmin $userAdmin,
        AdminUser $adminGetter,
        Fetcher $fetcher,
        IsUsingTwoFactor $isUsingTwoFactor
    ) {
        parent::__construct($context);
        $this->userAdmin        = $userAdmin;
        $this->adminGetter      = $adminGetter;
        $this->fetcher          = $fetcher;
        $this->isUsingTwoFactor = $isUsingTwoFactor;
    }

    public function shouldActionBeRun()
    {
        if ($this->isEnabled() === false) {
            $this->redirectAction = $this->redirectToDashboard();

            return false;
        }

        if ($this->getAdminUser() === false) {
            $this->redirectAction = $this->handleMissingUser();

            return false;
        }

        if ($this->isUserUsingTwoFactor() === false) {
            $this->redirectAction = $this->redirectToDashboard();

            return false;
        }

        return true;
    }

    private function isEnabled()
    {
        return ($this->userAdmin->isTwoFactorEnabled() == true);
    }

    public function getAdminUser()
    {
        if (null === $this->adminModel) {
            try {
                $this->adminModel = $this->adminGetter->getAdminUser();
            } catch (\Exception $e) {
                $this->adminModel = false;
            }
        }

        return $this->adminModel;
    }

    private function isUserUsingTwoFactor()
    {
        $user = $this->getAdminUser();

        return $this->isUsingTwoFactor->getValue($user);
    }

    private function redirectToDashboard()
    {
        $url = $this->fetcher->getAdminDashboardUrl();

        return $this->redirect($url);
    }

    private function handleMissingUser()
    {
        $url = $this->fetcher->getAdminLogInUrl();

        return $this->redirect($url);
    }

    public function redirect($path)
    {
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath($path);

        return $redirect;
    }

    public function getRedirectAction()
    {
        return $this->redirectAction;
    }
}
