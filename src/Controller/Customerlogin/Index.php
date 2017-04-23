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

namespace Rossmitchell\Twofactor\Controller\Customerlogin;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Rossmitchell\Twofactor\Model\Config\Customer as CustomerAdmin;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Customer;
use Rossmitchell\Twofactor\Model\TwoFactorUrls;

class Index extends AbstractController
{
    /** @var PageFactory  */
    private $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context          $context
     * @param CustomerAdmin    $customerAdmin
     * @param Customer         $customerGetter
     * @param TwoFactorUrls    $twoFactorUrls
     * @param IsUsingTwoFactor $isUsingTwoFactor
     * @param PageFactory      $resultPageFactory
     */
    public function __construct(
        Context $context,
        CustomerAdmin $customerAdmin,
        Customer $customerGetter,
        TwoFactorUrls $twoFactorUrls,
        IsUsingTwoFactor $isUsingTwoFactor,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $customerAdmin, $customerGetter, $twoFactorUrls, $isUsingTwoFactor);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        if ($this->shouldActionBeRun() === false) {
            return $this->getRedirectAction();
        }

        return $this->resultPageFactory->create();
    }
}
