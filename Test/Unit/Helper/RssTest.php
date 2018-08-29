<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Test\Unit\Helper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RssTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Techspot\SendQuote\Helper\Rss
     */
    protected $model;

    /**
     * @var \Techspot\SendQuote\Model\SendquoteFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendquoteFactoryMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Url\DecoderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlDecoderMock;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSessionMock;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerRepositoryMock;

    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    protected function setUp()
    {
        $this->sendquoteFactoryMock = $this->getMockBuilder(\Techspot\SendQuote\Model\SendquoteFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->getMock();

        $this->urlDecoderMock = $this->getMockBuilder(\Magento\Framework\Url\DecoderInterface::class)
            ->getMock();

        $this->customerFactoryMock = $this->getMockBuilder(\Magento\Customer\Api\Data\CustomerInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->customerSessionMock = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerRepositoryMock = $this->getMockBuilder(\Magento\Customer\Api\CustomerRepositoryInterface::class)
            ->getMock();

        $this->moduleManagerMock = $this->getMockBuilder(\Magento\Framework\Module\Manager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfigMock = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->model = $objectManager->getObject(
            \Techspot\SendQuote\Helper\Rss::class,
            [
                'sendquoteFactory' => $this->sendquoteFactoryMock,
                'httpRequest' => $this->requestMock,
                'urlDecoder' => $this->urlDecoderMock,
                'customerFactory' => $this->customerFactoryMock,
                'customerSession' => $this->customerSessionMock,
                'customerRepository' => $this->customerRepositoryMock,
                'moduleManager' => $this->moduleManagerMock,
                'scopeConfig' => $this->scopeConfigMock,
            ]
        );
    }

    public function testGetSendquoteWithSendquoteId()
    {
        $sendquoteId = 1;

        $sendquote = $this->getMockBuilder(\Techspot\SendQuote\Model\Sendquote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sendquoteFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($sendquote);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('sendquote_id', null)
            ->willReturn($sendquoteId);

        $sendquote->expects($this->once())
            ->method('load')
            ->with($sendquoteId, null)
            ->willReturnSelf();

        $this->assertEquals($sendquote, $this->model->getSendquote());
        // Check that sendquote is cached
        $this->assertSame($sendquote, $this->model->getSendquote());
    }

    public function testGetSendquoteWithCustomerId()
    {
        $customerId = 1;
        $data = $customerId . ',2';

        $sendquote = $this->getMockBuilder(\Techspot\SendQuote\Model\Sendquote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sendquoteFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($sendquote);

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('sendquote_id', null)
            ->willReturn('');

        $this->urlDecoderMock->expects($this->any())
            ->method('decode')
            ->willReturnArgument(0);

        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('data', null)
            ->willReturn($data);

        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn(0);

        $customer = $this->getMockBuilder(\Magento\Customer\Api\Data\CustomerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($customer);

        $this->customerRepositoryMock->expects($this->never())
            ->method('getById');

        $customer->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($customerId);

        $sendquote->expects($this->once())
            ->method('loadByCustomerId')
            ->with($customerId, false)
            ->willReturnSelf();

        $this->assertEquals($sendquote, $this->model->getSendquote());
    }

    public function testGetCustomerWithSession()
    {
        $customerId = 1;
        $data = $customerId . ',2';

        $this->urlDecoderMock->expects($this->any())
            ->method('decode')
            ->willReturnArgument(0);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('data', null)
            ->willReturn($data);

        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $customer = $this->getMockBuilder(\Magento\Customer\Api\Data\CustomerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);

        $this->customerFactoryMock->expects($this->never())
            ->method('create');

        $this->assertEquals($customer, $this->model->getCustomer());
        // Check that customer is cached
        $this->assertSame($customer, $this->model->getCustomer());
    }

    /**
     * @param bool $isModuleEnabled
     * @param bool $isSendquoteActive
     * @param bool $result
     * @dataProvider dataProviderIsRssAllow
     */
    public function testIsRssAllow($isModuleEnabled, $isSendquoteActive, $result)
    {
        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_Rss')
            ->willReturn($isModuleEnabled);

        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with('rss/sendquote/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ->willReturn($isSendquoteActive);

        $this->assertEquals($result, $this->model->isRssAllow());
    }

    /**
     * @return array
     */
    public function dataProviderIsRssAllow()
    {
        return [
            [false, false, false],
            [true, false, false],
            [false, true, false],
            [true, true, true],
        ];
    }
}
