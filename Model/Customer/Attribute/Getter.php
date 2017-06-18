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

class Getter
{
    public function getValue($object, $attributeCode)
    {
        if (!is_object($object)) {
            throw InputException::invalidFieldValue('object', 'false');
        }

        if ($object instanceof CustomerInterface) {
            return $this->getDataFromInterface($object, $attributeCode);
        }

        if ($object instanceof Customer) {
            return $this->getDataFromCustomerModel($object, $attributeCode);
        }

        throw InputException::invalidFieldValue('object', get_class($object));
    }

    public function hasValue($object, $attributeCode)
    {
        return ($this->getValue($object, $attributeCode) !== null);
    }

    private function getDataFromInterface(CustomerInterface $customer, $attributeCode)
    {
        $option = $customer->getCustomAttribute($attributeCode);
        if (null === $option) {
            return null;
        }

        return $option->getValue();
    }

    private function getDataFromCustomerModel(Customer $customer, $attributeCode)
    {
        return $customer->getData($attributeCode);
    }
}
