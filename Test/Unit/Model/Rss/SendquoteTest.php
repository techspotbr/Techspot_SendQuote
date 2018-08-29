<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Techspot\SendQuote\Test\Unit\Model\Rss;

use Magento\Directory\Helper\Data;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SendquoteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Techspot\SendQuote\Model\Rss\Sendquote
     */
    protected $model;

    /**
     * @var \Techspot\SendQuote\Block\Customer\Sendquote
     */
    protected $sendquoteBlock;

    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $rssFactoryMock;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilderMock;

    /**
     * @var \Techspot\SendQuote\Helper\Rss
     */
    protected $sendquoteHelperMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelperMock;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $catalogOutputMock;

    /**
     * @var \Magento\Catalog\Helper\Output|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * Set up mock objects for tested class
     *
     * @return void
     */
    protected function setUp()
    {
        $this->catalogOutputMock = $this->createMock(\Magento\Catalog\Helper\Output::class);
        $this->rssFactoryMock = $this->createPartialMock(\Magento\Rss\Model\RssFactory::class, ['create']);
        $this->sendquoteBlock = $this->createMock(\Techspot\SendQuote\Block\Customer\Sendquote::class);
        $this->sendquoteHelperMock = $this->createPartialMock(
            \Techspot\SendQuote\Helper\Rss::class,
            ['getSendquote', 'getCustomer', 'getCustomerName']
        );
        $this->urlBuilderMock = $this->getMockForAbstractClass(\Magento\Framework\UrlInterface::class);
        $this->scopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);

        $this->imageHelperMock = $this->createMock(\Magento\Catalog\Helper\Image::class);

        $this->layoutMock = $this->getMockForAbstractClass(
            \Magento\Framework\View\LayoutInterface::class,
            [],
            '',
            true,
            true,
            true,
            ['getBlock']
        );

        $this->customerFactory = $this->getMockBuilder(\Magento\Customer\Model\CustomerFactory::class)
            ->setMethods(['create'])->disableOriginalConstructor()->getMock();

        $requestMock = $this->createMock(\Magento\Framework\App\RequestInterface::class);
        $requestMock->expects($this->any())->method('getParam')->with('sharing_code')
            ->will($this->returnValue('somesharingcode'));

        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            \Techspot\SendQuote\Model\Rss\Sendquote::class,
            [
                'sendquoteHelper' => $this->sendquoteHelperMock,
                'sendquoteBlock' => $this->sendquoteBlock,
                'outputHelper' => $this->catalogOutputMock,
                'imageHelper' => $this->imageHelperMock,
                'urlBuilder' => $this->urlBuilderMock,
                'scopeConfig' => $this->scopeConfig,
                'rssFactory' => $this->rssFactoryMock,
                'layout' => $this->layoutMock,
                'request' => $requestMock,
                'customerFactory' => $this->customerFactory
            ]
        );
    }

    public function testGetRssData()
    {
        $sendquoteId = 1;
        $customerName = 'Customer Name';
        $title = "$customerName's Sendquote";
        $sendquoteModelMock = $this->createPartialMock(
            \Techspot\SendQuote\Model\Sendquote::class,
            ['getId', '__wakeup', 'getCustomerId', 'getItemCollection', 'getSharingCode']
        );
        $customerServiceMock = $this->createMock(\Magento\Customer\Api\Data\CustomerInterface::class);
        $sendquoteSharingUrl = 'sendquote/shared/index/1';
        $locale = 'en_US';
        $productUrl = 'http://product.url/';
        $productName = 'Product name';

        $customer = $this->getMockBuilder(\Magento\Customer\Model\Customer::class)
            ->setMethods(['getName', '__wakeup', 'load'])
            ->disableOriginalConstructor()->getMock();
        $customer->expects($this->once())->method('load')->will($this->returnSelf());
        $customer->expects($this->once())->method('getName')->will($this->returnValue('Customer Name'));

        $this->customerFactory->expects($this->once())->method('create')->will($this->returnValue($customer));

        $this->sendquoteHelperMock->expects($this->any())
            ->method('getSendquote')
            ->will($this->returnValue($sendquoteModelMock));
        $this->sendquoteHelperMock->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customerServiceMock));
        $sendquoteModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($sendquoteId));
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($sendquoteSharingUrl));
        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->will(
                $this->returnValueMap(
                    [
                        [
                            'advanced/modules_disable_output/Magento_Rss',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                            null,
                            null,
                        ],
                        [
                            Data::XML_PATH_DEFAULT_LOCALE,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                            null,
                            $locale
                        ],
                    ]
                )
            );

        $staticArgs = [
            'productName' => $productName,
            'productUrl' => $productUrl,
        ];
        $description = $this->processSendquoteItemDescription($sendquoteModelMock, $staticArgs);

        $expectedResult = [
            'title' => $title,
            'description' => $title,
            'link' => $sendquoteSharingUrl,
            'charset' => 'UTF-8',
            'entries' => [
                0 => [
                    'title' => $productName,
                    'link' => $productUrl,
                    'description' => $description,
                ],
            ],
        ];

        $this->assertEquals($expectedResult, $this->model->getRssData());
    }

    /**
     * Additional function to process forming description for sendquote item
     *
     * @param \Techspot\SendQuote\Model\Sendquote $sendquoteModelMock
     * @param array $staticArgs
     * @return string
     */
    protected function processSendquoteItemDescription($sendquoteModelMock, $staticArgs)
    {
        $imgThumbSrc = 'http://source-for-thumbnail';
        $priceHtmlForTest = '<div class="price">Price is 10 for example</div>';
        $productDescription = 'Product description';
        $productShortDescription = 'Product short description';

        $sendquoteItem = $this->createMock(\Techspot\SendQuote\Model\Item::class);
        $sendquoteItemsCollection = [
            $sendquoteItem,
        ];
        $productMock = $this->createPartialMock(\Magento\Catalog\Model\Product::class, [
                'getAllowedInRss',
                'getAllowedPriceInRss',
                'getDescription',
                'getShortDescription',
                'getName',
                '__wakeup'
            ]);

        $sendquoteModelMock->expects($this->once())
            ->method('getItemCollection')
            ->will($this->returnValue($sendquoteItemsCollection));
        $sendquoteItem->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($productMock));
        $productMock->expects($this->once())
            ->method('getAllowedPriceInRss')
            ->will($this->returnValue(true));
        $productMock->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($staticArgs['productName']));
        $productMock->expects($this->once())
            ->method('getAllowedInRss')
            ->will($this->returnValue(true));
        $this->imageHelperMock->expects($this->once())
            ->method('init')
            ->with($productMock, 'rss_thumbnail')
            ->will($this->returnSelf());
        $this->imageHelperMock->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($imgThumbSrc));
        $priceRendererMock = $this->createPartialMock(\Magento\Framework\Pricing\Render::class, ['render']);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->will($this->returnValue($priceRendererMock));
        $priceRendererMock->expects($this->once())
            ->method('render')
            ->will($this->returnValue($priceHtmlForTest));
        $productMock->expects($this->any())
            ->method('getDescription')
            ->will($this->returnValue($productDescription));
        $productMock->expects($this->any())
            ->method('getShortDescription')
            ->will($this->returnValue($productShortDescription));
        $this->catalogOutputMock->expects($this->any())
            ->method('productAttribute')
            ->will($this->returnArgument(1));
        $this->sendquoteBlock
            ->expects($this->any())
            ->method('getProductUrl')
            ->with($productMock, ['_rss' => true])
            ->will($this->returnValue($staticArgs['productUrl']));

        $description = '<table><tr><td><a href="' . $staticArgs['productUrl'] . '"><img src="' . $imgThumbSrc .
            '" border="0" align="left" height="75" width="75"></a></td><td style="text-decoration:none;">' .
            $productShortDescription . '<p>' . $priceHtmlForTest . '</p><p>Comment: ' . $productDescription . '<p>' .
            '</td></tr></table>';

        return $description;
    }

    public function testIsAllowed()
    {
        $this->scopeConfig->expects($this->once())->method('getValue')
            ->with('rss/sendquote/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
            ->will($this->returnValue(true));
        $this->assertTrue($this->model->isAllowed());
    }

    public function testGetCacheKey()
    {
        $this->assertEquals('rss_sendquote_data', $this->model->getCacheKey());
    }

    public function testGetCacheLifetime()
    {
        $this->assertEquals(60, $this->model->getCacheLifetime());
    }

    public function testIsAuthRequired()
    {
        $sendquote = $this->getMockBuilder(\Techspot\SendQuote\Model\Sendquote::class)->setMethods(
            ['getId', '__wakeup', 'getCustomerId', 'getItemCollection', 'getSharingCode']
        )->disableOriginalConstructor()->getMock();
        $sendquote->expects($this->any())->method('getSharingCode')
            ->will($this->returnValue('somesharingcode'));
        $this->sendquoteHelperMock->expects($this->any())->method('getSendquote')
            ->will($this->returnValue($sendquote));
        $this->assertEquals(false, $this->model->isAuthRequired());
    }

    public function testGetProductPriceHtmlBlockDoesntExists()
    {
        $price = 10.;

        $productMock = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        $renderBlockMock = $this->getMockBuilder(\Magento\Framework\Pricing\Render::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderBlockMock->expects($this->once())
            ->method('render')
            ->with(
                'sendquote_configured_price',
                $productMock,
                ['zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST]
            )
            ->willReturn($price);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->willReturn(false);
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(
                \Magento\Framework\Pricing\Render::class,
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            )
            ->willReturn($renderBlockMock);

        $this->assertEquals($price, $this->model->getProductPriceHtml($productMock));
    }

    public function testGetProductPriceHtmlBlockExists()
    {
        $price = 10.;

        $productMock = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        $renderBlockMock = $this->getMockBuilder(\Magento\Framework\Pricing\Render::class)
            ->disableOriginalConstructor()
            ->getMock();
        $renderBlockMock->expects($this->once())
            ->method('render')
            ->with(
                'sendquote_configured_price',
                $productMock,
                ['zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST]
            )
            ->willReturn($price);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->willReturn($renderBlockMock);

        $this->assertEquals($price, $this->model->getProductPriceHtml($productMock));
    }
}
