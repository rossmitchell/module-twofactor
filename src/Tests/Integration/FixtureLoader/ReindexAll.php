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

namespace Rossmitchell\Twofactor\Tests\Integration\FixtureLoader;

use Magento\Indexer\Model\Indexer\CollectionFactory;
use Magento\Indexer\Model\IndexerFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit_Framework_BaseTestListener;

class ReindexAll extends PHPUnit_Framework_BaseTestListener
{
    private $alreadyRun = false;

    /**
     * This is a fairly unpleasant hack to get around the fact that I can not see a way to add a column to customer
     * flat
     * grid in an install and get the indexes to run at the same time.
     *
     * This then causes the tests to fail, so we are going to try and manually trigger the indexes after everything is
     * installed, but before the tests run.233
     *
     * @param \PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->alreadyRun === true) {
            return;
        }

        $this->alreadyRun = true;

        $objectManager     = Bootstrap::getObjectManager();
        $collectionFactory = $objectManager->create(CollectionFactory::class);
        $indexFactory      = $objectManager->create(IndexerFactory::class);
        $indexerCollection = $collectionFactory->create();
        $ids               = $indexerCollection->getAllIds();
        foreach ($ids as $id) {
            $idx = $indexFactory->create()->load($id);
            $idx->reindexAll($id);
        }
    }
}
