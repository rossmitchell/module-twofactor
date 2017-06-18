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

use Magento\Customer\Model\Customer;
use Rossmitchell\Twofactor\Model\Customer\Attribute\Getter;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Tests\Unit\TestAbstract;

class IsUsingTwoFactorTest extends TestAbstract
{

    public function testGetWithNoValue()
    {
        $customer = $this->getCustomerMock();
        $getter   = $this->getMockBuilder(Getter::class)->getMock();
        $getter->expects($this->once())->method('hasValue')->will($this->returnValue(false));

        /** @var IsUsingTwoFactor $testClass */
        $testClass = $this->getObject(IsUsingTwoFactor::class, ['getter' => $getter]);
        $this->assertFalse($testClass->getValue($customer));

    }

    public function getCustomerMock()
    {
        $mock = $this->getMockBuilder(Customer::class)->disableOriginalConstructor()->getMock();

        return $mock;
    }
}
