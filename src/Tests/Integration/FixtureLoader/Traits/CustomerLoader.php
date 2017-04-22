<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 22/04/17
 * Time: 14:03
 */

namespace Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits;


use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Customer;

trait CustomerLoader
{
    public static function getCustomerDataPath()
    {
        return __DIR__.'/../_data/customer.php';
    }

    public static function getCustomerData()
    {
        $customerData = null;
        $dataFile = self::getCustomerDataPath();
        require $dataFile;
        if (null === $customerData) {
            throw new \Exception("No Customer Data has been set");
        }

        return $customerData;
    }

    public static function loadCustomer()
    {
        $customerData = self::getCustomerData();
        $customerLoader = new Customer($customerData);
        $customerLoader->loadData();
    }

    public static function loadCustomerRollback()
    {
        $customerData = self::getCustomerData();
        $customerLoader = new Customer($customerData);
        $customerLoader->rollBackData();
    }
}
