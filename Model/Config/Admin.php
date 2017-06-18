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

namespace Rossmitchell\Twofactor\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Admin
{
    const IS_ENABLED_PATH = 'two_factor_admin_users/details/enable';
    const COMPANY_NAME_PATH = 'two_factor_admin_users/details/company_name';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Admin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isTwoFactorEnabled()
    {
        return $this->scopeConfig->getValue(self::IS_ENABLED_PATH);
    }

    public function getCompanyName()
    {
        return $this->scopeConfig->getValue(self::COMPANY_NAME_PATH);
    }
}
