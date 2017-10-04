<?php

namespace Worldpay\Payments\Test\Unit\Controller\Apm;

use Worldpay\Payments\Model\Methods\WorldpayPayments;

class WorldpayPaymentsTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this -> quoteMock = $this->getMockBuilder('Magento\Quote\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $billingMock = $this->getMockBuilder('Magento\Quote\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $shippingMock = $this->getMockBuilder('Magento\Quote\Model\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $this -> quoteMock -> expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($billingMock));

        $this -> quoteMock -> expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($shippingMock));

        $this -> configMock = $this->getMockBuilder('\Worldpay\Payments\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this -> configMock -> expects($this->any())
            ->method('getServiceKey')
            ->will($this->returnValue('A_SERVICE_KEY'));

        $this -> sessionMock = $this->getMockBuilder('\Magento\Checkout\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this -> backendSessionMock = $this->getMockBuilder('\Magento\Backend\Model\Auth\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this -> customerSessionMock = $this->getMockBuilder('\Magento\Customer\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $this -> customerMock = $this->getMockBuilder('\Magento\Customer\Model\Customer')
            ->disableOriginalConstructor()
            ->getMock();

        $this -> customerSessionMock -> expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($this -> customerMock));
    }

    public function testStripPortNumberFromIpAddressWhenGettingOrderDetails()
    {
        $worldpayPayments = $this->getMockBuilder(WorldpayPayments::class)
            ->setMethods(array('__construct', 'getSession', 'getClientIp'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->setMinimumConstructorValues($worldpayPayments);

        $worldpayPayments -> expects($this->any())
            ->method('getClientIp')
            ->will($this->returnValue('123.45.678.90:54321'));

        $data = $this->invokeMethod($worldpayPayments, 'getSharedOrderDetails', [$this->quoteMock, "USD"]);

        $this->assertEquals("123.45.678.90", $data['shopperIpAddress']);
    }

    public function testStripPortNumberFromIpAddressWhenSettingUp3dsOrder()
    {
        $worldpayPayments = $this->getMockBuilder(WorldpayPayments::class)
            ->setMethods(array('__construct', 'getSession', 'getClientIp'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->setMinimumConstructorValues($worldpayPayments);
        $worldpayPayments -> expects($this->any())
            ->method('getClientIp')
            ->will($this->returnValue('123.45.678.90:54321'));

        $worldpayPayments -> setupWorldpay($this->quoteMock, "GBP");
        $actual3dsIp = \Worldpay\Utils::getThreeDSShopperObject()['shopperIpAddress'];

        $this->assertEquals("123.45.678.90", $actual3dsIp);
    }

    public function testStrippingPortNumberFromUnknownIpShouldReturnUnknown()
    {
        $worldpayPayments = $this->getMockBuilder(WorldpayPayments::class)
            ->setMethods(array('__construct', 'getSession', 'getClientIp'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->setMinimumConstructorValues($worldpayPayments);
        $worldpayPayments -> expects($this->any())
            ->method('getClientIp')
            ->will($this->returnValue('UNKNOWN'));

        $worldpayPayments -> setupWorldpay($this->quoteMock, "GBP");
        $actual3dsIp = \Worldpay\Utils::getThreeDSShopperObject()['shopperIpAddress'];

        $this->assertEquals("UNKNOWN", $actual3dsIp);
    }

    public function testStripPortNumberShouldReturnEmptyStringWhenIpIsNull()
    {
        $worldpayPayments = $this->getMockBuilder(WorldpayPayments::class)
            ->setMethods(array('__construct', 'getSession', 'getClientIp'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->setMinimumConstructorValues($worldpayPayments);
        $worldpayPayments -> expects($this->any())
            ->method('getClientIp')
            ->will($this->returnValue(null));

        $worldpayPayments -> setupWorldpay($this->quoteMock, "GBP");
        $actual3dsIp = \Worldpay\Utils::getThreeDSShopperObject()['shopperIpAddress'];

        $this->assertEquals("", $actual3dsIp);
    }

    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    private function setProtectedProperty($object, $property, $value)
    {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($object, $value);
    }

    private function setMinimumConstructorValues($worldpayPayments)
    {
        $worldpayPayments->config = $this->configMock;
        $this->setProtectedProperty($worldpayPayments, 'checkoutSession', $this->sessionMock);
        $this->setProtectedProperty($worldpayPayments, 'backendAuthSession', $this->backendSessionMock);
        $this->setProtectedProperty($worldpayPayments, 'customerSession', $this->customerSessionMock);
    }

}
