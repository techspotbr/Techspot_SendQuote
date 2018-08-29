<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Test\Unit\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateItemOptionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productRepository;

    /**
     * @var \Techspot\SendQuote\Controller\SendquoteProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sendquoteProvider;

    /**
     * @var \Magento\Framework\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Request\Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $om;

    /**
     * @var \Magento\Framework\Message\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $url;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Event\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\Controller\ResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Framework\Controller\Result\Redirect|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $formKeyValidator;

    /**
     * SetUp method
     *
     * @return void
     */
    protected function setUp()
    {
        $this->productRepository = $this->createMock(\Magento\Catalog\Model\ProductRepository::class);
        $this->context = $this->createMock(\Magento\Framework\App\Action\Context::class);
        $this->request = $this->createMock(\Magento\Framework\App\Request\Http::class);
        $this->sendquoteProvider = $this->createMock(\Techspot\SendQuote\Controller\SendquoteProvider::class);
        $this->om = $this->createMock(\Magento\Framework\App\ObjectManager::class);
        $this->messageManager = $this->createMock(\Magento\Framework\Message\Manager::class);
        $this->url = $this->createMock(\Magento\Framework\Url::class);
        $this->customerSession = $this->createMock(\Magento\Customer\Model\Session::class);
        $this->eventManager = $this->createMock(\Magento\Framework\Event\Manager::class);
        $this->resultFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultRedirectMock = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->resultRedirectMock);

        $this->formKeyValidator = $this->getMockBuilder(\Magento\Framework\Data\Form\FormKey\Validator::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * TearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset(
            $this->productRepository,
            $this->context,
            $this->request,
            $this->sendquoteProvider,
            $this->om,
            $this->messageManager,
            $this->url,
            $this->eventManager
        );
    }

    /**
     * Prepare context
     *
     * @return void
     */
    public function prepareContext()
    {
        $actionFlag = $this->createMock(\Magento\Framework\App\ActionFlag::class);

        $this->context
            ->expects($this->any())
            ->method('getObjectManager')
            ->willReturn($this->om);
        $this->context
            ->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->context
            ->expects($this->any())
            ->method('getEventManager')
            ->willReturn($this->eventManager);
        $this->context
            ->expects($this->any())
            ->method('getUrl')
            ->willReturn($this->url);
        $this->context
            ->expects($this->any())
            ->method('getActionFlag')
            ->willReturn($actionFlag);
        $this->context
            ->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManager);
        $this->context->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactoryMock);
    }

    /**
     * Get controller
     *
     * @return \Techspot\SendQuote\Controller\Index\UpdateItemOptions
     */
    protected function getController()
    {
        $this->prepareContext();

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(true);

        return new \Techspot\SendQuote\Controller\Index\UpdateItemOptions(
            $this->context,
            $this->customerSession,
            $this->sendquoteProvider,
            $this->productRepository,
            $this->formKeyValidator
        );
    }

    public function testExecuteWithInvalidFormKey()
    {
        $this->prepareContext();

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(false);

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $controller = new \Techspot\SendQuote\Controller\Index\Remove(
            $this->context,
            $this->sendquoteProvider,
            $this->formKeyValidator
        );

        $this->assertSame($this->resultRedirectMock, $controller->execute());
    }

    /**
     * Test execute without product id
     *
     * @return void
     */
    public function testExecuteWithoutProductId()
    {
        $this->request
            ->expects($this->once())
            ->method('getParam')
            ->with('product')
            ->willReturn(null);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/', [])
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->getController()->execute());
    }

    /**
     * Test execute without product
     *
     * @return void
     */
    public function testExecuteWithoutProduct()
    {
        $this->request
            ->expects($this->once())
            ->method('getParam')
            ->with('product')
            ->willReturn(2);

        $this->productRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willThrowException(new \Magento\Framework\Exception\NoSuchEntityException());

        $this->messageManager
            ->expects($this->once())
            ->method('addError')
            ->with('We can\'t specify a product.')
            ->willReturn(true);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/', [])
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->getController()->execute());
    }

    /**
     * Test execute without quote list
     *
     * @return void
     */
    public function testExecuteWithoutWishList()
    {
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $item = $this->createMock(\Techspot\SendQuote\Model\Item::class);

        $product
            ->expects($this->once())
            ->method('isVisibleInCatalog')
            ->willReturn(true);

        $this->request
            ->expects($this->at(0))
            ->method('getParam')
            ->with('product', null)
            ->willReturn(2);
        $this->request
            ->expects($this->at(1))
            ->method('getParam')
            ->with('id', null)
            ->willReturn(3);

        $this->productRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($product);

        $this->messageManager
            ->expects($this->never())
            ->method('addError')
            ->with('We can\'t specify a product.')
            ->willReturn(true);

        $item
            ->expects($this->once())
            ->method('load')
            ->with(3)
            ->willReturnSelf();
        $item
            ->expects($this->once())
            ->method('__call')
            ->with('getSendquoteId')
            ->willReturn(12);

        $this->sendquoteProvider
            ->expects($this->once())
            ->method('getSendquote')
            ->with(12)
            ->willReturn(null);

        $this->om
            ->expects($this->once())
            ->method('create')
            ->with(\Techspot\SendQuote\Model\Item::class)
            ->willReturn($item);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/', [])
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->getController()->execute());
    }

    /**
     * Test execute add success exception
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecuteAddSuccessException()
    {
        $sendquote = $this->createMock(\Techspot\SendQuote\Model\Sendquote::class);
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $item = $this->createMock(\Techspot\SendQuote\Model\Item::class);
        $helper = $this->createMock(\Techspot\SendQuote\Helper\Data::class);

        $helper
            ->expects($this->exactly(2))
            ->method('calculate')
            ->willReturn(true);

        $sendquote
            ->expects($this->once())
            ->method('getItem')
            ->with(3)
            ->willReturn($item);
        $sendquote
            ->expects($this->once())
            ->method('updateItem')
            ->with(3, new \Magento\Framework\DataObject([]))
            ->willReturnSelf();
        $sendquote
            ->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $sendquote
            ->expects($this->once())
            ->method('getId')
            ->willReturn(56);

        $product
            ->expects($this->once())
            ->method('isVisibleInCatalog')
            ->willReturn(true);
        $product
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Test name');

        $this->request
            ->expects($this->at(0))
            ->method('getParam')
            ->with('product', null)
            ->willReturn(2);
        $this->request
            ->expects($this->at(1))
            ->method('getParam')
            ->with('id', null)
            ->willReturn(3);

        $this->productRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($product);

        $item
            ->expects($this->once())
            ->method('load')
            ->with(3)
            ->willReturnSelf();
        $item
            ->expects($this->once())
            ->method('__call')
            ->with('getSendquoteId')
            ->willReturn(12);

        $this->sendquoteProvider
            ->expects($this->once())
            ->method('getSendquote')
            ->with(12)
            ->willReturn($sendquote);

        $this->om
            ->expects($this->once())
            ->method('create')
            ->with(\Techspot\SendQuote\Model\Item::class)
            ->willReturn($item);

        $this->request
            ->expects($this->once())
            ->method('getParams')
            ->willReturn([]);

        $this->om
            ->expects($this->exactly(2))
            ->method('get')
            ->with(\Techspot\SendQuote\Helper\Data::class)
            ->willReturn($helper);

        $this->eventManager
            ->expects($this->once())
            ->method('dispatch')
            ->with('sendquote_update_item', ['sendquote' => $sendquote, 'product' => $product, 'item' => $item])
            ->willReturn(true);

        $this->messageManager
            ->expects($this->once())
            ->method('addSuccess')
            ->with('Test name has been updated in your Quotations.', null)
            ->willThrowException(new \Magento\Framework\Exception\LocalizedException(__('error-message')));
        $this->messageManager
            ->expects($this->once())
            ->method('addError')
            ->with('error-message', null)
            ->willReturn(true);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*', ['sendquote_id' => 56])
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->getController()->execute());
    }

    /**
     * Test execute add success critical exception
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecuteAddSuccessCriticalException()
    {
        $sendquote = $this->createMock(\Techspot\SendQuote\Model\Sendquote::class);
        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $item = $this->createMock(\Techspot\SendQuote\Model\Item::class);
        $helper = $this->createMock(\Techspot\SendQuote\Helper\Data::class);
        $logger = $this->createMock(\Magento\Framework\Logger\Monolog::class);
        $exception = new \Exception();

        $logger
            ->expects($this->once())
            ->method('critical')
            ->with($exception)
            ->willReturn(true);

        $helper
            ->expects($this->exactly(2))
            ->method('calculate')
            ->willReturn(true);

        $sendquote
            ->expects($this->once())
            ->method('getItem')
            ->with(3)
            ->willReturn($item);
        $sendquote
            ->expects($this->once())
            ->method('updateItem')
            ->with(3, new \Magento\Framework\DataObject([]))
            ->willReturnSelf();
        $sendquote
            ->expects($this->once())
            ->method('save')
            ->willReturn(null);
        $sendquote
            ->expects($this->once())
            ->method('getId')
            ->willReturn(56);

        $product
            ->expects($this->once())
            ->method('isVisibleInCatalog')
            ->willReturn(true);
        $product
            ->expects($this->once())
            ->method('getName')
            ->willReturn('Test name');

        $this->request
            ->expects($this->at(0))
            ->method('getParam')
            ->with('product', null)
            ->willReturn(2);
        $this->request
            ->expects($this->at(1))
            ->method('getParam')
            ->with('id', null)
            ->willReturn(3);

        $this->productRepository
            ->expects($this->once())
            ->method('getById')
            ->with(2)
            ->willReturn($product);

        $item
            ->expects($this->once())
            ->method('load')
            ->with(3)
            ->willReturnSelf();
        $item
            ->expects($this->once())
            ->method('__call')
            ->with('getSendquoteId')
            ->willReturn(12);

        $this->sendquoteProvider
            ->expects($this->once())
            ->method('getSendquote')
            ->with(12)
            ->willReturn($sendquote);

        $this->om
            ->expects($this->once())
            ->method('create')
            ->with(\Techspot\SendQuote\Model\Item::class)
            ->willReturn($item);

        $this->request
            ->expects($this->once())
            ->method('getParams')
            ->willReturn([]);

        $this->om
            ->expects($this->at(1))
            ->method('get')
            ->with(\Techspot\SendQuote\Helper\Data::class)
            ->willReturn($helper);
        $this->om
            ->expects($this->at(2))
            ->method('get')
            ->with(\Techspot\SendQuote\Helper\Data::class)
            ->willReturn($helper);
        $this->om
            ->expects($this->at(3))
            ->method('get')
            ->with(\Psr\Log\LoggerInterface::class)
            ->willReturn($logger);

        $this->eventManager
            ->expects($this->once())
            ->method('dispatch')
            ->with('sendquote_update_item', ['sendquote' => $sendquote, 'product' => $product, 'item' => $item])
            ->willReturn(true);

        $this->messageManager
            ->expects($this->once())
            ->method('addSuccess')
            ->with('Test name has been updated in your Quotations.', null)
            ->willThrowException($exception);
        $this->messageManager
            ->expects($this->once())
            ->method('addError')
            ->with('We can\'t update your Quotations right now.', null)
            ->willReturn(true);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*', ['sendquote_id' => 56])
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->getController()->execute());
    }
}
