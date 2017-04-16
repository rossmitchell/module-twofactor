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


use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\InputException;

class Session
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Session constructor.
     *
     * @param CustomerSession $customerSession
     */
    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function setData($key, $value)
    {
        $methodName = $this->convertKeyToMethodName('set', $key);
        $session    = $this->customerSession;
        $this->startSession($session);
        $session->$methodName($value);
    }

    public function getData($key)
    {
        $methodName = $this->convertKeyToMethodName('get', $key);
        $session    = $this->customerSession;
        $this->startSession($session);

        return $session->$methodName();
    }

    public function unsetData($key)
    {
        $methodName = $this->convertKeyToMethodName('uns', $key);
        $session    = $this->customerSession;
        $this->startSession($session);
        $session->$methodName($key);
    }

    public function hasData($key)
    {
        $methodName = $this->convertKeyToMethodName('has', $key);
        $session    = $this->customerSession;
        $this->startSession($session);

        return $session->$methodName();
    }

    private function convertKeyToMethodName($type, $key)
    {
        switch ($type) {
            case 'get':
            case 'set':
            case 'uns':
            case 'has':
                break;
            default:
                InputException::invalidFieldValue('type', $type);
        }
        $methodName = $type.str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

        return $methodName;
    }

    private function startSession(CustomerSession $session)
    {
        if ($session->isSessionExists() === false) {
            $session->start();
        }
    }
}
