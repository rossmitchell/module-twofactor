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

namespace Rossmitchell\Twofactor\Tests\Integration\Abstracts;

use Magento\Backend\Model\Auth;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Security\Model\Plugin\Auth as AuthPlugin;
use Magento\TestFramework\TestCase\AbstractController;
use Magento\Backend\Model\Auth\Session\Proxy;
use Magento\User\Model\User;
use Rossmitchell\Twofactor\Tests\Integration\Helpers\Customer as CustomerHelper;


class AbstractTestClass extends AbstractController
{

    private $customerHelper;

    public function getFormKey()
    {
        $formKeyClass = $this->createObject(FormKey::class, false);
        $formKey      = $formKeyClass->getFormKey();
        $formKeyClass->set($formKey);

        return $formKey;
    }

    /**
     * Login the user
     *
     * @param string $customerEmail Customer to mark as logged in for the session
     *
     * @param int    $websiteId
     *
     * @return void
     * @throws \Exception
     */
    public function login($customerEmail, $websiteId = 1)
    {
        $this->getCustomerHelper()->login($customerEmail, $websiteId);
    }

    public function loginAdmin($username, $password)
    {
        /** @var User $user */
        $user = $this->createObject(User::class);
        $user->loadByUsername($username);
        if (null === $user->getId()) {
            throw new \Exception('Could not find the admin user');
        }

        $auth = $this->createObject(Auth::class, false);
        $auth->login($username, $password);
        $this->createObject(AuthPlugin::class, false)->afterLogin($auth);
    }

    public function createObject($className, $new = true)
    {
        if ($new === true) {
            return $this->_objectManager->create($className);
        }

        return $this->_objectManager->get($className);
    }

    public function assertRedirectsToHomePage()
    {
        $this->assertRedirect($this->stringEndsWith('index.php/'));
    }

    /**
     * @return Proxy
     */
    public function getAdminSession()
    {
        $session = $this->createObject(Proxy::class, false);
        if ($session->isSessionExists() === false) {
            $session->start();
        }

        return $session;
    }

    public function disableSecretKeys()
    {
        $this->createObject(UrlInterface::class, false)->turnOffSecretKey();
    }

    /**
     * @return CustomerHelper
     */
    private function getCustomerHelper()
    {
        if (null === $this->customerHelper) {
            $this->customerHelper = $this->createObject(CustomerHelper::class);
        }

        return $this->customerHelper;
    }
}
