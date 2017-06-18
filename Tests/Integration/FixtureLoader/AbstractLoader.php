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

use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

abstract class AbstractLoader
{
    public $data;

    private $objectManager;

    public function __construct($data)
    {
        $this->data = $data;
        $this->verifyData();
    }

    abstract public function loadData();

    abstract public function rollBackData();

    abstract public function verifyData();

    public function createObject($className, $new = true)
    {
        $objectManager = $this->getObjectManager();
        if ($new === true) {
            return $objectManager->create($className);
        }

        return $objectManager->get($className);
    }

    private function getObjectManager()
    {
        if (null === $this->objectManager) {
            $this->objectManager = Bootstrap::getObjectManager();
        }

        return $this->objectManager;
    }

    public function setSecureArea()
    {
        /** @var Registry $registry */
        $registry = $this->createObject(Registry::class, false);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);
    }
}
