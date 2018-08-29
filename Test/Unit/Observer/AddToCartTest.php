<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Test\Unit\Observer;

use \Techspot\SendQuote\Observer\AddToCart as Observer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddToCartTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Observer
     */
    protected $observer;

    /**
     * @var \Techspot\SendQuote\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;

    /**
     * @var \Techspot\SendQuote\Model\SendquoteFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendquoteFactory;

    /**
     * @var \Techspot\SendQuote\Model\Sendquote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendquote;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    protected function setUp()
    {
        $this->checkoutSession = $this->getMockBuilder(
            \Magento\Checkout\Model\Session::class
        )->setMethods(
            [
                'getSharedSendquote',
                'getSendquotePendingMessages',
                'getSendquotePendingUrls',
                'getSendquoteIds',
                'getSingleSendquoteId',
                'setSingleSendquoteId',
                'setSendquoteIds',
                'setSendquotePendingUrls',
                'setSendquotePendingMessages',
                'setNoCartRedirect',
            ]
        )->disableOriginalConstructor()->getMock();
        $this->customerSession = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->disableOriginalConstructor()
            ->setMethods(['setSendquoteItemCount', 'isLoggedIn', 'getCustomerId'])
            ->getMock();
        $this->sendquoteFactory = $this->getMockBuilder(\Techspot\SendQuote\Model\SendquoteFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->sendquote = $this->getMockBuilder(\Techspot\SendQuote\Model\Sendquote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManager = $this->getMockBuilder(\Magento\Framework\Message\ManagerInterface::class)
            ->getMock();

        $this->sendquoteFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->sendquote);

        $this->observer = new Observer(
            $this->checkoutSession,
            $this->customerSession,
            $this->sendquoteFactory,
            $this->messageManager
        );
    }

    public function testExecute()
    {
        $sendquoteId = 1;
        $customerId = 2;
        $url = 'http://some.pending/url';
        $message = 'some error msg';

        $eventObserver = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event = $this->getMockBuilder(\Magento\Framework\Event::class)
            ->setMethods(['getRequest', 'getResponse'])
            ->disableOriginalConstructor()
            ->getMock();
        $request = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)->getMock();
        $response = $this->getMockBuilder(\Magento\Framework\App\ResponseInterface::class)
            ->setMethods(['setRedirect'])
            ->getMockForAbstractClass();
        $sendquotes = $this->getMockBuilder(\Techspot\SendQuote\Model\ResourceModel\Sendquote\Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $loadedSendquote = $this->getMockBuilder(\Techspot\SendQuote\Model\Sendquote\Item::class)
            ->setMethods(['getId', 'delete'])
            ->disableOriginalConstructor()
            ->getMock();

        $eventObserver->expects($this->any())->method('getEvent')->willReturn($event);

        $request->expects($this->any())->method('getParam')->with('sendquote_next')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);

        $this->checkoutSession->expects($this->once())->method('getSharedSendquote');
        $this->checkoutSession->expects($this->once())->method('getSendquotePendingMessages')->willReturn([$message]);
        $this->checkoutSession->expects($this->once())->method('getSendquotePendingUrls')->willReturn([$url]);
        $this->checkoutSession->expects($this->once())->method('getSendquoteIds');
        $this->checkoutSession->expects($this->once())->method('getSingleSendquoteId')->willReturn($sendquoteId);

        $this->customerSession->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(true);
        $this->customerSession->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $this->sendquote->expects($this->once())
            ->method('loadByCustomerId')
            ->with($this->logicalOr($customerId, true))
            ->willReturnSelf();
        $this->sendquote->expects($this->once())
            ->method('getItemCollection')
            ->willReturn($sendquotes);
        $loadedSendquote->expects($this->once())
            ->method('getId')
            ->willReturn($sendquoteId);
        $loadedSendquote->expects($this->once())
            ->method('delete');
        $sendquotes->expects($this->once())
            ->method('load')
            ->willReturn([$loadedSendquote]);
        $this->checkoutSession->expects($this->once())
            ->method('setSendquoteIds')
            ->with([])
            ->willReturnSelf();
        $this->checkoutSession->expects($this->once())
            ->method('setSingleSendquoteId')
            ->with(null)
            ->willReturnSelf();
        $this->checkoutSession->expects($this->once())
            ->method('setSendquotePendingUrls')
            ->with([])
            ->willReturnSelf();
        $this->checkoutSession->expects($this->once())
            ->method('setSendquotePendingMessages')
            ->with([])
            ->willReturnSelf();
        $this->messageManager->expects($this->once())
            ->method('addError')
            ->with($message)
            ->willReturnSelf();
        $event->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);
        $response->expects($this->once())
            ->method('setRedirect')
            ->with($url);
        $this->checkoutSession->expects($this->once())
            ->method('setNoCartRedirect')
            ->with(true);

        /** @var $eventObserver \Magento\Framework\Event\Observer */
        $this->observer->execute($eventObserver);
    }
}
