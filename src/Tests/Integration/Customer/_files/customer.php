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

use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Attribute\TwoFactorSecret;

$customerData = [
    [
        'websiteId' => 1,
        'id' => 1,
        'email' => 'customer@example.com',
        'password' => 'password',
        'groupId' => 1,
        'storeId' => 1,
        'isActive' => 1,
        'prefix' => 'Mr.',
        'firstname' => 'John',
        'middlename' => 'A',
        'lastname' => 'Smith',
        'suffix' => 'Esq.',
        'defaultBilling' => 1,
        'defaultShipping' => 1,
        'taxvat' => '12',
        'gender' => 0,
        IsUsingTwoFactor::ATTRIBUTE_CODE => 1,
        TwoFactorSecret::ATTRIBUTE_CODE => 'testcode'
    ],
];


