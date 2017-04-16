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
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use Rossmitchell\Twofactor\Model\Customer\Attribute\TwoFactorSecret;
use Rossmitchell\Twofactor\Model\Customer\Getter;
use Rossmitchell\Twofactor\Model\Customer\IsVerified;
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\Verify as GoogleVerify;
use Rossmitchell\Twofactor\Model\TwoFactorUrls;

class Verify extends Action
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
     * @var Getter
     */
    private $customerGetter;
    /**
     * @var TwoFactorUrls
     */
    private $twoFactorUrls;
    /**
     * @var IsVerified
     */
    private $isVerified;

    /**
     * Constructor
     *
     * @param Context         $context
     * @param Getter          $customerGetter
     * @param TwoFactorSecret $secret
     * @param GoogleVerify    $verify
     * @param TwoFactorUrls   $twoFactorUrls
     * @param IsVerified      $isVerified
     */
    public function __construct(
        Context $context,
        Getter $customerGetter,
        TwoFactorSecret $secret,
        GoogleVerify $verify,
        TwoFactorUrls $twoFactorUrls,
        IsVerified $isVerified
    ) {
        parent::__construct($context);
        $this->secret         = $secret;
        $this->verify         = $verify;
        $this->customerGetter = $customerGetter;
        $this->twoFactorUrls  = $twoFactorUrls;
        $this->isVerified     = $isVerified;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $secret             = $this->getRequest()->getParam('secret');
        $customer           = $this->customerGetter->getCustomer();
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
        $this->isVerified->setCustomerIsVerified();
        $this->addSuccessMessage();
        $accountUrl = $this->twoFactorUrls->getCustomerAccountUrl();
        return $this->redirect($accountUrl);
    }

    private function handleError()
    {
        $this->isVerified->removeCustomerIsVerified();
        $this->addErrorMessage();
        $authenticateUrl = $this->twoFactorUrls->getCustomerAuthenticationUrl();
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

    private function redirect($path)
    {
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath($path);

        return $redirect;
    }
}
