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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Getter;
use Rossmitchell\Twofactor\Model\Customer\IsVerified;
use Rossmitchell\Twofactor\Model\TwoFactorUrls;

class Postdispatch implements ObserverInterface
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
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;
    /**
     * @var IsVerified
     */
    private $isVerified;
    /**
     * @var TwoFactorUrls
     */
    private $twoFactorUrls;

    /**
     * Predispatch constructor.
     *
     * @param ResponseFactory  $responseFactory
     * @param UrlInterface     $url
     * @param Getter           $customerGetter
     * @param IsVerified       $isVerified
     * @param IsUsingTwoFactor $isUsingTwoFactor
     * @param TwoFactorUrls    $twoFactorUrls
     */
    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Getter $customerGetter,
        IsVerified $isVerified,
        IsUsingTwoFactor $isUsingTwoFactor,
        TwoFactorUrls $twoFactorUrls
    ) {
        $this->responseFactory  = $responseFactory;
        $this->url              = $url;
        $this->customerGetter   = $customerGetter;
        $this->isUsingTwoFactor = $isUsingTwoFactor;
        $this->isVerified       = $isVerified;
        $this->twoFactorUrls    = $twoFactorUrls;
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

        $controller = $observer->getEvent()->getData('controller_action');
        $this->redirectToTwoFactorCheck($controller);
    }

    private function shouldTheCustomerBeRedirected()
    {
        if ($this->areWeOnAnAllowedPage() === true) {
            return false;
        }

        $customer = $this->customerGetter->getCustomer();
        if ($customer === false) {
            return false;
        }
        $usingTwoFactor = $this->isUsingTwoFactor->getValue($customer);
        if ($usingTwoFactor === false) {
            return false;
        }

        return true;
    }

    private function areWeOnAnAllowedPage()
    {
        $twoFactorUrls = $this->twoFactorUrls;
        if ($twoFactorUrls->areWeOnTheAuthenticationPage() === true) {
            return true;
        }

        if ($twoFactorUrls->areWeOnTheVerificationPage() === true) {
            return true;
        }

        return false;
    }

    private function hasTwoFactorBeenChecked()
    {
        $checked = $this->isVerified->isCustomerVerified();

        return ($checked === true);
    }

    private function redirectToTwoFactorCheck(Action $controller)
    {
        $twoFactorCheckUrl = $this->twoFactorUrls->getCustomerAuthenticationUrl();
        $response          = $controller->getResponse();
        $response->setRedirect($twoFactorCheckUrl);
    }
}
