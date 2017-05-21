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
use Rossmitchell\Twofactor\Model\Config\Customer as CustomerConfig;
use Rossmitchell\Twofactor\Model\Customer\Attribute\TwoFactorSecret;
use Rossmitchell\Twofactor\Model\Customer\Customer;
use Rossmitchell\Twofactor\Model\Customer\GetQrCode;

class QRCode extends Template
{
    /**
     * @var TwoFactorSecret
     */
    private $twoFactorSecret;
    /**
     * @var Customer
     */
    private $customerGetter;
    /**
     * @var CustomerConfig
     */
    private $customerConfig;
    /**
     * @var GetQrCode
     */
    private $getQrCode;

    /**
     * QRCode constructor.
     *
     * @param Template\Context $context
     * @param TwoFactorSecret  $twoFactorSecret
     * @param Customer         $customerGetter
     * @param CustomerConfig   $customerConfig
     * @param GetQrCode        $getQrCode
     * @param array            $data
     *
     * @internal param array $data
     */
    public function __construct(
        Template\Context $context,
        TwoFactorSecret $twoFactorSecret,
        Customer $customerGetter,
        CustomerConfig $customerConfig,
        GetQrCode $getQrCode,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->twoFactorSecret = $twoFactorSecret;
        $this->customerGetter  = $customerGetter;
        $this->customerConfig  = $customerConfig;
        $this->getQrCode = $getQrCode;
    }

    /**
     * A simple getter method to return the current customer
     *
     * @return CustomerInterface|false
     */
    public function getCustomer()
    {
        return $this->customerGetter->getCustomer();
    }

    /**
     * Used to check if the code should be displayed - will return false if two factor is disabled in the config, or if
     * the customer does not have a code. Otherwise returns true
     *
     * @param CustomerInterface $customer
     *
     * @return bool
     */
    public function shouldQrCodeBeDisplayed(CustomerInterface $customer)
    {
        if ($this->customerConfig->isTwoFactorEnabled() === false) {
            return false;
        }

        if ($this->twoFactorSecret->hasValue($customer) === false) {
            return false;
        }

        return true;
    }

    /**
     * Used to get the customers QR Code. Will return false if they don't have one, otherwise will return the image
     * string
     *
     * @param CustomerInterface $customer
     *
     * @return bool|string
     */
    public function getQrCode(CustomerInterface $customer)
    {
        return $this->getQrCode->getQrCode($customer);
    }
}
