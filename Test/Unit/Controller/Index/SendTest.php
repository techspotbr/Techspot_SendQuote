<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Test\Unit\Controller\Index;

use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Data\Customer as CustomerData;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Session\Generic as SendquoteSession;
use Magento\Framework\Translate\Inline\StateInterface as TranslateInlineStateInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Layout;
use Magento\Framework\View\Result\Layout as ResultLayout;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Techspot\SendQuote\Controller\Index\Send;
use Techspot\SendQuote\Controller\SendquoteProviderInterface;
use Techspot\SendQuote\Model\Config as SendquoteConfig;
use Techspot\SendQuote\Model\Sendquote;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SendTest extends \PHPUnit\Framework\TestCase
{
    /** @var  Send |\PHPUnit_Framework_MockObject_MockObject */
    protected $model;

    /** @var  ActionContext |\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var  FormKeyValidator |\PHPUnit_Framework_MockObject_MockObject */
    protected $formKeyValidator;

    /** @var  CustomerSession |\PHPUnit_Framework_MockObject_MockObject */
    protected $customerSession;

    /** @var  SendquoteProviderInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $sendquoteProvider;

    /** @var  SendquoteConfig |\PHPUnit_Framework_MockObject_MockObject */
    protected $sendquoteConfig;

    /** @var  TransportBuilder |\PHPUnit_Framework_MockObject_MockObject */
    protected $transportBuilder;

    /** @var  TranslateInlineStateInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $inlineTranslation;

    /** @var  CustomerViewHelper |\PHPUnit_Framework_MockObject_MockObject */
    protected $customerViewHelper;

    /** @var  SendquoteSession |\PHPUnit_Framework_MockObject_MockObject */
    protected $sendquoteSession;

    /** @var  ScopeConfigInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfig;

    /** @var  Store |\PHPUnit_Framework_MockObject_MockObject */
    protected $store;

    /** @var  StoreManagerInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var  ResultFactory |\PHPUnit_Framework_MockObject_MockObject */
    protected $resultFactory;

    /** @var  ResultRedirect |\PHPUnit_Framework_MockObject_MockObject */
    protected $resultRedirect;

    /** @var  ResultLayout |\PHPUnit_Framework_MockObject_MockObject */
    protected $resultLayout;

    /** @var  Layout |\PHPUnit_Framework_MockObject_MockObject */
    protected $layout;

    /** @var  RequestInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var  Sendquote |\PHPUnit_Framework_MockObject_MockObject */
    protected $sendquote;

    /** @var  ManagerInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $messageManager;

    /** @var  CustomerData |\PHPUnit_Framework_MockObject_MockObject */
    protected $customerData;

    /** @var  UrlInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $url;

    /** @var  TransportInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $transport;

    /** @var  EventManagerInterface |\PHPUnit_Framework_MockObject_MockObject */
    protected $eventManager;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp()
    {
        $this->resultRedirect = $this->getMockBuilder(\Magento\Framework\Controller\Result\Redirect::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultLayout = $this->getMockBuilder(\Magento\Framework\View\Result\Layout::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resultFactory = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactory->expects($this->any())
            ->method('create')
            ->willReturnMap([
                [ResultFactory::TYPE_REDIRECT, [], $this->resultRedirect],
                [ResultFactory::TYPE_LAYOUT, [], $this->resultLayout],
            ]);

        $this->request = $this->getMockBuilder(\Magento\Framework\App\RequestInterface::class)
            ->setMethods([
                'getPost',
                'getPostValue',
            ])
            ->getMockForAbstractClass();

        $this->messageManager = $this->getMockBuilder(\Magento\Framework\Message\ManagerInterface::class)
            ->getMockForAbstractClass();

        $this->url = $this->getMockBuilder(\Magento\Framework\UrlInterface::class)
            ->getMockForAbstractClass();

        $this->eventManager = $this->getMockBuilder(\Magento\Framework\Event\ManagerInterface::class)
            ->getMockForAbstractClass();

        $this->context = $this->getMockBuilder(\Magento\Framework\App\Action\Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->context->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($this->resultFactory);
        $this->context->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManager);
        $this->context->expects($this->any())
            ->method('getUrl')
            ->willReturn($this->url);
        $this->context->expects($this->any())
            ->method('getEventManager')
            ->willReturn($this->eventManager);

        $this->formKeyValidator = $this->getMockBuilder(\Magento\Framework\Data\Form\FormKey\Validator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerSession = $this->getMockBuilder(\Magento\Customer\Model\Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sendquoteProvider = $this->getMockBuilder(\Techspot\SendQuote\Controller\SendquoteProviderInterface::class)
            ->getMockForAbstractClass();

        $this->sendquoteConfig = $this->getMockBuilder(\Techspot\SendQuote\Model\Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transportBuilder = $this->getMockBuilder(\Magento\Framework\Mail\Template\TransportBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inlineTranslation = $this->getMockBuilder(\Magento\Framework\Translate\Inline\StateInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerViewHelper = $this->getMockBuilder(\Magento\Customer\Helper\View::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sendquoteSession = $this->getMockBuilder(\Magento\Framework\Session\Generic::class)
            ->disableOriginalConstructor()
            ->setMethods(['setSharingForm'])
            ->getMock();

        $this->scopeConfig = $this->getMockBuilder(\Magento\Framework\App\Config\ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->store = $this->getMockBuilder(\Magento\Store\Model\Store::class)
            ->disableOriginalConstructor()
            ->setMethods(['getStoreId'])
            ->getMock();

        $this->storeManager = $this->getMockBuilder(\Magento\Store\Model\StoreManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($this->store);

        $this->sendquote = $this->getMockBuilder(\Techspot\SendQuote\Model\Sendquote::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getShared',
                'setShared',
                'getId',
                'getSharingCode',
                'save',
                'isSalable',
            ])
            ->getMock();

        $this->customerData = $this->getMockBuilder(\Magento\Customer\Model\Data\Customer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->layout = $this->getMockBuilder(\Magento\Framework\View\Layout::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getBlock',
                'setSendquoteId',
                'toHtml',
            ])
            ->getMock();

        $this->transport = $this->getMockBuilder(\Magento\Framework\Mail\TransportInterface::class)
            ->getMockForAbstractClass();

        $this->model = new Send(
            $this->context,
            $this->formKeyValidator,
            $this->customerSession,
            $this->sendquoteProvider,
            $this->sendquoteConfig,
            $this->transportBuilder,
            $this->inlineTranslation,
            $this->customerViewHelper,
            $this->sendquoteSession,
            $this->scopeConfig,
            $this->storeManager
        );
    }

    public function testExecuteNoFormKeyValidated()
    {
        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(false);

        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertEquals($this->resultRedirect, $this->model->execute());
    }

    /**
     * @expectedException \Magento\Framework\Exception\NotFoundException
     * @expectedExceptionMessage Page not found.
     */
    public function testExecuteNoSendquoteAvailable()
    {
        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(true);

        $this->sendquoteProvider->expects($this->once())
            ->method('getSendquote')
            ->willReturn(null);

        $this->model->execute();
    }

    /**
     * @param string $text
     * @param int $textLimit
     * @param string $emails
     * @param int $emailsLimit
     * @param int $shared
     * @param string $postValue
     * @param string $errorMessage
     *
     * @dataProvider dataProviderExecuteWithError
     */
    public function testExecuteWithError(
        $text,
        $textLimit,
        $emails,
        $emailsLimit,
        $shared,
        $postValue,
        $errorMessage
    ) {
        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(true);

        $this->sendquote->expects($this->once())
            ->method('getShared')
            ->willReturn($shared);

        $this->sendquoteProvider->expects($this->once())
            ->method('getSendquote')
            ->willReturn($this->sendquote);

        $this->sendquoteConfig->expects($this->once())
            ->method('getSharingEmailLimit')
            ->willReturn($emailsLimit);
        $this->sendquoteConfig->expects($this->once())
            ->method('getSharingTextLimit')
            ->willReturn($textLimit);

        $this->request->expects($this->exactly(2))
            ->method('getPost')
            ->willReturnMap([
                ['emails', $emails],
                ['message', $text],
            ]);
        $this->request->expects($this->once())
            ->method('getPostValue')
            ->willReturn($postValue);

        $this->messageManager->expects($this->once())
            ->method('addError')
            ->with($errorMessage)
            ->willReturnSelf();

        $this->sendquoteSession->expects($this->any())
            ->method('setSharingForm')
            ->with($postValue)
            ->willReturnSelf();

        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/share')
            ->willReturnSelf();

        $this->assertEquals($this->resultRedirect, $this->model->execute());
    }

    /**
     * 1. Text
     * 2. Text limit
     * 3. Emails
     * 4. Emails limit
     * 5. Shared sendquotes counter
     * 6. POST value
     * 7. Error message (RESULT)
     *
     * @return array
     */
    public function dataProviderExecuteWithError()
    {
        return [
            ['test text', 1, 'user1@example.com', 1, 0, '', 'Message length must not exceed 1 symbols'],
            ['test text', 100, null, 1, 0, '', 'Please enter an email address.'],
            ['test text', 100, '', 1, 0, '', 'Please enter an email address.'],
            ['test text', 100, 'user1@example.com', 1, 1, '', 'This quote list can be shared 0 more times.'],
            [
                'test text',
                100,
                'u1@example.com, u2@example.com',
                3,
                2,
                '',
                'This quote list can be shared 1 more times.'
            ],
            ['test text', 100, 'wrongEmailAddress', 1, 0, '', 'Please enter a valid email address.'],
            ['test text', 100, 'user1@example.com, wrongEmailAddress', 2, 0, '', 'Please enter a valid email address.'],
            ['test text', 100, 'wrongEmailAddress, user2@example.com', 2, 0, '', 'Please enter a valid email address.'],
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecuteWithException()
    {
        $text = 'test text';
        $textLimit = 100;
        $emails = 'user1@example.com';
        $emailsLimit = 1;
        $shared = 0;
        $customerName = 'user1 user1';
        $sendquoteId = 1;
        $rssLink = 'rss link';
        $sharingCode = 'sharing code';
        $exceptionMessage = 'test exception message';
        $postValue = '';

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(true);

        $this->sendquote->expects($this->exactly(2))
            ->method('getShared')
            ->willReturn($shared);
        $this->sendquote->expects($this->once())
            ->method('setShared')
            ->with($shared)
            ->willReturnSelf();
        $this->sendquote->expects($this->once())
            ->method('getId')
            ->willReturn($sendquoteId);
        $this->sendquote->expects($this->once())
            ->method('getSharingCode')
            ->willReturn($sharingCode);
        $this->sendquote->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $this->sendquoteProvider->expects($this->once())
            ->method('getSendquote')
            ->willReturn($this->sendquote);

        $this->sendquoteConfig->expects($this->once())
            ->method('getSharingEmailLimit')
            ->willReturn($emailsLimit);
        $this->sendquoteConfig->expects($this->once())
            ->method('getSharingTextLimit')
            ->willReturn($textLimit);

        $this->request->expects($this->exactly(2))
            ->method('getPost')
            ->willReturnMap([
                ['emails', $emails],
                ['message', $text],
            ]);
        $this->request->expects($this->exactly(2))
            ->method('getParam')
            ->with('rss_url')
            ->willReturn(true);
        $this->request->expects($this->once())
            ->method('getPostValue')
            ->willReturn($postValue);

        $this->layout->expects($this->once())
            ->method('getBlock')
            ->with('sendquote.email.rss')
            ->willReturnSelf();
        $this->layout->expects($this->once())
            ->method('setSendquoteId')
            ->with($sendquoteId)
            ->willReturnSelf();
        $this->layout->expects($this->once())
            ->method('toHtml')
            ->willReturn($rssLink);

        $this->resultLayout->expects($this->exactly(2))
            ->method('addHandle')
            ->willReturnMap([
                ['sendquote_email_rss', null],
                ['sendquote_email_items', null],
            ]);
        $this->resultLayout->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->layout);

        $this->inlineTranslation->expects($this->once())
            ->method('suspend')
            ->willReturnSelf();
        $this->inlineTranslation->expects($this->once())
            ->method('resume')
            ->willReturnSelf();

        $this->customerSession->expects($this->once())
            ->method('getCustomerDataObject')
            ->willReturn($this->customerData);

        $this->customerViewHelper->expects($this->once())
            ->method('getCustomerName')
            ->with($this->customerData)
            ->willReturn($customerName);

        // Throw Exception
        $this->transportBuilder->expects($this->once())
            ->method('setTemplateIdentifier')
            ->willThrowException(new \Exception($exceptionMessage));

        $this->messageManager->expects($this->once())
            ->method('addError')
            ->with($exceptionMessage)
            ->willReturnSelf();

        $this->sendquoteSession->expects($this->any())
            ->method('setSharingForm')
            ->with($postValue)
            ->willReturnSelf();

        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*/share')
            ->willReturnSelf();

        $this->assertEquals($this->resultRedirect, $this->model->execute());
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecute()
    {
        $text = 'text';
        $textLimit = 100;
        $emails = 'user1@example.com';
        $emailsLimit = 1;
        $shared = 0;
        $customerName = 'user1 user1';
        $sendquoteId = 1;
        $sharingCode = 'sharing code';
        $templateIdentifier = 'template identifier';
        $storeId = 1;
        $viewOnSiteLink = 'view on site link';
        $from = 'user0@example.com';

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->request)
            ->willReturn(true);

        $this->sendquote->expects($this->exactly(2))
            ->method('getShared')
            ->willReturn($shared);
        $this->sendquote->expects($this->once())
            ->method('setShared')
            ->with(++$shared)
            ->willReturnSelf();
        $this->sendquote->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($sendquoteId);
        $this->sendquote->expects($this->once())
            ->method('getSharingCode')
            ->willReturn($sharingCode);
        $this->sendquote->expects($this->once())
            ->method('save')
            ->willReturnSelf();
        $this->sendquote->expects($this->once())
            ->method('isSalable')
            ->willReturn(true);

        $this->sendquoteProvider->expects($this->once())
            ->method('getSendquote')
            ->willReturn($this->sendquote);

        $this->sendquoteConfig->expects($this->once())
            ->method('getSharingEmailLimit')
            ->willReturn($emailsLimit);
        $this->sendquoteConfig->expects($this->once())
            ->method('getSharingTextLimit')
            ->willReturn($textLimit);

        $this->request->expects($this->exactly(2))
            ->method('getPost')
            ->willReturnMap([
                ['emails', $emails],
                ['message', $text],
            ]);
        $this->request->expects($this->exactly(2))
            ->method('getParam')
            ->with('rss_url')
            ->willReturn(true);

        $this->layout->expects($this->exactly(2))
            ->method('getBlock')
            ->willReturnMap([
                ['sendquote.email.rss', $this->layout],
                ['sendquote.email.items', $this->layout],
            ]);

        $this->layout->expects($this->once())
            ->method('setSendquoteId')
            ->with($sendquoteId)
            ->willReturnSelf();
        $this->layout->expects($this->exactly(2))
            ->method('toHtml')
            ->willReturn($text);

        $this->resultLayout->expects($this->exactly(2))
            ->method('addHandle')
            ->willReturnMap([
                ['sendquote_email_rss', null],
                ['sendquote_email_items', null],
            ]);
        $this->resultLayout->expects($this->exactly(2))
            ->method('getLayout')
            ->willReturn($this->layout);

        $this->inlineTranslation->expects($this->once())
            ->method('suspend')
            ->willReturnSelf();
        $this->inlineTranslation->expects($this->once())
            ->method('resume')
            ->willReturnSelf();

        $this->customerSession->expects($this->once())
            ->method('getCustomerDataObject')
            ->willReturn($this->customerData);

        $this->customerViewHelper->expects($this->once())
            ->method('getCustomerName')
            ->with($this->customerData)
            ->willReturn($customerName);

        $this->scopeConfig->expects($this->exactly(2))
            ->method('getValue')
            ->willReturnMap([
                ['sendquote/email/email_template', ScopeInterface::SCOPE_STORE, null, $templateIdentifier],
                ['sendquote/email/email_identity', ScopeInterface::SCOPE_STORE, null, $from],
            ]);

        $this->store->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $this->url->expects($this->once())
            ->method('getUrl')
            ->with('*/shared/index', ['code' => $sharingCode])
            ->willReturn($viewOnSiteLink);

        $this->transportBuilder->expects($this->once())
            ->method('setTemplateIdentifier')
            ->with($templateIdentifier)
            ->willReturnSelf();
        $this->transportBuilder->expects($this->once())
            ->method('setTemplateOptions')
            ->with([
                'area' => Area::AREA_FRONTEND,
                'store' => $storeId,
            ])
            ->willReturnSelf();
        $this->transportBuilder->expects($this->once())
            ->method('setTemplateVars')
            ->with([
                'customer' => $this->customerData,
                'customerName' => $customerName,
                'salable' => 'yes',
                'items' => $text,
                'viewOnSiteLink' => $viewOnSiteLink,
                'message' => $text . $text,
                'store' => $this->store,
            ])
            ->willReturnSelf();
        $this->transportBuilder->expects($this->once())
            ->method('setFrom')
            ->with($from)
            ->willReturnSelf();
        $this->transportBuilder->expects($this->once())
            ->method('addTo')
            ->with($emails)
            ->willReturnSelf();
        $this->transportBuilder->expects($this->once())
            ->method('getTransport')
            ->willReturn($this->transport);

        $this->transport->expects($this->once())
            ->method('sendMessage')
            ->willReturnSelf();

        $this->eventManager->expects($this->once())
            ->method('dispatch')
            ->with('sendquote_share', ['sendquote' => $this->sendquote])
            ->willReturnSelf();

        $this->messageManager->expects($this->once())
            ->method('addSuccess')
            ->with(__('Your quotation has been shared.'))
            ->willReturnSelf();

        $this->resultRedirect->expects($this->once())
            ->method('setPath')
            ->with('*/*', ['sendquote_id' => $sendquoteId])
            ->willReturnSelf();

        $this->assertEquals($this->resultRedirect, $this->model->execute());
    }
}
