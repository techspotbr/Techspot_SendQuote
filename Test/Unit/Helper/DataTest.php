<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Test\Unit\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Registry;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Techspot\SendQuote\Controller\SendquoteProviderInterface;
use Techspot\SendQuote\Model\Item as SendquoteItem;
use Techspot\SendQuote\Model\Sendquote;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataTest extends \PHPUnit\Framework\TestCase
{
    /** @var  \Techspot\SendQuote\Helper\Data */
    protected $model;

    /** @var  SendquoteProviderInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $sendquoteProvider;

    /** @var  Registry |\PHPUnit_Framework_MockObject_MockObject */
    protected $coreRegistry;

    /** @var  PostHelper |\PHPUnit_Framework_MockObject_MockObject */
    protected $postDataHelper;

    /** @var  SendquoteItem |\PHPUnit_Framework_MockObject_MockObject */
    protected $sendquoteItem;

    /** @var  Product |\PHPUnit_Framework_MockObject_MockObject */
    protected $product;

    /** @var  StoreManagerInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var  Store |\PHPUnit_Framework_MockObject_MockObject */
    protected $store;

    /** @var  UrlInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $urlBuilder;

    /** @var  Sendquote |\PHPUnit_Framework_MockObject_MockObject */
    protected $sendquote;

    /** @var  EncoderInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlEncoderMock;

    /** @var  RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $requestMock;

    /** @var  Context |\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /**
     * Set up mock objects for tested class
     *
     * @return void
     */
    protected function setUp()
    {
        $this->store = $this->getMockBuilder(\Magento\Store\Model\Store::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($this->store);

        $this->urlEncoderMock = $this->getMockBuilder(\Magento\Framework\Url\EncoderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getServer'])
            ->getMockForAbstractClass();

        $this->urlBuilder = $this->getMockBuilder(\Magento\Framework\UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->context = $this->getMockBuilder(\Magento\Framework\App\Helper\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->expects($this->once())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilder);
        $this->context->expects($this->once())
            ->method('getUrlEncoder')
            ->willReturn($this->urlEncoderMock);
        $this->context->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->sendquoteProvider = $this->getMockBuilder(\Techspot\SendQuote\Controller\SendquoteProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->coreRegistry = $this->getMockBuilder(\Magento\Framework\Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->postDataHelper = $this->getMockBuilder(\Magento\Framework\Data\Helper\PostHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sendquoteItem = $this->getMockBuilder(\Techspot\SendQuote\Model\Item::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getProduct',
                'getSendquoteItemId',
                'getQty',
            ])
            ->getMock();

        $this->sendquote = $this->getMockBuilder(\Techspot\SendQuote\Model\Sendquote::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            \Techspot\SendQuote\Helper\Data::class,
            [
                'context' => $this->context,
                'storeManager' => $this->storeManager,
                'sendquoteProvider' => $this->sendquoteProvider,
                'coreRegistry' => $this->coreRegistry,
                'postDataHelper' => $this->postDataHelper
            ]
        );
    }

    public function testGetAddToCartUrl()
    {
        $url = 'http://magento.com/sendquote/index/index/sendquote_id/1/?___store=default';

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('sendquote/index/cart', ['item' => '%item%'])
            ->will($this->returnValue($url));

        $this->urlBuilder->expects($this->any())
            ->method('getUrl')
            ->with('sendquote/index/index', ['_current' => true, '_use_rewrite' => true, '_scope_to_url' => true])
            ->will($this->returnValue($url));

        $this->assertEquals($url, $this->model->getAddToCartUrl('%item%'));
    }

    public function testGetConfigureUrl()
    {
        $url = 'http://magento2ce/sendquote/index/configure/id/4/product_id/30/';

        /** @var \Techspot\SendQuote\Model\Item|\PHPUnit_Framework_MockObject_MockObject $sendquoteItem */
        $sendquoteItem = $this->createPartialMock(
            \Techspot\SendQuote\Model\Item::class,
            ['getSendquoteItemId', 'getProductId']
        );
        $sendquoteItem
            ->expects($this->once())
            ->method('getSendquoteItemId')
            ->will($this->returnValue(4));
        $sendquoteItem
            ->expects($this->once())
            ->method('getProductId')
            ->will($this->returnValue(30));

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('sendquote/index/configure', ['id' => 4, 'product_id' => 30])
            ->will($this->returnValue($url));

        $this->assertEquals($url, $this->model->getConfigureUrl($sendquoteItem));
    }

    public function testGetSendquote()
    {
        $this->sendquoteProvider->expects($this->once())
            ->method('getSendquote')
            ->will($this->returnValue($this->sendquote));

        $this->assertEquals($this->sendquote, $this->model->getSendquote());
    }

    public function testGetSendquoteWithCoreRegistry()
    {
        $this->coreRegistry->expects($this->any())
            ->method('registry')
            ->willReturn($this->sendquote);

        $this->assertEquals($this->sendquote, $this->model->getSendquote());
    }

    public function testGetAddToCartParams()
    {
        $url = 'result url';
        $storeId = 1;
        $sendquoteItemId = 1;
        $sendquoteItemQty = 1;

        $this->sendquoteItem->expects($this->once())
            ->method('getProduct')
            ->willReturn($this->product);
        $this->sendquoteItem->expects($this->once())
            ->method('getSendquoteItemId')
            ->willReturn($sendquoteItemId);
        $this->sendquoteItem->expects($this->once())
            ->method('getQty')
            ->willReturn($sendquoteItemQty);

        $this->product->expects($this->once())
            ->method('isVisibleInSiteVisibility')
            ->willReturn(true);
        $this->product->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->requestMock->expects($this->never())
            ->method('getServer');

        $this->urlEncoderMock->expects($this->never())
            ->method('encode');

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('sendquote/index/cart')
            ->willReturn($url);

        $expected = [
            'item' => $sendquoteItemId,
            'qty' => $sendquoteItemQty,
        ];
        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, $expected)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getAddToCartParams($this->sendquoteItem));
    }

    public function testGetAddToCartParamsWithReferer()
    {
        $url = 'result url';
        $storeId = 1;
        $sendquoteItemId = 1;
        $sendquoteItemQty = 1;
        $referer = 'referer';
        $refererEncoded = 'referer_encoded';

        $this->sendquoteItem->expects($this->once())
            ->method('getProduct')
            ->willReturn($this->product);
        $this->sendquoteItem->expects($this->once())
            ->method('getSendquoteItemId')
            ->willReturn($sendquoteItemId);
        $this->sendquoteItem->expects($this->once())
            ->method('getQty')
            ->willReturn($sendquoteItemQty);

        $this->product->expects($this->once())
            ->method('isVisibleInSiteVisibility')
            ->willReturn(true);
        $this->product->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->requestMock->expects($this->once())
            ->method('getServer')
            ->with('HTTP_REFERER')
            ->willReturn($referer);

        $this->urlEncoderMock->expects($this->once())
            ->method('encode')
            ->with($referer)
            ->willReturn($refererEncoded);

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('sendquote/index/cart')
            ->willReturn($url);

        $expected = [
            'item' => $sendquoteItemId,
            ActionInterface::PARAM_NAME_URL_ENCODED => $refererEncoded,
            'qty' => $sendquoteItemQty,
        ];
        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, $expected)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getAddToCartParams($this->sendquoteItem, true));
    }

    public function testGetRemoveParams()
    {
        $url = 'result url';
        $sendquoteItemId = 1;

        $this->sendquoteItem->expects($this->once())
            ->method('getSendquoteItemId')
            ->willReturn($sendquoteItemId);

        $this->requestMock->expects($this->never())
            ->method('getServer');

        $this->urlEncoderMock->expects($this->never())
            ->method('encode');

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('sendquote/index/remove', [])
            ->willReturn($url);

        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, ['item' => $sendquoteItemId])
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getRemoveParams($this->sendquoteItem));
    }

    public function testGetRemoveParamsWithReferer()
    {
        $url = 'result url';
        $sendquoteItemId = 1;
        $referer = 'referer';
        $refererEncoded = 'referer_encoded';

        $this->sendquoteItem->expects($this->once())
            ->method('getSendquoteItemId')
            ->willReturn($sendquoteItemId);

        $this->requestMock->expects($this->once())
            ->method('getServer')
            ->with('HTTP_REFERER')
            ->willReturn($referer);

        $this->urlEncoderMock->expects($this->once())
            ->method('encode')
            ->with($referer)
            ->willReturn($refererEncoded);

        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with('sendquote/index/remove', [])
            ->willReturn($url);

        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, ['item' => $sendquoteItemId, ActionInterface::PARAM_NAME_URL_ENCODED => $refererEncoded])
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getRemoveParams($this->sendquoteItem, true));
    }

    public function testGetSharedAddToCartUrl()
    {
        $url = 'result url';
        $storeId = 1;
        $sendquoteItemId = 1;
        $sendquoteItemQty = 1;

        $this->sendquoteItem->expects($this->once())
            ->method('getProduct')
            ->willReturn($this->product);
        $this->sendquoteItem->expects($this->once())
            ->method('getSendquoteItemId')
            ->willReturn($sendquoteItemId);
        $this->sendquoteItem->expects($this->once())
            ->method('getQty')
            ->willReturn($sendquoteItemQty);

        $this->product->expects($this->once())
            ->method('isVisibleInSiteVisibility')
            ->willReturn(true);
        $this->product->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('sendquote/shared/cart')
            ->willReturn($url);

        $exptected = [
            'item' => $sendquoteItemId,
            'qty' => $sendquoteItemQty,
        ];
        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url, $exptected)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getSharedAddToCartUrl($this->sendquoteItem));
    }

    public function testGetSharedAddAllToCartUrl()
    {
        $url = 'result url';

        $this->store->expects($this->once())
            ->method('getUrl')
            ->with('*/*/allcart', ['_current' => true])
            ->willReturn($url);

        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($url)
            ->willReturn($url);

        $this->assertEquals($url, $this->model->getSharedAddAllToCartUrl());
    }
}
