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

use Magento\Framework\App\Config\ScopeConfigInterface;

class Configuration extends AbstractLoader
{

    public function loadData()
    {
        /** @var \Magento\TestFramework\App\Config $configWriter */
        $configWriter = $this->createObject(ScopeConfigInterface::class, false);
        foreach ($this->data as $configValue) {
            $key   = $configValue['key'];
            $value = $configValue['value'];
            $scope = $configValue['scope'];
            switch ($scope) {
                case 'default':
                    $configWriter->setValue($key, $value, 'default');
                // Intentional fall through
                case 'website':
                    $configWriter->setValue($key, $value, 'website');
                // Intentional fall through
                case 'store':
                    $configWriter->setValue($key, $value, 'store');
                    break;
                default:
                    throw new \Exception("Scope must be ons of default, website, store. You used $scope");
            }
        }
    }

    public function rollBackData()
    {
        // TODO: Implement rollBackData() method.
    }

    public function verifyData()
    {
        if (!is_array($this->data)) {
            throw new \Exception("configData must be an array");
        }
        foreach ($this->data as $config) {
            if(!isset($config['key']) || !isset($config['value']) || !isset($config['scope'])) {
                throw new \Exception('Each row must contain key, value, and scope values');
            }
        }
    }
}
