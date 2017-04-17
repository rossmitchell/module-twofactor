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

namespace Rossmitchell\Twofactor\Block\Adminhtml\Adminlogin;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\Template;
use Rossmitchell\Twofactor\Model\TwoFactorUrls;

class Index extends Template
{
    /**
     * @var TwoFactorUrls
     */
    private $twoFactorUrls;
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * Index constructor.
     *
     * @param Template\Context $context
     * @param TwoFactorUrls    $twoFactorUrls
     * @param ManagerInterface $messageManager
     * @param array            $data
     */
    public function __construct(
        Template\Context $context,
        TwoFactorUrls $twoFactorUrls,
        ManagerInterface $messageManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->twoFactorUrls  = $twoFactorUrls;
        $this->messageManager = $messageManager;
    }

    public function getVerificationUrl()
    {
        return $this->twoFactorUrls->getAuthenticationUrl(true);
    }

    public function getMessages()
    {
        $messages   = [];
        $collection = $this->messageManager->getMessages(true);
        if (null === $collection) {
            return $messages;
        }

        foreach ($collection->getItems() as $message) {
            $messages[] = $message->getText();
        }

        return $messages;
    }
}
