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

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;

class InstallData implements InstallDataInterface
{

    private $customerSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerSetup->addAttribute('customer', 'use_two_factor_authentication', [
            'type' => 'int',
            'label' => 'use_two_factor_authentication',
            'input' => 'boolean',
            'source' => '',
            'required' => true,
            'visible' => true,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);

        
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer', 'use_two_factor_authentication')
            ->addData(['used_in_forms' => [
                'adminhtml_customer',
                'customer_account_create',
                'customer_account_edit'
            ]]);
        $attribute->save();
        

        $customerSetup->addAttribute('customer', 'two_factor_secret', [
            'type' => 'varchar',
            'label' => 'two_factor_secret',
            'input' => 'text',
            'source' => '',
            'required' => false,
            'visible' => false,
            'position' => 333,
            'system' => false,
            'backend' => ''
        ]);
    }
}
