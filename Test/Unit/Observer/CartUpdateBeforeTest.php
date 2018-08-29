<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Test\Unit\Observer;

use \Techspot\SendQuote\Observer\CartUpdateBefore as Observer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartUpdateBeforeTest extends \PHPUnit\Framework\TestCase
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
        $this->helper = $this->getMockBuilder(\Techspot\SendQuote\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sendquoteFactory = $this->getMockBuilder(\Techspot\SendQuote\Model\SendquoteFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->sendquote = $this->getMockBuilder(\Techspot\SendQuote\Model\Sendquote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->sendquoteFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->sendquote);

        $this->observer = new Observer(
            $this->helper,
            $this->sendquoteFactory
        );
    }

    /**
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecute()
    {
        $customerId = 1;
        $itemId = 5;
        $itemQty = 123;
        $productId = 321;

        $eventObserver = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder(\Magento\Framework\Event::class)
            ->setMethods(['getCart', 'getInfo'])
            ->disableOriginalConstructor()
            ->getMock();

        $eventObserver->expects($this->exactly(2))
            ->method('getEvent')
            ->willReturn($event);

        $quoteItem = $this->getMockBuilder(\Magento\Quote\Model\Quote\Item::class)
            ->setMethods(['getProductId', 'getBuyRequest', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $buyRequest = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['setQty'])
            ->disableOriginalConstructor()
            ->getMock();

        $infoData = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->setMethods(['toArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $infoData->expects($this->once())
            ->method('toArray')
            ->willReturn([$itemId => ['qty' => $itemQty, 'sendquote' => true]]);

        $cart = $this->getMockBuilder(\Magento\Checkout\Model\Cart::class)->disableOriginalConstructor()->getMock();
        $quote = $this->getMockBuilder(\Magento\Quote\Model\Quote::class)
            ->setMethods(['getCustomerId', 'getItemById', 'removeItem', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->once())
            ->method('getCart')
            ->willReturn($cart);

        $event->expects($this->once())
            ->method('getInfo')
            ->willReturn($infoData);

        $cart->expects($this->any())
            ->method('getQuote')
            ->willReturn($quote);

        $quoteItem->expects($this->once())
            ->method('getProductId')
            ->willReturn($productId);
        $quoteItem->expects($this->once())
            ->method('getBuyRequest')
            ->willReturn($buyRequest);

        $buyRequest->expects($this->once())
            ->method('setQty')
            ->with($itemQty)
            ->willReturnSelf();

        $quote->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $quote->expects($this->once())
            ->method('getItemById')
            ->with($itemId)
            ->willReturn($quoteItem);

        $quote->expects($this->once())
            ->method('removeItem')
            ->with($itemId);

        $this->sendquote->expects($this->once())
            ->method('loadByCustomerId')
            ->with($this->logicalOr($customerId, true))
            ->willReturnSelf();

        $this->sendquote->expects($this->once())
            ->method('addNewItem')
            ->with($this->logicalOr($productId, $buyRequest));

        $this->sendquote->expects($this->once())
            ->method('save');

        $this->helper->expects($this->once())
            ->method('calculate');

        /** @var $eventObserver \Magento\Framework\Event\Observer */
        $this->assertSame(
            $this->observer,
            $this->observer->execute($eventObserver)
        );
    }
}
