<?php
/**
 *
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Index;

use Magento\Framework\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Session\Generic as SendquoteSession;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Layout as ResultLayout;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Send extends \Techspot\SendQuote\Controller\AbstractIndex
{
    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerHelperView;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Techspot\SendQuote\Model\Config
     */
    protected $_sendquoteConfig;

    /**
     * @var \Techspot\SendQuote\Controller\SendquoteProviderInterface
     */
    protected $sendquoteProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var SendquoteSession
     */
    protected $sendquoteSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Techspot\SendQuote\Controller\SendquoteProviderInterface $sendquoteProvider
     * @param \Techspot\SendQuote\Model\Config $sendquoteConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Customer\Helper\View $customerHelperView
     * @param SendquoteSession $sendquoteSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession,
        \Techspot\SendQuote\Controller\SendquoteProviderInterface $sendquoteProvider,
        \Techspot\SendQuote\Model\Config $sendquoteConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Customer\Helper\View $customerHelperView,
        SendquoteSession $sendquoteSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_customerSession = $customerSession;
        $this->sendquoteProvider = $sendquoteProvider;
        $this->_sendquoteConfig = $sendquoteConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_customerHelperView = $customerHelperView;
        $this->sendquoteSession = $sendquoteSession;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Send sendquote
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        
        $sendquote = $this->sendquoteProvider->getSendquote();
        if (!$sendquote) {
            throw new NotFoundException(__('Page not found.'));
        }

        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        $this->addLayoutHandles($resultLayout);
        $this->inlineTranslation->suspend();

        try {
            $customer = $this->_customerSession->getCustomerDataObject();
            $customerName = $this->_customerHelperView->getCustomerName($customer);

            $message = __('NEW QUOTATION NEED RESPONSE: %1', $sendquote->getId());
            $emails[] = $customer->getEmail();
            $sharingCode = $sendquote->getSharingCode();

            try {
                foreach ($emails as $email) {
                    $transport = $this->_transportBuilder->setTemplateIdentifier(
                        $this->scopeConfig->getValue(
                            'sendquote/email/email_request_template',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->storeManager->getStore()->getStoreId(),
                        ]
                    )->setTemplateVars(
                        [
                            'customer' => $customer,
                            'customerName' => $customerName,
                            'salable' => $sendquote->isSalable() ? 'yes' : '',
                            'items' => $this->getSendquoteItems($resultLayout),
                            //'viewOnSiteLink' => $this->_url->getUrl('*/shared/index', ['code' => $sharingCode]),
                            'message' => $message,
                            'store' => $this->storeManager->getStore(),
                        ]
                    )->setFrom(
                        $this->scopeConfig->getValue(
                            'sendquote/email/email_identity',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        )
                    )->addTo(
                        $email
                    )->getTransport();

                    $transport->sendMessage();

                   
                }
            } catch (\Exception $e) {
                
                $sendquote->save();
                throw $e;
            }
           
            $sendquote->save();

            $this->inlineTranslation->resume();

            $this->_eventManager->dispatch('sendquote_share', ['sendquote' => $sendquote]);
            $this->messageManager->addSuccess(__('Your quotation was requested. Wait for our team to return.'));
            $resultRedirect->setPath('*/quotations', ['sendquote_id' => $sendquote->getId()]);
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError($e->getMessage());
            $this->sendquoteSession->setSharingForm($this->getRequest()->getPostValue());
            $resultRedirect->setPath('*/*/quotations');
            return $resultRedirect;
        }
    }

    /**
     * Prepare to load additional email blocks
     *
     * Add 'sendquote_email_rss' layout handle.
     * Add 'sendquote_email_items' layout handle.
     *
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return void
     */
    protected function addLayoutHandles(ResultLayout $resultLayout)
    {
        if ($this->getRequest()->getParam('rss_url')) {
            $resultLayout->addHandle('sendquote_email_rss');
        }
        $resultLayout->addHandle('sendquote_email_items');
    }

    /**
     * Retrieve RSS link content (html)
     *
     * @param int $sendquoteId
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return mixed
     */
    protected function getRssLink($sendquoteId, ResultLayout $resultLayout)
    {
        if ($this->getRequest()->getParam('rss_url')) {
            return $resultLayout->getLayout()
                ->getBlock('sendquote.email.rss')
                ->setSendquoteId($sendquoteId)
                ->toHtml();
        }
    }

    /**
     * Retrieve sendquote items content (html)
     *
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return string
     */
    protected function getSendquoteItems(ResultLayout $resultLayout)
    {
        return $resultLayout->getLayout()
            ->getBlock('sendquote.request.email.items')
            ->toHtml();
    }
}
