<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Test\Unit\Controller;

class SendquoteProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Techspot\SendQuote\Controller\SendquoteProvider
     */
    protected $sendquoteProvider;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Techspot\SendQuote\Model\SendquoteFactory
     */
    protected $sendquoteFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Set up mock objects for tested class
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->request = $this->createMock(\Magento\Framework\App\RequestInterface::class);

        $this->sendquoteFactory = $this->createPartialMock(\Techspot\SendQuote\Model\SendquoteFactory::class, ['create']);

        $this->customerSession = $this->createPartialMock(\Magento\Customer\Model\Session::class, ['getCustomerId']);

        $this->messageManager = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);

        $this->sendquoteProvider = $objectManager->getObject(
            \Techspot\SendQuote\Controller\SendquoteProvider::class,
            [
                'request' => $this->request,
                'sendquoteFactory' => $this->sendquoteFactory,
                'customerSession' => $this->customerSession,
                'messageManager' => $this->messageManager
            ]
        );
    }

    public function testGetSendquote()
    {
        $sendquote = $this->createMock(\Techspot\SendQuote\Model\Sendquote::class);

        $this->sendquoteFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($sendquote));

        $this->assertEquals($sendquote, $this->sendquoteProvider->getSendquote());
    }

    public function testGetSendquoteWithCustomer()
    {
        $sendquote = $this->createPartialMock(
            \Techspot\SendQuote\Model\Sendquote::class,
            ['loadByCustomerId', 'getId', 'getCustomerId', '__wakeup']
        );
        $sendquote->expects($this->once())
            ->method('loadByCustomerId')
            ->will($this->returnSelf());
        $sendquote->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));
        $sendquote->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $this->sendquoteFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($sendquote));

        $this->customerSession->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $this->assertEquals($sendquote, $this->sendquoteProvider->getSendquote());
    }

    public function testGetSendquoteWithIdAndCustomer()
    {
        $sendquote = $this->createPartialMock(
            \Techspot\SendQuote\Model\Sendquote::class,
            ['loadByCustomerId', 'load', 'getId', 'getCustomerId', '__wakeup']
        );

        $sendquote->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $sendquote->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $sendquote->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $this->sendquoteFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($sendquote));

        $this->request->expects($this->once())
            ->method('getParam')
            ->will($this->returnValue(1));

        $this->customerSession->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $this->assertEquals($sendquote, $this->sendquoteProvider->getSendquote());
    }

    public function testGetSendquoteWithIdWithoutCustomer()
    {
        $sendquote = $this->createPartialMock(
            \Techspot\SendQuote\Model\Sendquote::class,
            ['loadByCustomerId', 'load', 'getId', 'getCustomerId', '__wakeup']
        );

        $sendquote->expects($this->once())
            ->method('load')
            ->will($this->returnSelf());
        $sendquote->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));
        $sendquote->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $this->sendquoteFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($sendquote));

        $this->request->expects($this->once())
            ->method('getParam')
            ->will($this->returnValue(1));

        $this->assertEquals(false, $this->sendquoteProvider->getSendquote());
    }
}
