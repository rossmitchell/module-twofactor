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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use Rossmitchell\Twofactor\Model\Admin\AdminUser;
use Rossmitchell\Twofactor\Model\Admin\Attribute\TwoFactorSecret;
use Rossmitchell\Twofactor\Model\Admin\Session;
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\Verify as GoogleVerify;
use Rossmitchell\Twofactor\Model\Urls\Fetcher;
use Rossmitchell\Twofactor\Model\Verification\IsVerified;
use Rossmitchell\Twofactor\Model\Config\Admin as UserAdmin;
use Rossmitchell\Twofactor\Model\Admin\Attribute\IsUsingTwoFactor;

class Verify extends AbstractController
{
    /**
     * @var TwoFactorSecret
     */
    private $twoFactorSecret;
    /**
     * @var GoogleVerify
     */
    private $verify;
    /**
     * @var IsVerified
     */
    private $isVerified;
    /**
     * @var Session
     */
    private $adminSession;
    /**
     * @var Fetcher
     */
    private $fetcher;

    /**
     * Verify constructor.
     *
     * @param Context          $context
     * @param UserAdmin        $userAdmin
     * @param AdminUser        $adminGetter
     * @param Fetcher          $fetcher
     * @param IsUsingTwoFactor $isUsingTwoFactor
     * @param TwoFactorSecret  $twoFactorSecret
     * @param GoogleVerify     $verify
     * @param IsVerified       $isVerified
     * @param Session          $adminSession
     * @param Fetcher          $fetcher
     */
    public function __construct(
        Context $context,
        UserAdmin $userAdmin,
        AdminUser $adminGetter,
        IsUsingTwoFactor $isUsingTwoFactor,
        TwoFactorSecret $twoFactorSecret,
        GoogleVerify $verify,
        IsVerified $isVerified,
        Session $adminSession,
        Fetcher $fetcher
    ) {
        parent::__construct($context, $userAdmin, $adminGetter, $fetcher, $isUsingTwoFactor);
        $this->twoFactorSecret = $twoFactorSecret;
        $this->verify          = $verify;
        $this->isVerified      = $isVerified;
        $this->adminSession    = $adminSession;
        $this->fetcher         = $fetcher;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if ($this->shouldActionBeRun() === false) {
            return $this->getRedirectAction();
        }

        $secret    = $this->getRequest()->getParam('secret');
        $adminUser = $this->getAdminUser();

        $verificationPassed = $this->verifySecret($adminUser, $secret);

        if ($verificationPassed === false) {
            return $this->handleError();
        }

        return $this->handleSuccess();
    }

    private function verifySecret($adminUser, $postedSecret)
    {
        $customerSecret = $this->twoFactorSecret->getValue($adminUser);
        try {
            $verified = $this->verify->verify($customerSecret, $postedSecret);
        } catch (InvalidCharactersException $exception) {
            $verified = false;
        }

        return $verified;
    }

    private function handleError()
    {
        $this->isVerified->removeIsVerified($this->adminSession);
        $this->addErrorMessage();
        $authenticateUrl = $this->fetcher->getAuthenticationUrl(true);

        return $this->redirect($authenticateUrl);
    }

    private function addErrorMessage()
    {
        $this->messageManager->addErrorMessage("Two Factor Code was incorrect");
    }

    private function handleSuccess()
    {
        $this->isVerified->setIsVerified($this->adminSession);
        $accountUrl = $this->fetcher->getAdminDashboardUrl();

        return $this->redirect($accountUrl);
    }
}
