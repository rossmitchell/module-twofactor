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

namespace Rossmitchell\Twofactor\Model\Customer;

use Rossmitchell\Twofactor\Model\GoogleTwoFactor\QRCode as QRCodeGenerator;
use Rossmitchell\Twofactor\Model\Customer\Attribute\TwoFactorSecret;
use Rossmitchell\Twofactor\Model\Config\Customer as CustomerConfig;
use Magento\Customer\Api\Data\CustomerInterface;

class GetQrCode
{
    /**
     * @var TwoFactorSecret
     */
    private $twoFactorSecret;
    /**
     * @var CustomerConfig
     */
    private $customerConfig;
    /**
     * @var QRCodeGenerator
     */
    private $qRCode;

    /**
     * GetQrCode constructor.
     *
     * @param TwoFactorSecret $twoFactorSecret
     * @param CustomerConfig  $customerConfig
     * @param QRCodeGenerator $qRCode
     */
    public function __construct(
        TwoFactorSecret $twoFactorSecret,
        CustomerConfig $customerConfig,
        QRCodeGenerator $qRCode
    ) {

        $this->twoFactorSecret = $twoFactorSecret;
        $this->customerConfig  = $customerConfig;
        $this->qRCode          = $qRCode;
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
        $secret      = $this->twoFactorSecret->getValue($customer);
        $email       = $customer->getEmail();
        $companyName = $this->customerConfig->getCompanyName();
        $qrCode      = $this->qRCode->generateQRCode($companyName, $email, $secret);

        return $qrCode;
    }
}
