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


use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Configuration;

if (!isset($configurationData) || !is_array($configurationData)) {
    throw new \Exception("No Customer Data has been set");
}

$configurationLoader = new Configuration($configurationData);

if (!isset($action)) {
    throw new \Exception("No action has been set");
}
switch ($action) {
    case 'load':
        $configurationLoader->loadData();
        break;
    case 'rollback':
        $configurationLoader->rollBackData();
        break;
    default:
        throw new Exception("Unknown action: $action");
}
