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

namespace Rossmitchell\Twofactor\Tests\Unit\Model\Customer\Attribute;

use Magento\Customer\Model\Data\Customer;
use Magento\Framework\Api\AttributeValue;
use Rossmitchell\Twofactor\Model\Customer\Attribute\Getter;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Attribute\Setter;
use Rossmitchell\Twofactor\Tests\Unit\TestAbstract;

class SetterTest extends TestAbstract
{

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testCanHandleNonCustomerObject()
    {
        $class = $this->getClass();
        $object = new \StdClass();
        $class->setValue($object, 'test', 123);
    }

    public function testCanSetValueOnCustomerInterface()
    {
        $class = $this->getClass();

        $value = $this->getObject(AttributeValue::class);

        $factory = $this->getMockBuilder('\Magento\Framework\Api\AttributeValueFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $factory->expects($this->once())->method('create')->will($this->returnValue($value));


        /** @var Customer $customer */
        $customer = $this
            ->getObject(Customer::class, ['attributeValueFactory' => $factory]);

        $reflection = new \ReflectionClass($customer);
        $attributes = $reflection->getProperty('customAttributesCodes');
        $attributes->setAccessible(true);
        $attributes->setValue($customer, [IsUsingTwoFactor::ATTRIBUTE_CODE]);
        $class->setValue($customer, IsUsingTwoFactor::ATTRIBUTE_CODE, true);
        $getter = new Getter();
        $this->assertEquals(true, $getter->getValue($customer, IsUsingTwoFactor::ATTRIBUTE_CODE));
    }


    private function getClass()
    {
        $class = new Setter();

        return $class;
    }
}
