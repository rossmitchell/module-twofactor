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


use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $version = $context->getVersion();

        if (version_compare($version, '0.0.1') < 0) {
            $adminTable = $installer->getTable('admin_user');
            $this->addUseTwoFactorColumn($installer, $adminTable);
            $this->addTwoFactorSecretColumn($installer, $adminTable);
        }

        $installer->endSetup();
    }

    private function addUseTwoFactorColumn(SchemaSetupInterface $installer, $adminTable)
    {
        $installer->getConnection()->addColumn(
            $adminTable,
            'use_two_factor',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => 0,
                'comment' => 'Use Two Factor Authentication',
            ]
        );
    }

    private function addTwoFactorSecretColumn(SchemaSetupInterface $installer, $adminTable)
    {
        $installer->getConnection()->addColumn(
            $adminTable,
            'two_factor_secret',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Two Factor Secret',
            ]
        );
    }
}
