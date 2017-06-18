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

namespace Rossmitchell\Twofactor\Observer\AdminUser;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\User\Model\User;
use Rossmitchell\Twofactor\Model\Admin\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Admin\Attribute\TwoFactorSecret;
use Rossmitchell\Twofactor\Model\Admin\Session;
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\Secret;
use Rossmitchell\Twofactor\Model\Verification\IsVerified;

class SaveBefore implements ObserverInterface
{
    /**
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;
    /**
     * @var TwoFactorSecret
     */
    private $twoFactorSecret;
    /**
     * @var Secret
     */
    private $secret;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var IsVerified
     */
    private $isVerified;

    /**
     * SaveBefore constructor.
     *
     * @param IsUsingTwoFactor $isUsingTwoFactor
     * @param TwoFactorSecret  $twoFactorSecret
     * @param Secret           $secret
     * @param Session          $session
     * @param IsVerified       $isVerified
     */
    public function __construct(
        IsUsingTwoFactor $isUsingTwoFactor,
        TwoFactorSecret $twoFactorSecret,
        Secret $secret,
        Session $session,
        IsVerified $isVerified
    ) {
        $this->isUsingTwoFactor = $isUsingTwoFactor;
        $this->twoFactorSecret  = $twoFactorSecret;
        $this->secret           = $secret;
        $this->session          = $session;
        $this->isVerified       = $isVerified;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $adminUser = $observer->getEvent()->getData('data_object');
        if ($this->isSetToUseTwoFactor($adminUser) === false) {
            return;
        }

        if ($this->alreadyHasASecret($adminUser) === true) {
            return;
        }

        $this->addNewSecret($adminUser);
        $this->markAsValidated();
    }

    private function isSetToUseTwoFactor(User $user)
    {
        return ($this->isUsingTwoFactor->getValue($user) === true);
    }

    private function alreadyHasASecret(User $user)
    {
        return ($this->twoFactorSecret->getValue($user) !== null);
    }

    private function addNewSecret(User $user)
    {
        $secret = $this->secret->generateSecret();
        $this->twoFactorSecret->setValue($user, $secret);
    }

    private function markAsValidated()
    {
        $this->isVerified->setIsVerified($this->session);
    }
}
