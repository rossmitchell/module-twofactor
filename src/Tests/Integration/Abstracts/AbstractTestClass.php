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

namespace Rossmitchell\Twofactor\Tests\Integration\Abstracts;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey;
use Magento\TestFramework\TestCase\AbstractController;

class AbstractTestClass extends AbstractController
{

    public function getFormKey()
    {
        return $this->createObject(FormKey::class)->getFormKey();
    }

    /**
     * Login the user
     *
     * @param string $customerEmail Customer to mark as logged in for the session
     *
     * @param int    $websiteId
     *
     * @return void
     * @throws \Exception
     */
    public function login($customerEmail, $websiteId = 1)
    {
        /** @var Session $session */
        $session = $this->createObject(Session::class, false);
        /** @var Customer $customer */
        $customer = $this->createObject(Customer::class);
        $customer->setWebsiteId($websiteId);
        $customerId = $customer->loadByEmail($customerEmail);
        if($session->loginById($customerId->getId()) === false) {
            throw new \Exception("Could not log customer in");
        }
    }

    public function createObject($className, $new = true)
    {
        if ($new === true) {
            return $this->_objectManager->create($className);
        }

        return $this->_objectManager->get($className);
    }

    public function assertRedirectsToHomePage()
    {
        $this->assertRedirect($this->stringEndsWith('index.php/'));
    }
}
