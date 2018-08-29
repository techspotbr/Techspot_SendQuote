<?php
/**
 * Copyright © TechSpot Web Agency. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Sendquote block customer quotations
 *
 * @author     Bruno Monteiro <babumsouza1@gmail.com>
 */
namespace Techspot\SendQuote\Block\Customer;

use \Magento\Framework\App\ObjectManager;
use \Techspot\SendQuote\Model\ResourceModel\Sendquote\CollectionFactoryInterface;

class Quotations extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Techspot\SendQuote\Model\ResourceModel\Sendquote\Collection
     */
    protected $quotations;
    
    /**
     * @var \Techspot\SendQuote\Model\ResourceModel\Sendquote\CollectionFactory
     */
    protected $_quotationCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var CollectionFactoryInterface
     */
    private $quotationCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Techspot\SendQuote\Model\ResourceModel\Sendquote\CollectionFactory $quotationCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Techspot\SendQuote\Model\ResourceModel\Sendquote\CollectionFactory $quotationCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_quotationCollectionFactory = $quotationCollectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Quotations'));
    }

    /**
     * @return CollectionFactoryInterface
     *
     */
    private function getQuotationCollectionFactory()
    {
        if ($this->quotationCollectionFactory === null) {
            $this->quotationCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->quotationCollectionFactory;
    }

    /**
     * @return bool|\Techspot\SendQuote\Model\ResourceModel\Sendquote\Collection
     */
    public function getQuotations()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->quotations) {
            $this->quotations = $this->getQuotationCollectionFactory()->create($customerId)->addFieldToSelect(
                '*'
            )->setOrder(
                'updated_at',
                'desc'
            );
        }
        return $this->quotations;
    }

    /**
     * Get View url
     * 
     * @param object $quotation
     * 
     * @return string
     */
    public function getViewUrl($quotation)
    {
        return $this->getUrl('sendquote/*/view', ['id' => $quotation->getSendquoteId()]);
    }

}