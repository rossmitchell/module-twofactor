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

namespace Rossmitchell\Twofactor\Observer\Controller;

use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Rossmitchell\Twofactor\Model\Customer\Getter;
use Rossmitchell\Twofactor\Model\Customer\Session;
use Rossmitchell\Twofactor\Model\Customer\UsingTwoFactor;

class Predispatch implements ObserverInterface
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var Getter
     */
    private $customerGetter;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var UsingTwoFactor
     */
    private $usingTwoFactor;

    /**
     * Predispatch constructor.
     *
     * @param ResponseFactory $responseFactory
     * @param UrlInterface    $url
     * @param Getter          $customerGetter
     * @param Session         $session
     * @param UsingTwoFactor  $usingTwoFactor
     */
    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Getter $customerGetter,
        Session $session,
        UsingTwoFactor $usingTwoFactor
    ) {
        $this->responseFactory = $responseFactory;
        $this->url             = $url;
        $this->customerGetter  = $customerGetter;
        $this->session         = $session;
        $this->usingTwoFactor  = $usingTwoFactor;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->shouldTheCustomerBeRedirected() === false) {
            return;
        }

        if ($this->hasTwoFactorBeenChecked() === true) {
            return;
        }

        $this->redirectToTwoFactorCheck();
    }

    private function shouldTheCustomerBeRedirected()
    {
        if ($this->areWeOnTheAuthenticationPage() === true) {
            return false;
        }

        if ($this->areWeOnTheVerificationPage() === true) {
            return false;
        }

        $customer = $this->customerGetter->getCustomer();
        if ($customer === false) {
            return false;
        }
        $usingTwoFactor = $this->usingTwoFactor->isCustomerUsingTwoFactor();
        if ($usingTwoFactor === false) {
            return false;
        }

        return true;
    }

    private function areWeOnTheAuthenticationPage()
    {
        $currentUrl  = $this->url->getCurrentUrl();
        $redirectUrl = $this->getUrlToRedirectTo();

        return ($currentUrl === $redirectUrl);
    }

    private function areWeOnTheVerificationPage()
    {
        $currentUrl = trim($this->url->getCurrentUrl(), '/');
        $verificationUrl = trim($this->url->getUrl('twofactor/customerlogin/verify'), '/');

        return ($currentUrl === $verificationUrl);
    }

    private function getUrlToRedirectTo()
    {
        return $this->url->getUrl('twofactor/customerlogin/index');
    }

    private function hasTwoFactorBeenChecked()
    {
        $checked = $this->session->getData('two_factor_passed');

        return ($checked === true);
    }

    private function redirectToTwoFactorCheck()
    {
        $twoFactorCheckUrl = $this->getUrlToRedirectTo();
        $response          = $this->responseFactory->create();
        $response->setRedirect($twoFactorCheckUrl);
        $response->sendResponse();
        exit();
    }
}
