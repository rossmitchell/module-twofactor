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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session;

class Getter
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * Getter constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session                     $customerSession
     */
    public function __construct(CustomerRepositoryInterface $customerRepository, Session $customerSession)
    {
        $this->customerRepository = $customerRepository;
        $this->customerSession    = $customerSession;
    }

    public function getCustomer()
    {
        if (null === $this->customer) {
            $this->customer = $this->getCustomerFromSession();
        }

        return $this->customer;
    }

    private function getCustomerFromSession()
    {
        $customerId = $this->customerSession->getCustomerId();
        $customer   = false;

        if (null !== $customerId) {
            $customer = $this->customerRepository->getById($customerId);
        }

        return $customer;
    }

    public function getCustomerSession()
    {
        $session = $this->customerSession;

        return $session;
    }
}
