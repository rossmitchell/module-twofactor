<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 13/05/17
 * Time: 23:03
 */

namespace Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits;

use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\AdminUser;

trait AdminUserLoader
{
    public static function getAdminUserDataPath()
    {
        return __DIR__.'/../_data/adminUser.php';
    }

    public static function getAdminUserData()
    {
        $adminData = null;
        $dataFile = self::getAdminUserDataPath();
        require $dataFile;
        if (null === $adminData) {
            throw new \Exception("No Admin Data has been set");
        }

        return $adminData;
    }

    public static function loadAdminUsers()
    {
        $customerData = self::getAdminUserData();
        $customerLoader = new AdminUser($customerData);
        $customerLoader->loadData();
    }
}
