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

class Secret
{
    /**
     * @var Getter
     */
    private $customerGetter;
    private $customer;
    private $secret;

    /**
     * Secret constructor.
     *
     * @param Getter $customerGetter
     */
    public function __construct(Getter $customerGetter)
    {
        $this->customerGetter = $customerGetter;
    }

    public function hasASecret()
    {
        /* Check that we have a customer */
        $customer = $this->getCustomer();
        if ($customer === false || null === $customer->getId()) {
            return false;
        }

        /* Check that the attribute has been set */
        $attribute = $customer->getCustomAttribute('two_factor_secret');
        if(null === $attribute) {
            return false;
        }

        /* Check that there is a value saved */
        $secret = $attribute->getValue();
        if (empty($secret)) {
            return false;
        }
        $this->secret = $secret;

        return true;
    }

    public function getSecret()
    {
        if (false === $this->hasASecret()) {
            return false;
        }

        return $this->secret;
    }

    private function getCustomer()
    {
        if (null === $this->customer) {
            $this->customer = $this->customerGetter->getCustomer();
        }

        return $this->customer;
    }
}
