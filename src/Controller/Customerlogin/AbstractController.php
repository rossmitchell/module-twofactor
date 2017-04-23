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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Rossmitchell\Twofactor\Model\Config\Customer as CustomerAdmin;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Customer;
use Rossmitchell\Twofactor\Model\TwoFactorUrls;

abstract class AbstractController extends Action
{

    /**
     * @var CustomerAdmin
     */
    private $customerAdmin;
    /**
     * @var Customer
     */
    private $customerGetter;
    /**
     * @var TwoFactorUrls
     */
    private $twoFactorUrls;

    private $redirectAction;

    private $customerModel;
    /**
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;

    public function __construct(
        Context $context,
        CustomerAdmin $customerAdmin,
        Customer $customerGetter,
        TwoFactorUrls $twoFactorUrls,
        IsUsingTwoFactor $isUsingTwoFactor
    ) {
        parent::__construct($context);
        $this->customerAdmin  = $customerAdmin;
        $this->customerGetter = $customerGetter;
        $this->twoFactorUrls  = $twoFactorUrls;
        $this->isUsingTwoFactor = $isUsingTwoFactor;
    }

    public function shouldActionBeRun()
    {
        if ($this->isEnabled() === false) {
            $this->redirectAction = $this->handleDisabled();

            return false;
        }

        if ($this->getCustomer() === false) {
            $this->redirectAction = $this->handleMissingCustomer();

            return false;
        }

        if ($this->isCustomerUsingTwoFactor() === false) {
            $this->redirectAction = $this->handleNonOptInCustomer();

            return false;
        }

        return true;
    }

    public function getRedirectAction()
    {
        return $this->redirectAction;
    }

    private function isEnabled()
    {
        return ($this->customerAdmin->isTwoFactorEnabled() == true);
    }

    private function handleDisabled()
    {
        return $this->redirect('/');
    }

    public function getCustomer()
    {
        if (null === $this->customerModel) {
            $this->customerModel = $this->customerGetter->getCustomer();
        }

        return $this->customerModel;
    }

    private function handleMissingCustomer()
    {
        $loginUrl = $this->twoFactorUrls->getCustomerLogInUrl();

        return $this->redirect($loginUrl);
    }

    private function isCustomerUsingTwoFactor()
    {
        $customer = $this->getCustomer();

        return $this->isUsingTwoFactor->getValue($customer);
    }

    private function handleNonOptInCustomer()
    {
        return $this->redirect('/');
    }

    public function redirect($path)
    {
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath($path);

        return $redirect;
    }
}
