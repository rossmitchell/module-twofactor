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

namespace Rossmitchell\Twofactor\Traits;

use Magento\Framework\Exception\InputException;

trait SessionTrait
{

    public function setData($key, $value)
    {
        $methodName = $this->convertKeyToMethodName('set', $key);
        $session    = $this->getSession();
        $session->$methodName($value);
    }

    public function getData($key)
    {
        $methodName = $this->convertKeyToMethodName('get', $key);
        $session    = $this->getSession();
        return $session->$methodName();
    }

    public function unsetData($key)
    {
        $methodName = $this->convertKeyToMethodName('uns', $key);
        $session    = $this->getSession();
        $session->$methodName($key);
    }

    public function hasData($key)
    {
        $methodName = $this->convertKeyToMethodName('has', $key);
        $session    = $this->getSession();

        return $session->$methodName();
    }

    private function convertKeyToMethodName($type, $key)
    {
        $allowedMethods = ['get', 'set', 'uns', 'has'];
        if (!in_array($type, $allowedMethods)) {
            InputException::invalidFieldValue('type', $type);
        }
        $methodName = $type.str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

        return $methodName;
    }

    abstract public function getSession();
}
