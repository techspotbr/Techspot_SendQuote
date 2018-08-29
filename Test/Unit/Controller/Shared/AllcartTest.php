<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Test\Unit\Controller\Shared;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\Controller\ResultFactory;

class AllcartTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Techspot\SendQuote\Controller\Shared\Allcart
     */
    protected $allcartController;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Techspot\SendQuote\Controller\Shared\SendquoteProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendquoteProviderMock;

    /**
     * @var \Techspot\SendQuote\Model\ItemCarrier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemCarrierMock;

    /**
     * @var \Techspot\SendQuote\Model\Sendquote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendquoteMock;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Framework\Controller\Result\Forward|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultForwardMock;

    protected function setUp()
    {
        $this->sendquoteProviderMock = $this->getMockBuilder(\Techspot\SendQuote\Controller\Shared\SendquoteProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemCarrierMock = $this->getMockBuilder(\Techspot\SendQuote\Model\ItemCarrier::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sendquoteMock = $this->getMockBuilder(\Techspot\SendQuote\Model\Sendquote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\Request\Http::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultForwardMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Forward::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->willReturnMap(
                [
                    [ResultFactory::TYPE_REDIRECT, [], $this->resultRedirectMock],
                    [ResultFactory::TYPE_FORWARD, [], $this->resultForwardMock]
                ]
            );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->context = $this->objectManagerHelper->getObject(
            \Magento\Framework\App\Action\Context::class,
            [
                'request' => $this->requestMock,
                'resultFactory' => $this->resultFactoryMock
            ]
        );
        $this->allcartController = $this->objectManagerHelper->getObject(
            \Techspot\SendQuote\Controller\Shared\Allcart::class,
            [
                'context' => $this->context,
                'sendquoteProvider' => $this->sendquoteProviderMock,
                'itemCarrier' => $this->itemCarrierMock
            ]
        );
    }

    public function testExecuteWithSendquote()
    {
        $url = 'http://redirect-url.com';
        $quantity = 2;

        $this->sendquoteProviderMock->expects($this->once())
            ->method('getSendquote')
            ->willReturn($this->sendquoteMock);
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->with('qty')
            ->willReturn($quantity);
        $this->itemCarrierMock->expects($this->once())
            ->method('moveAllToCart')
            ->with($this->sendquoteMock, 2)
            ->willReturn($url);
        $this->resultRedirectMock->expects($this->once())
            ->method('setUrl')
            ->with($url)
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->allcartController->execute());
    }

    public function testExecuteWithNoSendquote()
    {
        $this->sendquoteProviderMock->expects($this->once())
            ->method('getSendquote')
            ->willReturn(false);
        $this->resultForwardMock->expects($this->once())
            ->method('forward')
            ->with('noroute')
            ->willReturnSelf();

        $this->assertSame($this->resultForwardMock, $this->allcartController->execute());
    }
}
