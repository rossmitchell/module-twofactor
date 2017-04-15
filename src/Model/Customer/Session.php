<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 15/04/17
 * Time: 21:34
 */

namespace Rossmitchell\Twofactor\Model\Customer;


use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\InputException;

class Session
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Session constructor.
     *
     * @param CustomerSession $customerSession
     */
    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function setData($key, $value)
    {
        $methodName = $this->convertKeyToMethodName('set', $key);
        $this->customerSession->$methodName($value);
    }

    public function getData($key)
    {
        $methodName = $this->convertKeyToMethodName('get', $key);
        return $this->customerSession->$methodName();
    }

    public function unsetData($key)
    {
        $methodName = $this->convertKeyToMethodName('uns', $key);
        $this->customerSession->$methodName($key);
    }

    public function hasData($key)
    {
        $methodName = $this->convertKeyToMethodName('has', $key);

        return $this->customerSession->$methodName();
    }

    private function convertKeyToMethodName($type,$key)
    {
        switch($type) {
            case 'get':
            case 'set':
            case 'uns':
            case 'has':
                break;
            default:
                InputException::invalidFieldValue('type', $type);
        }
        $methodName = $type . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

        return $methodName;
    }
}
