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

namespace Rossmitchell\Twofactor\Block\Customer\Account\Create;

use Rossmitchell\Twofactor\Model\Config\Customer;
use Magento\Framework\View\Element\Template;

class UseTwoFactor extends Template
{
    /**
     * @var Customer
     */
    private $customerConfig;

    /**
     * UseTwoFactor constructor.
     *
     * @param Template\Context $context
     * @param Customer         $customerConfig
     * @param array            $data
     */
    public function __construct(Template\Context $context, Customer $customerConfig, array $data = [])
    {
        parent::__construct($context, $data);
        $this->customerConfig = $customerConfig;
    }

    public function shouldFieldBeDisplayed()
    {
        return ($this->customerConfig->isTwoFactorEnabled() == true);
    }
}
