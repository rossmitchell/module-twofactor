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

namespace Rossmitchell\Twofactor\Block\Customer\Account\Edit;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Rossmitchell\Twofactor\Model\Config\Customer as CustomerConfig;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Customer;

class UseTwoFactor extends Template
{
    /**
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;
    /**
     * @var Customer
     */
    private $customerGetter;
    /**
     * @var CustomerConfig
     */
    private $customerConfig;

    /**
     * UseTwoFactor constructor.
     *
     * @param Context          $context
     * @param IsUsingTwoFactor $isUsingTwoFactor
     * @param Customer         $customerGetter
     * @param CustomerConfig   $customerConfig
     * @param array            $data
     */
    public function __construct(
        Context $context,
        IsUsingTwoFactor $isUsingTwoFactor,
        Customer $customerGetter,
        CustomerConfig $customerConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->isUsingTwoFactor = $isUsingTwoFactor;
        $this->customerGetter   = $customerGetter;
        $this->customerConfig   = $customerConfig;
    }

    /**
     * We only want to display the block if two factor has been enabled in the admin
     *
     * @return bool
     */
    public function shouldBlockBeDisplayed()
    {
        return ($this->customerConfig->isTwoFactorEnabled() === true);
    }

    /**
     * Used to get the customer, return false if no customer is present in the session
     *
     * @return CustomerInterface|false
     */
    public function getCustomer()
    {
        return $this->customerGetter->getCustomer();
    }

    /**
     * Checks to see if the customer is using two factor authentication.
     *
     * This first checks if the customer has a value set for the attributes, and if so if the value is true. If both of
     * these checks pass the method returns true, otherwise it returns false
     *
     * @param CustomerInterface $customer
     *
     * @return bool
     */
    public function isUsingTwoFactor(CustomerInterface $customer)
    {
        if ($this->isUsingTwoFactor->hasValue($customer) === false) {
            return false;
        }

        return ($this->isUsingTwoFactor->getValue($customer) === true);
    }

    /**
     * If the customer is using two factor authentication then this will return the selected snippet for use in the
     * options tag, if not it will return an empty string
     *
     * @param CustomerInterface $customer
     *
     * @return string
     */
    public function getSelectedForYes(CustomerInterface $customer)
    {
        return $this->getSelectedSnippet($customer, true);
    }

    /**
     * If the customer is not using two factor authentication then this will return the selected snippet for use in the
     * options tag, if they are it will return an empty string
     *
     * @param CustomerInterface $customer
     *
     * @return string
     */
    public function getSelectedForNo(CustomerInterface $customer)
    {
        return $this->getSelectedSnippet($customer, false);
    }

    /**
     * Generates the snippet for the two getSelected methods
     *
     * @param CustomerInterface $customer
     * @param boolean           $condition
     *
     * @return string
     */
    private function getSelectedSnippet(CustomerInterface $customer, $condition)
    {
        $html = '';
        if ($this->isUsingTwoFactor($customer) === $condition) {
            $html = ' selected="selected"';
        }

        return $html;
    }
}
