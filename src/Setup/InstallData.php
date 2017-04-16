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

namespace Rossmitchell\Twofactor\Setup;

use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Model\AttributeRepository;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Attribute\TwoFactorSecret;

class InstallData implements InstallDataInterface
{

    private $customerSetupFactory;
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * Constructor
     *
     * @param CustomerSetupFactory         $customerSetupFactory
     * @param AttributeSetFactory          $attributeSetFactory
     * @param AttributeRepositoryInterface $attributeRepository
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        AttributeRepositoryInterface $attributeRepository
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory  = $attributeSetFactory;
        $this->attributeRepository  = $attributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /** @var CustomerSetup $customerSetup */
        $customerSetup  = $this->customerSetupFactory->create(['setup' => $setup]);
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet     = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $useTwoFactorCode = IsUsingTwoFactor::ATTRIBUTE_CODE;

        $customerSetup->addAttribute(
            'customer',
            $useTwoFactorCode,
            [
                'type' => 'int',
                'label' => $useTwoFactorCode,
                'input' => 'boolean',
                'source' => '',
                'required' => true,
                'visible' => true,
                'position' => 333,
                'system' => false,
                'backend' => '',
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', $useTwoFactorCode)->addData(
            [
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => [
                    'adminhtml_customer',
                    'customer_account_create',
                    'customer_account_edit',
                ],
            ]
        );
        $attribute->save();

        $secretCode = TwoFactorSecret::ATTRIBUTE_CODE;

        $customerSetup->addAttribute(
            'customer',
            $secretCode,
            [
                'type' => 'varchar',
                'label' => $secretCode,
                'input' => 'text',
                'source' => '',
                'required' => false,
                'visible' => false,
                'position' => 334,
                'system' => false,
                'backend' => '',
            ]
        );
    }
}
