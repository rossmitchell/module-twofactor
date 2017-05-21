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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer as MagentoCustomer;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Indexer\IndexerRegistry;

class Customer extends AbstractLoader
{


    public function loadData()
    {
        $this->runIndexes();
        foreach ($this->data as $customerData) {
            $customer = $this->getCustomer($customerData['email']);
            foreach ($customerData as $key => $value) {
                $customer->setData($key, $value);
            }
            $customer->save();
        }
    }

    public function rollBackData()
    {
        $this->runIndexes();
        /** @var MagentoCustomer $customer */
        $this->setSecureArea();
        foreach ($this->data as $customerData) {
            $customer = $this->getCustomer($customerData['email'], false);
            if ($customer === false) {
                continue;
            }
            $customer = $this->createObject(MagentoCustomer::class);
            $customer->setWebsiteId($customerData['websiteId']);
            $customer->loadByEmail($customerData['email']);
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

    /**
     * @param      $email
     * @param bool $force
     *
     * @return bool|\Magento\Customer\Api\Data\CustomerInterface|mixed
     */
    private function getCustomer($email, $force = true)
    {
        /** @var CustomerRepositoryInterface $repository */
        $repository = $this->createObject(CustomerRepositoryInterface::class, false);
        try {
            $customer = $repository->get($email);
        } catch (NoSuchEntityException $exception) {
            if ($force === false) {
                $customer = false;
            } else {
                $customer = $this->createObject(MagentoCustomer::class);
            }
        }

        return $customer;
    }

    private function runIndexes()
    {
        $indexerRepository = $this->createObject(IndexerRegistry::class, false);
        $indexer = $indexerRepository->get(MagentoCustomer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->reindexAll();
    }
}
