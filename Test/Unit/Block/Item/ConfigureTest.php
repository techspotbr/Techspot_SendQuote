<?php
/**
 * \Techspot\SendQuote\Block\Item\Configure
 *
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Test\Unit\Block\Item;

class ConfigureTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Techspot\SendQuote\Block\Item\Configure
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendquoteDataMock;

    protected function setUp()
    {
        $this->sendquoteDataMock = $this->getMockBuilder(
            \Techspot\SendQuote\Helper\Data::class
        )->disableOriginalConstructor()->getMock();
        $this->contextMock = $this->getMockBuilder(
            \Magento\Framework\View\Element\Template\Context::class
        )->disableOriginalConstructor()->getMock();
        $this->registryMock = $this->getMockBuilder(\Magento\Framework\Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escaperMock = $this->getMockBuilder(\Magento\Framework\Escaper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escaperMock->method('escapeHtml')
            ->willReturnCallback(
                function ($string) {
                    return 'escapeHtml' . $string;
                }
            );
        $this->contextMock->expects($this->once())
            ->method('getEscaper')
            ->willReturn($escaperMock);

        $this->model = new \Techspot\SendQuote\Block\Item\Configure(
            $this->contextMock,
            $this->sendquoteDataMock,
            $this->registryMock
        );
    }

    public function testGetSendquoteOptions()
    {
        $typeId = 'simple';
        $product = $this->getMockBuilder(
            \Magento\Catalog\Model\Product::class
        )->disableOriginalConstructor()->getMock();
        $product->expects($this->once())->method('getTypeId')->willReturn($typeId);
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with($this->equalTo('product'))
            ->willReturn($product);

        $this->assertEquals(['productType' => 'escapeHtml' . $typeId], $this->model->getSendquoteOptions());
    }

    public function testGetProduct()
    {
        $product = 'some test product';
        $this->registryMock->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            $this->equalTo('product')
        )->willReturn(
            $product
        );

        $this->assertEquals($product, $this->model->getProduct());
    }

    public function testSetLayout()
    {
        $layoutMock = $this->createMock(\Magento\Framework\View\LayoutInterface::class);

        $blockMock = $this->createPartialMock(
            \Magento\Framework\View\Element\AbstractBlock::class,
            ['setCustomAddToCartUrl']
        );
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.info')
            ->will($this->returnValue($blockMock));

        $itemMock = $this->createMock(\Techspot\SendQuote\Model\Item::class);

        $this->registryMock->expects($this->exactly(2))
            ->method('registry')
            ->with('sendquote_item')
            ->willReturn($itemMock);

        $this->sendquoteDataMock->expects($this->once())
            ->method('getAddToCartUrl')
            ->with($itemMock)
            ->willReturn('some_url');

        $blockMock->expects($this->once())
            ->method('setCustomAddToCartUrl')
            ->with('some_url');

        $this->assertEquals($this->model, $this->model->setLayout($layoutMock));
        $this->assertEquals($layoutMock, $this->model->getLayout());
    }

    public function testSetLayoutWithNoItem()
    {
        $layoutMock = $this->createMock(\Magento\Framework\View\LayoutInterface::class);

        $blockMock = $this->createPartialMock(
            \Magento\Framework\View\Element\AbstractBlock::class,
            ['setCustomAddToCartUrl']
        );
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.info')
            ->willReturn($blockMock);

        $this->registryMock->expects($this->exactly(1))
            ->method('registry')
            ->with('sendquote_item')
            ->willReturn(null);

        $this->sendquoteDataMock->expects($this->never())
            ->method('getAddToCartUrl');

        $blockMock->expects($this->never())
            ->method('setCustomAddToCartUrl');

        $this->assertEquals($this->model, $this->model->setLayout($layoutMock));
        $this->assertEquals($layoutMock, $this->model->getLayout());
    }

    public function testSetLayoutWithNoBlockAndItem()
    {
        $layoutMock = $this->createMock(\Magento\Framework\View\LayoutInterface::class);

        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.info')
            ->willReturn(null);

        $this->registryMock->expects($this->never())
            ->method('registry');

        $this->sendquoteDataMock->expects($this->never())
            ->method('getAddToCartUrl');

        $this->assertEquals($this->model, $this->model->setLayout($layoutMock));
        $this->assertEquals($layoutMock, $this->model->getLayout());
    }
}
