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

namespace Rossmitchell\Twofactor\Tests\Integration\FixtureLoader;

use Magento\Customer\Model\Customer as MagentoCustomer;
use Magento\Customer\Model\CustomerFactory;

class Customer extends AbstractLoader
{


    public function loadData()
    {

        /** @var CustomerFactory $customerFactory */
        foreach ($this->data as $customerData) {
            $customer = $this->createObject(MagentoCustomer::class);
            #$customer = $customerFactory->create();
            #$customer = $customer->load($customerData['id']);
            foreach ($customerData as $key => $value) {
                $customer->setData($key, $value);
            }
            #$customer->isObjectNew(false);
            $customer->save();
        }

    }

    public function rollBackData()
    {
        /** @var MagentoCustomer $customer */
        $this->setSecureArea();
        foreach ($this->data as $customerData) {
            $customer = $this->createObject(MagentoCustomer::class);
            $customer->setWebsiteId($customerData['websiteId']);
            $customer->loadByEmail($customerData['email']);
            #if (!$customer->getId()) {
            #    throw new \Exception("could not load a customer with an id of ".$customerData['id']);
            #}
            $customer->delete();
        }
    }

    public function verifyData()
    {
        foreach ($this->data as $customerData) {
            if (!isset($customerData['id'])) {
                throw new \Exception("You must set an ID for each customer");
            }
        }
    }
}
