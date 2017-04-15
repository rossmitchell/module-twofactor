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

namespace Rossmitchell\Twofactor\Block\Customer\Account\Edit;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Rossmitchell\Twofactor\Model\Customer\UsingTwoFactor;

class UseTwoFactor extends Template
{
    /**
     * @var UsingTwoFactor
     */
    private $usingTwoFactor;

    /**
     * UseTwoFactor constructor.
     *
     * @param Context $context
     * @param UsingTwoFactor   $usingTwoFactor
     * @param array            $data
     */
    public function __construct(Context $context, UsingTwoFactor $usingTwoFactor, array $data = [])
    {
        parent::__construct($context, $data);
        $this->usingTwoFactor = $usingTwoFactor;
    }

    public function isUsingTwoFactor()
    {
        return $this->usingTwoFactor->isCustomerUsingTwoFactor();
    }

    public function getSelectedForYes()
    {
        return $this->getSelectedSnippet(true);
    }

    public function getSelectedForNo()
    {
        return $this->getSelectedSnippet(false);
    }

    private function getSelectedSnippet($condition)
    {
        $html = '';
        if ($this->isUsingTwoFactor() === $condition) {
            $html = ' selected="selected"';
        }

        return $html;
    }
}
