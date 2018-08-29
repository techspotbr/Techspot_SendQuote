<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Test\Unit\Block\Cart\Item\Renderer\Actions;

use Techspot\SendQuote\Block\Cart\Item\Renderer\Actions\MoveToSendquote;
use Magento\Quote\Model\Quote\Item;
use Techspot\SendQuote\Helper\Data;

class MoveToSendquoteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MoveToSendquote
     */
    protected $model;

    /** @var Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $sendquoteHelperMock;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->sendquoteHelperMock = $this->getMockBuilder(\Techspot\SendQuote\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManagerHelper->getObject(
            \Techspot\SendQuote\Block\Cart\Item\Renderer\Actions\MoveToSendquote::class,
            [
                'sendquoteHelper' => $this->sendquoteHelperMock,
            ]
        );
    }

    public function testIsAllowInCart()
    {
        $this->sendquoteHelperMock->expects($this->once())
            ->method('isAllowInCart')
            ->willReturn(true);

        $this->assertTrue($this->model->isAllowInCart());
    }

    public function testGetMoveFromCartParams()
    {
        $itemId = 45;
        $json = '{json;}';

        /**
         * @var Item|\PHPUnit_Framework_MockObject_MockObject $itemMock
         */
        $itemMock = $this->getMockBuilder(\Magento\Quote\Model\Quote\Item::class)
            ->disableOriginalConstructor()
            ->getMock();

        $itemMock->expects($this->once())
            ->method('getId')
            ->willReturn($itemId);

        $this->sendquoteHelperMock->expects($this->once())
            ->method('getMoveFromCartParams')
            ->with($itemId)
            ->willReturn($json);

        $this->model->setItem($itemMock);
        $this->assertEquals($json, $this->model->getMoveFromCartParams());
    }
}
