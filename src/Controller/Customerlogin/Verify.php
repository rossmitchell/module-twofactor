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

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use PragmaRX\Google2FA\Google2FA;

class Verify extends Action
{

    protected $resultPageFactory;
    /**
     * @var Google2FA
     */
    private $google2FA;

    /**
     * Constructor
     *
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     * @param Google2FA   $google2FA
     */
    public function __construct(Context $context, PageFactory $resultPageFactory, Google2FA $google2FA)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->google2FA = $google2FA;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $number = $this->getRequest()->getParam('number');

        $resultPage = $this->resultPageFactory->create();

        /** @var Messages $messageBlock */
        $messageBlock = $resultPage->getLayout()->createBlock(
            'Magento\Framework\View\Element\Messages',
            'answer'
        );
        if (is_numeric($number)) {
            $messageBlock->addSuccess($number . ' times 2 is ' . ($number * 2));
        } else {
            $messageBlock->addError('You didn\'t enter a number!');
        }

        $messageBlock->addSuccess($this->google2FA->generateSecretKey(32));

        $resultPage->getLayout()->setChild(
            'content',
            $messageBlock->getNameInLayout(),
            'answer_alias'
        );

        return $resultPage;
    }
}
