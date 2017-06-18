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

namespace Rossmitchell\Twofactor\Tests\Integration\Customer\Verification;

use Rossmitchell\Twofactor\Tests\Integration\Abstracts\AbstractTestClass;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\ConfigurationLoader;

class RedirectToHomeWhenDisabledTest extends AbstractTestClass
{
    use ConfigurationLoader;

    public static function getConfigurationDataPath()
    {
        return __DIR__.'/../_files/two_factor_disabled.php';
    }

    /**
     * @magentoDbIsolation   enabled
     * @magentoDataFixture   loadConfiguration
     */
    public function testRedirectWhenDisabled()
    {
        $this->getRequest()
            ->setMethod('POST')
            ->setParam('secret', 'notarealcode');
        $this->dispatch('/twofactor/customerlogin/verify');

        $this->assertRedirectsToHomePage();
    }
}
