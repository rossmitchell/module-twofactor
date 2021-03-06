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

namespace Rossmitchell\Twofactor\Controller\Customerlogin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use Rossmitchell\Twofactor\Model\Config\Customer as CustomerAdmin;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Attribute\TwoFactorSecret;
use Rossmitchell\Twofactor\Model\Customer\Customer;
use Rossmitchell\Twofactor\Model\Customer\Session;
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\Verify as GoogleVerify;
use Rossmitchell\Twofactor\Model\Urls\Fetcher;
use Rossmitchell\Twofactor\Model\Verification\IsVerified;

class Verify extends AbstractController
{

    /**
     * @var TwoFactorSecret
     */
    private $secret;
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
    private $customerSession;

    /**
     * Constructor
     *
     * @param Context          $context
     * @param Customer         $customerGetter
     * @param TwoFactorSecret  $secret
     * @param GoogleVerify     $verify
     * @param Fetcher          $fetcher
     * @param IsVerified       $isVerified
     * @param Session          $customerSession
     * @param CustomerAdmin    $customerAdmin
     * @param IsUsingTwoFactor $isUsingTwoFactor
     */
    public function __construct(
        Context $context,
        Customer $customerGetter,
        TwoFactorSecret $secret,
        GoogleVerify $verify,
        Fetcher $fetcher,
        IsVerified $isVerified,
        Session $customerSession,
        CustomerAdmin $customerAdmin,
        IsUsingTwoFactor $isUsingTwoFactor
    ) {
        parent::__construct($context, $customerAdmin, $customerGetter, $fetcher, $isUsingTwoFactor);
        $this->secret          = $secret;
        $this->verify          = $verify;
        $this->isVerified      = $isVerified;
        $this->customerSession = $customerSession;
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

        $secret   = $this->getRequest()->getParam('secret');
        $customer = $this->getCustomer();
        if ($customer === false) {
            return $this->handleError();
        }
        $verificationPassed = $this->verifySecret($customer, $secret);

        if ($verificationPassed === false) {
            return $this->handleError();
        }

        return $this->handleSuccess();
    }

    private function verifySecret(CustomerInterface $customer, $postedSecret)
    {
        $customerSecret = $this->secret->getValue($customer);
        try {
            $verified = $this->verify->verify($customerSecret, $postedSecret);
        } catch (InvalidCharactersException $exception) {
            $verified = false;
        }

        return $verified;
    }

    private function handleSuccess()
    {
        $this->isVerified->setIsVerified($this->customerSession);
        $this->addSuccessMessage();
        $accountUrl = $this->getUrlFetcher()->getCustomerAccountUrl();

        return $this->redirect($accountUrl);
    }

    private function handleError()
    {
        $this->isVerified->removeIsVerified($this->customerSession);
        $this->addErrorMessage();
        $authenticateUrl = $this->getUrlFetcher()->getAuthenticationUrl();

        return $this->redirect($authenticateUrl);
    }

    private function addErrorMessage()
    {
        $this->messageManager->addErrorMessage("Two Factor Code was incorrect");
    }

    private function addSuccessMessage()
    {
        $this->messageManager->addSuccessMessage("Two Factor Code was correct");
    }
}
