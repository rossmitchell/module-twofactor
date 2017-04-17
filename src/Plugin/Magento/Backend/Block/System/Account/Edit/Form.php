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

namespace Rossmitchell\Twofactor\Plugin\Magento\Backend\Block\System\Account\Edit;

use Magento\Backend\Block\System\Account\Edit\Form as OriginalClass;
use Magento\Framework\Data\Form as OriginalForm;
use Magento\Framework\Data\Form\Element\Fieldset;
use Rossmitchell\Twofactor\Model\Admin\AdminUser;
use Rossmitchell\Twofactor\Model\Admin\Attribute\IsUsingTwoFactor;

class Form
{
    /**
     * @var AdminUser
     */
    private $adminUser;

    /**
     * Form constructor.
     *
     * @param AdminUser $adminUser
     */
    public function __construct(AdminUser $adminUser)
    {
        $this->adminUser = $adminUser;
    }

    public function beforeSetForm(OriginalClass $subject, OriginalForm $form)
    {
        $fieldSet = $this->getFieldSetFromForm($form);
        $this->addFieldToFieldSet($fieldSet);
        $this->updateFormData($form, $subject);

        return [$form];
    }

    /**
     * @param OriginalForm $form
     *
     * @return Fieldset
     * @throws \Exception
     */
    private function getFieldSetFromForm(OriginalForm $form)
    {
        $fieldSet = $form->getElement('base_fieldset');
        if (!($fieldSet instanceof Fieldset)) {
            throw new \Exception("The Fieldset has changed it's ID");
        }

        return $fieldSet;
    }

    private function addFieldToFieldSet(Fieldset $fieldSet)
    {
        $attributeCode = IsUsingTwoFactor::ATTRIBUTE_CODE;
        $fieldSet->addField(
            $attributeCode,
            'select',
            [
                'name' => $attributeCode,
                'label' => __('Use Two Factor for this account'),
                'title' => __('Use Two Factor for this account'),
                'values' => [['value' => '0', 'label' => 'No'], ['value' => '1', 'label' => 'Yes']],
                'class' => 'select',
            ]
        );
    }

    private function updateFormData(OriginalForm $form, OriginalClass $subject)
    {
        $user = $this->adminUser->getAdminUser();
        $user->unsetData('password');
        $userData = $user->getData();
        unset($userData[$subject::IDENTITY_VERIFICATION_PASSWORD_FIELD]);
        $form->setValues($userData);
    }
}
