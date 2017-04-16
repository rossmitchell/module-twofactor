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

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\InputException;

class Setter
{

    public function setValue($object, $attributeCode, $value)
    {
        if ($object instanceof  Customer) {
            $this->setValueOnModel($object, $attributeCode, $value);

            return;
        }

        if ($object instanceof CustomerInterface) {
            $this->setValueOnInterface($object, $attributeCode, $value);

            return;
        }

        throw InputException::invalidFieldValue('object', get_class($object));
    }

    private function setValueOnInterface(CustomerInterface $customer, $attributeCode, $value)
    {
        $customer->setCustomAttribute($attributeCode, $value);

    }

    private function setValueOnModel(Customer $customer, $attributeCode, $value)
    {
        $customer->setData($attributeCode, $value);
    }
}
