<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 22/04/17
 * Time: 14:03
 */

namespace Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits;


trait CustomerLoader
{
    public static function getCustomerData()
    {
        require __DIR__ . '/../_data/customer.php';
        if(!isset($customerData)) {
            throw new \Exception("No Customer Data has been set");
        }

        return $customerData;
    }

    static function loadCustomer()
    {
        $action = 'load';
        $customerData = self::getCustomerData();
        require __DIR__ . '/../_loaders/customer.php';
    }

    static function loadCustomerRollback()
    {
        $action = 'rollback';
        $customerData = self::getCustomerData();
        require __DIR__ . '/../_loaders/customer.php';
    }
}
