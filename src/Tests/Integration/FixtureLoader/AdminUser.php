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

use Magento\User\Model\UserFactory;

class AdminUser extends AbstractLoader
{

    public function loadData()
    {
        $this->setSecureArea();
        /** @var UserFactory $userFactory */
        $userFactory = $this->createObject(UserFactory::class);
        foreach ($this->data as $userData) {
            $user = $userFactory->create();
            foreach ($userData as $key => $value) {
                $user->setData($key, $value);
            }
            $user->save();
        }
    }

    public function rollBackData()
    {
        /** @var UserFactory $userFactory */
        $userFactory = $this->createObject(UserFactory::class);
        foreach ($this->data as $userData) {
            $user = $userFactory->create();
            $user->loadByUsername($userData['username']);
            $user->delete();
        }
    }

    public function verifyData()
    {
        foreach ($this->data as $userData) {
            if (isset($userData['user_id'])) {
                throw new \Exception('You can not set a user_id for an admin user - it prevents them saving');
            }

            $requiredAttributes = ['username', 'password'];

            foreach ($requiredAttributes as $attribute) {
                if (!isset($userData[$attribute])) {
                    throw new \Exception("You must set a value for $attribute");

                }
            }
        }
    }
}
