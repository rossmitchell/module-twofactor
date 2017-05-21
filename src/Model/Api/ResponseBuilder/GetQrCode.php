<?php
/**
 * A Magento 2 module named Rossmitchell/Twofactor
 * Copyright (C) 2017
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

namespace Rossmitchell\Twofactor\Model\Api\ResponseBuilder;

use Magento\Customer\Api\Data\CustomerInterface;
use Rossmitchell\Twofactor\Api\Response\GetQrCodeInterfaceFactory;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Attribute\TwoFactorSecret;
use Rossmitchell\Twofactor\Model\Customer\Customer;
use Rossmitchell\Twofactor\Model\Customer\GetQrCode as GetQrCodeFetcher;
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\QRCode;

class GetQrCode
{
    /**
     * @var GetQrCodeInterfaceFactory
     */
    private $response;
    /**
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;
    /**
     * @var TwoFactorSecret
     */
    private $twoFactorSecret;
    /**
     * @var QRCode
     */
    private $qrCode;
    /**
     * @var GetQrCodeFetcher
     */
    private $getQrCode;

    /**
     * GetQrCode constructor.
     *
     * @param GetQrCodeInterfaceFactory $response
     * @param IsUsingTwoFactor          $isUsingTwoFactor
     * @param TwoFactorSecret           $twoFactorSecret
     * @param QRCode                    $qrCode
     * @param GetQrCodeFetcher          $getQrCode
     */
    public function __construct(
        GetQrCodeInterfaceFactory $response,
        IsUsingTwoFactor $isUsingTwoFactor,
        TwoFactorSecret $twoFactorSecret,
        QRCode $qrCode,
        GetQrCodeFetcher $getQrCode
    ) {

        $this->response = $response;
        $this->isUsingTwoFactor = $isUsingTwoFactor;
        $this->twoFactorSecret = $twoFactorSecret;
        $this->qrCode = $qrCode;
        $this->getQrCode = $getQrCode;
    }

    public function buildResponseForCustomer(CustomerInterface $customer)
    {
        $response = $this->response->create();
        $response->setEmail($customer->getEmail());
        $isUsing = $this->isUsingTwoFactor($customer);
        $response->setIsUsingTwoFactor($isUsing);

        if ($isUsing === true) {
            $response->setQrCode($this->getQrCode->getQrCode($customer));
        }

        return $response;
    }

    private function isUsingTwoFactor(CustomerInterface $customer)
    {
        $hasValue = $this->isUsingTwoFactor->hasValue($customer);
        if ($hasValue === false) {
            return false;
        }

        return $this->isUsingTwoFactor->getValue($customer);
    }
}
