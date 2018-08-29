<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Test\Unit\Model;

use Techspot\SendQuote\Model\Sendquote;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class SendquoteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Helper\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productHelper;

    /**
     * @var \Techspot\SendQuote\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helper;

    /**
     * @var \Techspot\SendQuote\Model\ResourceModel\Sendquote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \Techspot\SendQuote\Model\ResourceModel\Sendquote\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $collection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $date;

    /**
     * @var \Techspot\SendQuote\Model\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemFactory;

    /**
     * @var \Techspot\SendQuote\Model\ResourceModel\Item\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Math\Random|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mathRandom;

    /**
     * @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcher;

    /**
     * @var Sendquote
     */
    protected $sendquote;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $serializer;

    protected function setUp()
    {
        $context = $this->getMockBuilder(\Magento\Framework\Model\Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventDispatcher = $this->getMockBuilder(\Magento\Framework\Event\ManagerInterface::class)
            ->getMock();
        $this->registry = $this->getMockBuilder(\Magento\Framework\Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productHelper = $this->getMockBuilder(\Magento\Catalog\Helper\Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->helper = $this->getMockBuilder(\Techspot\SendQuote\Helper\Data::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource = $this->getMockBuilder(\Techspot\SendQuote\Model\ResourceModel\Sendquote::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->collection = $this->getMockBuilder(\Techspot\SendQuote\Model\ResourceModel\Sendquote\Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->getMock();
        $this->date = $this->getMockBuilder(\Magento\Framework\Stdlib\DateTime\DateTime::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->itemFactory = $this->getMockBuilder(\Techspot\SendQuote\Model\ItemFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->itemsFactory = $this->getMockBuilder(\Techspot\SendQuote\Model\ResourceModel\Item\CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->productFactory = $this->getMockBuilder(\Magento\Catalog\Model\ProductFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->mathRandom = $this->getMockBuilder(\Magento\Framework\Math\Random::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dateTime = $this->getMockBuilder(\Magento\Framework\Stdlib\DateTime::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productRepository = $this->createMock(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->serializer = $this->getMockBuilder(\Magento\Framework\Serialize\Serializer\Json::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())
            ->method('getEventDispatcher')
            ->will($this->returnValue($this->eventDispatcher));

        $this->sendquote = new Sendquote(
            $context,
            $this->registry,
            $this->productHelper,
            $this->helper,
            $this->resource,
            $this->collection,
            $this->storeManager,
            $this->date,
            $this->itemFactory,
            $this->itemsFactory,
            $this->productFactory,
            $this->mathRandom,
            $this->dateTime,
            $this->productRepository,
            false,
            [],
            $this->serializer
        );
    }

    public function testLoadByCustomerId()
    {
        $customerId = 1;
        $customerIdFieldName = 'customer_id';
        $sharingCode = 'expected_sharing_code';
        $this->eventDispatcher->expects($this->any())
            ->method('dispatch');
        $this->resource->expects($this->any())
            ->method('getCustomerIdFieldName');
        $this->resource->expects($this->once())
            ->method('load')
            ->with($this->logicalOr($this->sendquote, $customerId, $customerIdFieldName));
        $this->mathRandom->expects($this->once())
            ->method('getUniqueHash')
            ->will($this->returnValue($sharingCode));

        $this->assertInstanceOf(
            \Techspot\SendQuote\Model\Sendquote::class,
            $this->sendquote->loadByCustomerId($customerId, true)
        );
        $this->assertEquals($customerId, $this->sendquote->getCustomerId());
        $this->assertEquals($sharingCode, $this->sendquote->getSharingCode());
    }

    /**
     * @param int|\Techspot\SendQuote\Model\Item|\PHPUnit_Framework_MockObject_MockObject $itemId
     * @param \Magento\Framework\DataObject $buyRequest
     * @param null|array|\Magento\Framework\DataObject $param
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @dataProvider updateItemDataProvider
     */
    public function testUpdateItem($itemId, $buyRequest, $param)
    {
        $storeId = 1;
        $productId = 1;
        $stores = [(new \Magento\Framework\DataObject())->setId($storeId)];

        $newItem = $this->getMockBuilder(\Techspot\SendQuote\Model\Item::class)
            ->setMethods(
                ['setProductId', 'setSendquoteId', 'setStoreId', 'setOptions', 'setProduct', 'setQty', 'getItem', 'save']
            )
            ->disableOriginalConstructor()
            ->getMock();
        $newItem->expects($this->any())->method('setProductId')->will($this->returnSelf());
        $newItem->expects($this->any())->method('setSendquoteId')->will($this->returnSelf());
        $newItem->expects($this->any())->method('setStoreId')->will($this->returnSelf());
        $newItem->expects($this->any())->method('setOptions')->will($this->returnSelf());
        $newItem->expects($this->any())->method('setProduct')->will($this->returnSelf());
        $newItem->expects($this->any())->method('setQty')->will($this->returnSelf());
        $newItem->expects($this->any())->method('getItem')->will($this->returnValue(2));
        $newItem->expects($this->any())->method('save')->will($this->returnSelf());

        $this->itemFactory->expects($this->once())->method('create')->will($this->returnValue($newItem));

        $this->storeManager->expects($this->any())->method('getStores')->will($this->returnValue($stores));
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($stores[0]));

        $product = $this->getMockBuilder(
            \Magento\Catalog\Model\Product::class
        )->disableOriginalConstructor()->getMock();
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $product->expects($this->any())->method('getStoreId')->will($this->returnValue($storeId));

        $instanceType = $this->getMockBuilder(\Magento\Catalog\Model\Product\Type\AbstractType::class)
            ->disableOriginalConstructor()
            ->getMock();
        $instanceType->expects($this->once())
            ->method('processConfiguration')
            ->will(
                $this->returnValue(
                    $this->getMockBuilder(
                        \Magento\Catalog\Model\Product::class
                    )->disableOriginalConstructor()->getMock()
                )
            );

        $newProduct = $this->getMockBuilder(
            \Magento\Catalog\Model\Product::class
        )->disableOriginalConstructor()->getMock();
        $newProduct->expects($this->any())
            ->method('setStoreId')
            ->with($storeId)
            ->will($this->returnSelf());
        $newProduct->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($instanceType));

        $item = $this->getMockBuilder(\Techspot\SendQuote\Model\Item::class)->disableOriginalConstructor()->getMock();
        $item->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $items = $this->getMockBuilder(\Techspot\SendQuote\Model\ResourceModel\Item\Collection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $items->expects($this->once())
            ->method('addSendquoteFilter')
            ->will($this->returnSelf());
        $items->expects($this->once())
            ->method('addStoreFilter')
            ->will($this->returnSelf());
        $items->expects($this->once())
            ->method('setVisibilityFilter')
            ->will($this->returnSelf());
        $items->expects($this->once())
            ->method('getItemById')
            ->will($this->returnValue($item));
        $items->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator([$item])));

        $this->itemsFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($items));

        $this->productRepository->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId)
            ->will($this->returnValue($newProduct));

        $this->assertInstanceOf(
            \Techspot\SendQuote\Model\Sendquote::class,
            $this->sendquote->updateItem($itemId, $buyRequest, $param)
        );
    }

    /**
     * @return array
     */
    public function updateItemDataProvider()
    {
        return [
            '0' => [1, new \Magento\Framework\DataObject(), null]
        ];
    }

    public function testAddNewItem()
    {
        $productId = 1;
        $storeId = 1;
        $buyRequest = json_encode([
            'number' => 42,
            'string' => 'string_value',
            'boolean' => true,
            'collection' => [1, 2, 3],
            'product' => 1,
            'form_key' => 'abc'
        ]);
        $result = 'product';

        $instanceType = $this->getMockBuilder(\Magento\Catalog\Model\Product\Type\AbstractType::class)
            ->disableOriginalConstructor()
            ->getMock();
        $instanceType->expects($this->once())
            ->method('processConfiguration')
            ->willReturn('product');

        $productMock = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['getId', 'hasSendquoteStoreId', 'getStoreId', 'getTypeInstance'])
            ->getMock();
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn($productId);
        $productMock->expects($this->once())
            ->method('hasSendquoteStoreId')
            ->willReturn(false);
        $productMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($instanceType);

        $this->productRepository->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId)
            ->willReturn($productMock);

        $this->serializer->expects($this->once())
            ->method('unserialize')
            ->willReturnCallback(
                function ($value) {
                    return json_decode($value, true);
                }
            );

        $this->assertEquals($result, $this->sendquote->addNewItem($productMock, $buyRequest));
    }
}
