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

namespace Rossmitchell\Twofactor\Model\Customer\Attribute;

class TwoFactorSecret
{
    const ATTRIBUTE_CODE = 'two_factor_secret';
    /**
     * @var Getter
     */
    private $getter;
    /**
     * @var Setter
     */
    private $setter;

    /**
     * TwoFactorSecret constructor.
     *
     * @param Getter $getter
     * @param Setter $setter
     */
    public function __construct(Getter $getter, Setter $setter)
    {
        $this->getter = $getter;
        $this->setter = $setter;
    }

    public function getValue($customer)
    {
        return $this->getter->getValue($customer, self::ATTRIBUTE_CODE);
    }

    public function setValue($customer, $value)
    {
        $this->setter->setValue($customer, self::ATTRIBUTE_CODE, $value);
    }

    public function hasValue($customer)
    {
        return $this->getter->hasValue($customer, self::ATTRIBUTE_CODE);
    }
}
