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

class IsVerified
{
    const TWO_FACTOR_SESSION_KEY = 'two_factor_verified';

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * IsVerified constructor.
     *
     * @param Session $customerSession
     */
    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function isCustomerVerified()
    {
        if ($this->customerSession->hasData(self::TWO_FACTOR_SESSION_KEY) === false) {
            return false;
        }

        $sessionValue = $this->customerSession->getData(self::TWO_FACTOR_SESSION_KEY);
        $isVerified   = ($sessionValue === true);

        return $isVerified;
    }

    public function setCustomerIsVerified()
    {
        $this->customerSession->setData(self::TWO_FACTOR_SESSION_KEY, true);
    }

    public function removeCustomerIsVerified()
    {
        $this->customerSession->unsetData(self::TWO_FACTOR_SESSION_KEY);
    }
}
