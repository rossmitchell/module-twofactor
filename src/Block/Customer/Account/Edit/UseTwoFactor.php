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
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Getter;
use Rossmitchell\Twofactor\Model\Customer\UsingTwoFactor;

class UseTwoFactor extends Template
{
    /**
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;
    /**
     * @var Getter
     */
    private $customerGetter;

    /**
     * UseTwoFactor constructor.
     *
     * @param Context          $context
     * @param IsUsingTwoFactor $isUsingTwoFactor
     * @param Getter           $customerGetter
     * @param array            $data
     */
    public function __construct(Context $context, IsUsingTwoFactor $isUsingTwoFactor, Getter $customerGetter, array $data = [])
    {
        parent::__construct($context, $data);
        $this->isUsingTwoFactor = $isUsingTwoFactor;
        $this->customerGetter = $customerGetter;
    }

    public function getCustomer()
    {
        return $this->customerGetter->getCustomer();
    }

    public function isUsingTwoFactor(CustomerInterface $customer)
    {
        if ($this->isUsingTwoFactor->hasValue($customer) === false) {
            return false;
        }

        return ($this->isUsingTwoFactor->getValue($customer) == true);
    }

    public function getSelectedForYes(CustomerInterface $customer)
    {
        return $this->getSelectedSnippet($customer,true);
    }

    public function getSelectedForNo(CustomerInterface $customer)
    {
        return $this->getSelectedSnippet($customer,false);
    }

    private function getSelectedSnippet(CustomerInterface $customer, $condition)
    {
        $html = '';
        if ($this->isUsingTwoFactor($customer) === $condition) {
            $html = ' selected="selected"';
        }

        return $html;
    }
}
