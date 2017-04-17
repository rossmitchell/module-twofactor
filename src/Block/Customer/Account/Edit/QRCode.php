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
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\QRCode as QRCodeGenerator;

class QRCode extends Template
{
    /**
     * @var QRCodeGenerator
     */
    private $qRCode;
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
     * QRCode constructor.
     *
     * @param Template\Context $context
     * @param QRCodeGenerator  $qRCode
     * @param TwoFactorSecret  $twoFactorSecret
     * @param Customer         $customerGetter
     * @param CustomerConfig   $customerConfig
     *
     * @internal param array $data
     */
    public function __construct(
        Template\Context $context,
        QRCodeGenerator $qRCode,
        TwoFactorSecret $twoFactorSecret,
        Customer $customerGetter,
        CustomerConfig $customerConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->qRCode          = $qRCode;
        $this->twoFactorSecret = $twoFactorSecret;
        $this->customerGetter  = $customerGetter;
        $this->customerConfig  = $customerConfig;
    }

    public function getCustomer()
    {
        return $this->customerGetter->getCustomer();
    }

    public function hasAQrCode(CustomerInterface $customer)
    {
        if ($this->customerConfig->isTwoFactorEnabled() == false) {
            return false;
        }

        return $this->twoFactorSecret->hasValue($customer);
    }

    public function getQrCode(CustomerInterface $customer)
    {
        if ($this->twoFactorSecret->hasValue($customer) === false) {
            throw new \Exception("Could not load the QR code because the customer does not have a secret");
        }

        $secret = $this->twoFactorSecret->hasValue($customer);
        $email  = $customer->getEmail();
        $qrCode = $this->qRCode->generateQRCode('Test', $email, $secret);

        return $qrCode;
    }
}
