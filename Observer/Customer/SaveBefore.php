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

namespace Rossmitchell\Twofactor\Observer\Customer;

use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Attribute\TwoFactorSecret;
use Rossmitchell\Twofactor\Model\Customer\Session;
use Rossmitchell\Twofactor\Model\Verification\IsVerified;
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\Secret;

class SaveBefore implements ObserverInterface
{
    /**
     * @var Secret
     */
    private $secret;
    /**
     * @var IsVerified
     */
    private $isVerified;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var TwoFactorSecret
     */
    private $twoFactorSecret;
    /**
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;

    /**
     * SaveBefore constructor.
     *
     * @param Secret           $secret
     * @param IsVerified       $isVerified
     * @param Session          $session
     * @param TwoFactorSecret  $twoFactorSecret
     * @param IsUsingTwoFactor $isUsingTwoFactor
     */
    public function __construct(
        Secret $secret,
        IsVerified $isVerified,
        Session $session,
        TwoFactorSecret $twoFactorSecret,
        IsUsingTwoFactor $isUsingTwoFactor
    ) {
        $this->secret          = $secret;
        $this->isVerified      = $isVerified;
        $this->session         = $session;
        $this->twoFactorSecret = $twoFactorSecret;
        $this->isUsingTwoFactor = $isUsingTwoFactor;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Customer $customer */
        $customer = $observer->getEvent()->getCustomer();
        if ($this->needsToUpdate($customer) === false) {
            return;
        }

        $this->generateSecretForCustomer($customer);
        $this->markCustomerAsVerified();
    }

    private function needsToUpdate(Customer $customer)
    {
        $useTwoFactor = $this->isUsingTwoFactor->getValue($customer);
        $hasSecret    = $this->twoFactorSecret->getValue($customer);
        /* If the use two factor attribute is not set then there is no need to do anything */
        if ($useTwoFactor === false) {
            return false;
        }

        /* If there is no secret set, then we need to update  */
        if (empty($hasSecret)) {
            return true;
        }

        /* If we reach this point the use_two_factor attribute is set to true, and a secret has previously been saved */
        return false;
    }

    private function generateSecretForCustomer(Customer $customer)
    {
        $secret = $this->secret->generateSecret();
        $this->twoFactorSecret->setValue($customer, $secret);
    }

    private function markCustomerAsVerified()
    {
        $this->isVerified->setIsVerified($this->session);
    }
}
