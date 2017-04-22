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
        $customerData = null;
        require __DIR__.'/../_data/customer.php';
        if (null === $customerData) {
            throw new \Exception("No Customer Data has been set");
        }

        return $customerData;
    }

    public static function loadCustomer()
    {
        $action       = 'load';
        $customerData = self::getCustomerData();
        require __DIR__.'/../_loaders/customer.php';
    }

    public static function loadCustomerRollback()
    {
        $action       = 'rollback';
        $customerData = self::getCustomerData();
        require __DIR__.'/../_loaders/customer.php';
    }
}
