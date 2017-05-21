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

namespace Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits;

use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Configuration;

trait ConfigurationLoader
{
    public static function getConfigurationDataPath()
    {
        return __DIR__.'/../_data/configuration.php';
    }

    public static function getConfigurationData()
    {
        echo get_called_class() . PHP_EOL;
        $configurationData = null;
        require self::getConfigurationDataPath();
        if (null === $configurationData) {
            throw new \Exception("No Customer Data has been set");
        }

        return $configurationData;
    }

    public static function loadConfiguration()
    {
        $configurationData = self::getConfigurationData();
        $configurationClass = new Configuration($configurationData);
        $configurationClass->loadData();
    }

    public static function loadConfigurationRollback()
    {
        $configurationData = self::getConfigurationData();
        $configurationClass = new Configuration($configurationData);
        $configurationClass->rollBackData();
    }
}
