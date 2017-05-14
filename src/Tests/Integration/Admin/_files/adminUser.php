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

$adminData = [
    [
        'firstname'         => 'Two Factor Enabled',
        'lastname'          => 'Admin',
        'email'             => 'two_factor_enabled@example.com',
        'username'          => 'two_factor_enabled',
        'password'          => 'password123',
        'is_active'         => '1',
        'role_type'         => 'G',
        'resource_id'       => 'Magento_Backend::all',
        'privileges'        => '',
        'assert_id'         => 0,
        'role_id'           => 1,
        'permissions'       => 'allow',
        'use_two_factor'    => 1,
        'two_factor_secret' => 'WSQO22WRQ4MRQG2SW3YTSVCLCGKCPSWG'
    ],
    [
        'firstname'         => 'Two Factor Disabled',
        'lastname'          => 'Admin',
        'email'             => 'two_factor_disabled@example.com',
        'username'          => 'two_factor_disabled',
        'password'          => 'password123',
        'is_active'         => '1',
        'role_type'         => 'G',
        'resource_id'       => 'Magento_Backend::all',
        'privileges'        => '',
        'assert_id'         => 0,
        'role_id'           => 1,
        'permissions'       => 'allow',
        'use_two_factor'    => 0,
        'two_factor_secret' => ''
    ]
];
