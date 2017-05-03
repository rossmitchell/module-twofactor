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

namespace Rossmitchell\Twofactor\Observer\Controller\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Rossmitchell\Twofactor\Model\Config\Customer as CustomerAdmin;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Customer;
use Rossmitchell\Twofactor\Model\Customer\Session;
use Rossmitchell\Twofactor\Model\Urls\Checker;
use Rossmitchell\Twofactor\Model\Urls\Fetcher;
use Rossmitchell\Twofactor\Model\Verification\IsVerified;
use Rossmitchell\Twofactor\Model\TwoFactorUrls;

/**
 * Class Postdispatch
 *
 * This is call after the page response has been generated, but before it has been sent through to the user. There are a
 * couple of benefits to calling the method at this point rather than before the response has been generated. First, it
 * gets called as soon as the customer logs in, which should save a redirect, and it also means that everything has
 * already been instantiated, so I don't have to worry about the session issues that can crop up when a method is called
 * to early.
 *
 * @TODO: This method is really quite complicate3d and should be refactored into separate classes
 */
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
     * @var Customer
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
     * @var Session
     */
    private $customerSession;
    /**
     * @var CustomerAdmin
     */
    private $customerAdmin;
    /**
     * @var Fetcher
     */
    private $fetcher;
    /**
     * @var Checker
     */
    private $checker;

    /**
     * Predispatch constructor.
     *
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     * @param Customer $customerGetter
     * @param IsVerified $isVerified
     * @param Session $customerSession
     * @param IsUsingTwoFactor $isUsingTwoFactor
     * @param CustomerAdmin $customerAdmin
     * @param Fetcher $fetcher
     * @param Checker $checker
     */
    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Customer $customerGetter,
        IsVerified $isVerified,
        Session $customerSession,
        IsUsingTwoFactor $isUsingTwoFactor,
        CustomerAdmin $customerAdmin,
        Fetcher $fetcher,
        Checker $checker
    ) {
        $this->responseFactory  = $responseFactory;
        $this->url              = $url;
        $this->customerGetter   = $customerGetter;
        $this->isUsingTwoFactor = $isUsingTwoFactor;
        $this->isVerified       = $isVerified;
        $this->customerSession  = $customerSession;
        $this->customerAdmin    = $customerAdmin;
        $this->fetcher = $fetcher;
        $this->checker = $checker;
    }

    /**
     * This is the observer method. It *now* listens for the controller_front_send_response_before event, and really
     * should be renamed
     *
     * @TODO: Rename the class so it matches the event
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->customerAdmin->isTwoFactorEnabled() != true) {
            return;
        }

        if ($this->shouldTheCustomerBeRedirected() === false) {
            return;
        }

        if ($this->hasTwoFactorBeenChecked() === true) {
            return;
        }

        $controller = $observer->getEvent()->getData('response');
        $this->redirectToTwoFactorCheck($controller);
    }

    /**
     * This checks to see if the customer is on a page that shouldn't be redirected, if we actually have a customer, and
     * if so does that customer have two fact enabled. Very similar checks are done in the admin observer and this is
     * one of the methods that I want to refactor, once the test coverage is high enough to let me do this with
     * confidence
     *
     * @TODO: Refactor this
     *
     * @return bool
     */
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

    /**
     * Checks if we are on the authentication or verification page. This code is duplicated in the admin observer, other
     * than forAdmin flag and can be refactored
     *
     * @TODO: move this to either the Checker class or somewhere else
     *
     * @return bool
     */
    private function areWeOnAnAllowedPage()
    {
        $twoFactorUrls = $this->checker;
        if ($twoFactorUrls->areWeOnTheAuthenticationPage(false) === true) {
            return true;
        }

        if ($twoFactorUrls->areWeOnTheVerificationPage(false) === true) {
            return true;
        }

        return false;
    }

    /**
     * Checks the session to see if the verification flag has been set. Can be refactored
     *
     * @return bool
     */
    private function hasTwoFactorBeenChecked()
    {
        $session = $this->customerSession;
        $checked = $this->isVerified->isVerified($session);

        return ($checked === true);
    }

    /**
     * Redirects the customer to two factor authentication page, i.e. where they need to enter in their code./
     *
     * @param $response
     */
    private function redirectToTwoFactorCheck($response)
    {
        $twoFactorCheckUrl = $this->fetcher->getAuthenticationUrl(false);

        $response->setRedirect($twoFactorCheckUrl);
    }
}
