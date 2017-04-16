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

namespace Rossmitchell\Twofactor\Model;

use Magento\Framework\UrlInterface;

class TwoFactorUrls
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * TwoFactorUrls constructor.
     *
     * @param UrlInterface $url
     */
    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
    }

    public function getCustomerAuthenticationUrl()
    {
        return $this->url->getUrl('twofactor/customerlogin/index');
    }

    public function getCustomerVerificationUrl()
    {
        return $this->url->getUrl('twofactor/customerlogin/verify');
    }

    public function getCustomerAccountUrl()
    {
        return $this->url->getUrl('customer/account/index');
    }

    public function getCustomerLogInUrl()
    {
        return $this->url->getUrl('customer/account/login');
    }

    public function getCurrentUrl()
    {
        return $this->url->getCurrentUrl();
    }

    public function areWeOnTheAuthenticationPage()
    {
        return $this->compareUrls($this->getCurrentUrl(), $this->getCustomerAuthenticationUrl());
    }

    public function areWeOnTheVerificationPage()
    {
        return $this->compareUrls($this->getCurrentUrl(), $this->getCustomerVerificationUrl());
    }

    private function compareUrls($firstUrl, $secondUrl)
    {
        $charList = '\t\n\r/';
        return (trim($firstUrl, $charList) === trim($secondUrl, $charList));
    }
}
