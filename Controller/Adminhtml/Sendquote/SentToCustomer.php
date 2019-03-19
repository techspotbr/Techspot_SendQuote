<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Techspot\SendQuote\Controller\Adminhtml\Sendquote;

use Magento\Backend\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Layout as ResultLayout;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class SentToCustomer extends \Magento\Backend\App\Action
{

    protected $authSession;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Techspot_SendQuote::email';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

     /** @var PriceCurrencyInterface $priceCurrency */
     protected $priceCurrency;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param  \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Registry $registry,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->authSession = $authSession;
        $this->registry = $registry;
        $this->_transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context);
    }

    /**
     * Quotation information page
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $quotationId    = $this->getRequest()->getParam('sendquote_id');
        $quotation      = $this->quotationLoad($quotationId);
    
        $customer       = $this->getCustomer($quotation->getCustomerId());
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
        
        $email = $customer->getEmail();
        $sent = 0;
        
        try {
            $transport = $this->_transportBuilder->setTemplateIdentifier(
                $this->scopeConfig->getValue(
                    'sendquote/email/email_response_template',
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
                    'quotationNumber' => $quotationId,
                    'customerName' => $customer->getName(),
                    'items' => $this->getSendquoteItems($quotation),
                    'viewOnSiteLink' => $this->_url->getUrl('*/shared/index', ['code' => $quotation->getSharingCode()]),
                    'message' => $quotation->getDescription(),
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

            $sent++;
            $quotation->setSentCustomer($quotation->getSentCustomer() + $sent);
            $quotation->save();

            $this->_eventManager->dispatch('sendquote_request', ['sendquote' => $quotation]);
            $this->messageManager->addSuccess(__('Your quotation was requested. Wait for our team to return.'));
            $resultRedirect->setPath('*/*/index', ['sendquote_id' => $quotation->getId()]);
            return $resultRedirect;

        } catch (\Exception $e) {
            throw $e;
        }        
    }

    protected function quotationLoad($sendquoteId)
    {
        $sendquote = $this->_objectManager->create(\Techspot\SendQuote\Model\Sendquote::class)->load($sendquoteId);
        $this->registry->register('current_quotation', $sendquote);
        return $sendquote;
    }

    protected function getCustomer($customerId)
    {
        $customer = $this->_objectManager->create(\Magento\Customer\Model\Customer::class)->load($customerId);
        return $customer;   
    }

    /**
     * Retrieve sendquote items content (html)
     *
     * @param \Magento\Framework\View\Result\Layout $resultLayout
     * @return string
     */
    protected function getSendquoteItems($quotation)
    {
       $tableHtml = '<table>';
       $tableHtml.= '<td>'.__('Sku').'</td>';
       $tableHtml.= '<td>'.__('Name').'</td>';
       $tableHtml.= '<td>'.__('Description').'</td>';
       $tableHtml.= '<td>'.__('Price Quote').'</td>';
       $tableHtml.= '</tr>';
       foreach($quotation->getItemCollection() as $_item){
           $tableHtml.= '<tr>';
           $tableHtml.= '<td>'.$_item->getProduct()->getData('sku').'</td>';
           $tableHtml.= '<td>'.$_item->getProduct()->getData('name').'</td>';
           $tableHtml.= '<td>'.$_item->getDescription().'</td>';
           $tableHtml.= '<td>'.($_item->getQty()*1).'</td>';
           $tableHtml.= '<td>'.$this->getFormatedPrice($_item->getCustomPrice()).'</td>';
           $tableHtml.= '</tr>';
       }
       $tableHtml.= '</table>';
       return $tableHtml;
    }

    /**
     * Function getFormatedPrice
     *
     * @param float $price
     *
     * @return string
     */
    public function getFormatedPrice($amount)
    {
        return $this->priceCurrency->convertAndFormat($amount);
    }
}
