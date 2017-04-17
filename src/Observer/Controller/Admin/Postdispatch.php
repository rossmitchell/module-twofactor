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

namespace Rossmitchell\Twofactor\Observer\Controller\Admin;

use Magento\Backend\App\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Rossmitchell\Twofactor\Model\Admin\AdminUser;
use Rossmitchell\Twofactor\Model\Admin\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Admin\Session;
use Rossmitchell\Twofactor\Model\Config\Admin;
use Rossmitchell\Twofactor\Model\TwoFactorUrls;
use Rossmitchell\Twofactor\Model\Verification\IsVerified;

class Postdispatch implements ObserverInterface
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
     * @var Session
     */
    private $session;
    /**
     * @var IsVerified
     */
    private $isVerified;
    /**
     * @var TwoFactorUrls
     */
    private $twoFactorUrls;
    /**
     * @var Admin
     */
    private $adminConfig;

    /**
     * Postdispatch constructor.
     *
     * @param AdminUser        $adminUser
     * @param IsUsingTwoFactor $isUsingTwoFactor
     * @param Session          $session
     * @param IsVerified       $isVerified
     * @param TwoFactorUrls    $twoFactorUrls
     * @param Admin            $adminConfig
     */
    public function __construct(
        AdminUser $adminUser,
        IsUsingTwoFactor $isUsingTwoFactor,
        Session $session,
        IsVerified $isVerified,
        TwoFactorUrls $twoFactorUrls,
        Admin $adminConfig
    ) {
        $this->adminUser        = $adminUser;
        $this->isUsingTwoFactor = $isUsingTwoFactor;
        $this->session          = $session;
        $this->isVerified       = $isVerified;
        $this->twoFactorUrls    = $twoFactorUrls;
        $this->adminConfig      = $adminConfig;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->isTwoFactorEnabled() === false) {
            return;
        }

        if ($this->shouldTheUserBeRedirected() === false) {
            return;
        }

        if ($this->areWeOnANonRedirectingPage() === true) {
            return;
        }

        $controller = $observer->getEvent()->getData('controller_action');
        $this->redirectToAuthenticationPage($controller);
    }

    private function isTwoFactorEnabled()
    {
        return ($this->adminConfig->isTwoFactorEnabled() == true);
    }

    private function shouldTheUserBeRedirected()
    {
        $adminUser = $this->adminUser;
        if ($adminUser->hasAdminUser() === false) {
            return false;
        }
        $user = $this->adminUser->getAdminUser();

        if ($this->isUsingTwoFactor->getValue($user) === false) {
            return false;
        }

        if ($this->isVerified->isVerified($this->session) === true) {
            return false;
        }

        return true;
    }

    private function areWeOnANonRedirectingPage()
    {
        $urls = $this->twoFactorUrls;

        if ($urls->areWeOnTheAuthenticationPage(true) === true) {
            return true;
        }

        if ($urls->areWeOnTheVerificationPage(true) === true) {
            return true;
        }

        return false;
    }

    private function redirectToAuthenticationPage(Action $controller)
    {
        $twoFactorCheckUrl = $this->twoFactorUrls->getAdminAuthenticationUrl();
        $response          = $controller->getResponse();
        $response->setRedirect($twoFactorCheckUrl);
    }
}
