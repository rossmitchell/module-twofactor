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

namespace Rossmitchell\Twofactor\Model\Urls;

use Magento\Framework\UrlInterface;

/**
 * Class Fetcher
 *
 * This is a simple wrapper class around the URL Interface class to give more meaningful names for fetching URLs
 */
class Fetcher
{
    const CUSTOMER_AUTHENTICATION_URL = 'twofactor/customerlogin/index';
    const CUSTOMER_VERIFICATION_URL = 'twofactor/customerlogin/verify';
    const CUSTOMER_ACCOUNT_URL = 'customer/account/index';
    const CUSTOMER_LOGIN_URL = 'customer/account/login';

    const ADMIN_AUTHENTICATION_URL = 'twofactor/adminlogin/index';
    const ADMIN_VERIFICATION_URL = 'twofactor/adminlogin/verify';
    const ADMIN_DASHBOARD_URL = 'admin/dashboard/index';

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * Fetcher constructor.
     *
     * @param UrlInterface $url
     */
    public function __construct(UrlInterface $url)
    {
        $this->url = $url;
    }

    /**
     * Used to get either the admin or customer two factor authentication URL
     *
     * @param bool $forAdmin - true for the admin URL, false for the customer URL
     * @return string
     */
    public function getAuthenticationUrl($forAdmin = false)
    {
        if ($forAdmin === true) {
            return $this->getUrl(self::ADMIN_AUTHENTICATION_URL);
        }

        return $this->getUrl(self::CUSTOMER_AUTHENTICATION_URL);
    }

    /**
     * Used to get either the admin or customer two factor verification URL
     *
     * @param bool $forAdmin - true for the admin URL, false for the customer URL
     * @return mixed
     */
    public function getVerificationUrl($forAdmin = false)
    {
        if ($forAdmin === true) {
            return $this->getUrl(self::ADMIN_VERIFICATION_URL);
        }

        return $this->getUrl(self::CUSTOMER_VERIFICATION_URL);
    }

    /**
     * Used to get the URL for the customer account page
     *
     * @return string
     */
    public function getCustomerAccountUrl()
    {
        return $this->url->getUrl(self::CUSTOMER_ACCOUNT_URL);
    }

    /**
     * Used to get the URL for the admin dashboard page
     *
     * @return string
     */
    public function getAdminDashboardUrl()
    {
        return $this->url->getUrl(self::ADMIN_DASHBOARD_URL);
    }

    /**
     * Used to get the customer login URL
     *
     * @return string
     */
    public function getCustomerLogInUrl()
    {
        return $this->url->getUrl(self::CUSTOMER_LOGIN_URL);
    }

    /**
     * This is used to actually get the URL
     *
     * @param $path
     * @return mixed
     */
    private function getUrl($path)
    {
        return $this->url->getUrl($path);
    }
}