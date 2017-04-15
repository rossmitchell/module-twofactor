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

namespace Rossmitchell\Twofactor\Block\Customerlogin;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Rossmitchell\Twofactor\Model\Customer\Secret as CustomerSecret;
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\QRCode;
use Rossmitchell\Twofactor\Model\GoogleTwoFactor\Secret;

class Index extends Template
{
    /**
     * @var Secret
     */
    private $secret;
    /**
     * @var QRCode
     */
    private $code;
    /**
     * @var CustomerSecret
     */
    private $customerSecret;

    /**
     * Index constructor.
     *
     * @param Context        $context
     * @param Secret         $secret
     * @param QRCode         $code
     * @param CustomerSecret $customerSecret
     * @param array          $data
     */
    public function __construct(
        Context $context,
        Secret $secret,
        QRCode $code,
        CustomerSecret $customerSecret,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->secret         = $secret;
        $this->code           = $code;
        $this->customerSecret = $customerSecret;
    }

    public function getSecret()
    {
        $secret = false;
        if($this->customerSecret->hasASecret()) {
            $secret = $this->customerSecret->getSecret();
        }

        return $secret;
    }

    public function getQRCode($company, $email, $secret)
    {
        return $this->code->generateQRCode($company, $email, $secret);
    }

    public function displayCurrentCode($secret)
    {
        return $this->code->displayCurrentCode($secret);
    }
}
